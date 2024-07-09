<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Controller;

use App\Application\Security\Form\AuthorizationForm;
use App\Application\Security\Form\RegistrationType;
use App\Application\Security\Form\RequestResetPasswordType;
use App\Application\Security\Form\SetNewPasswordType;
use App\Domain\Shared\CommandBusInterface;
use App\Domain\User\Entity\User;
use App\Domain\User\Repository\UserRepository;
use App\Infrastructure\Mailer\Command\BaseSendEmailCommand;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthorizationController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/login', name: 'app_authorization')]
    public function __invoke(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form = $this->createForm(AuthorizationForm::class);

        return $this->render('security/authorization.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, CommandBusInterface $commandBus, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));
            $user->setActivationKey($this->generateHash(new DateTime(), $user->getEmail()));
            $entityManager->persist($user);
            $entityManager->flush();


            try {
                $commandBus->handle(
                    new BaseSendEmailCommand(
                        $user->getEmail(),
                        '[no-reply] Time Tracker Jira - Link aktywacyjny',
                        'email/account.create.html.twig',
                        ['code' => $user->getActivationKey()],
                        false,
                        ''
                    )
                );
            } catch (\Exception $e) {
                //TODO: Refactor this to the async task to improve timing of registration.
            }
            $this->addFlash('success', 'Link aktywacyjny został wysłany na podany adres email');

            return $this->redirectToRoute('app_authorization');
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    #[Route('/activation/{code}', name: 'app_auth_activation', methods: ['GET'])]
    public function activation(string $code, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->userRepository->findOneBy(['activationKey' => $code]);

        if (! $user) {
            $this->addFlash('error', 'Podany link aktywacyjny jest nie poprawny lub też nie istnieje');

            return $this->redirectToRoute('app_authorization');
        }

        if ($user->isActive()) {
            $this->addFlash('info', 'Użytkownik jest już aktywny');

            return $this->redirectToRoute('app_authorization');
        }

        $user->setActive(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Aktywacja się powiodła');

        return $this->redirectToRoute('app_authorization');
    }

    #[Route('/reset-password', name: 'app_auth_request_reset_password')]
    public function resetPassword(Request $request, EntityManagerInterface $entityManager, CommandBusInterface $commandBus): Response
    {
        $form = $this->createForm(RequestResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Jeżeli konto istnieje w serwisie, to na podany adres email został przesłany link do resetu hasła');

            $user = $this->userRepository->findOneBy(['email' => $form->get('_username')->getData() ?? 'null@null.com']);
            if (! $user) {
                return $this->redirectToRoute('app_authorization');
            }

            $code = $this->generateHash(new DateTime(), $user->getEmail());

            $user->setActivationKey($code);
            $entityManager->persist($user);
            $entityManager->flush();

            $resetLink = $this->generateUrl('app_auth_reset_password', ['code' => $code], UrlGeneratorInterface::ABSOLUTE_URL);

            $commandBus->handle(
                new BaseSendEmailCommand(
                    $user->getEmail(),
                    '[no-reply] Time Tracker Jira - Resetowanie hasła',
                    'email/reset-password.html.twig',
                    ['user' => $user, 'resetPasswordUrl' => $resetLink],
                    false,
                    ''
                )
            );

            return $this->redirectToRoute('app_authorization');
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $this->render('security/reset_password.page.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    #[Route('/reset-password/{code}', name: 'app_auth_reset_password')]
    public function resetPasswordForm(string $code, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManagers): Response
    {
        $form = $this->createForm(SetNewPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->userRepository->findOneBy([
                'activationKey' => $code,
                'email' => $form->get('_username')->getData(),
            ]);

            if (! $user) {
                $this->addFlash('error', 'Przepraszamy, ale podany kod aktywacyjny jest niepoprawny.');

                return $this->redirectToRoute('app_authorization');
            }

            $newPassword = $form->get('password')->getData();
            $encodedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($encodedPassword);
            $user->setActivationKey(null);

            $entityManagers->persist($user);
            $entityManagers->flush();

            $this->addFlash('success', 'Twoje hasło zostało pomyślnie zresetowane. Możesz teraz się zalogować.');

            return $this->redirectToRoute('app_authorization');
        }

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        return $this->render('security/set-new-password.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    private function generateHash(DateTime $date, string $email): string
    {
        return "TTJ" . $date->format('Ymd') . strtoupper(md5($date->format('Y-m-d H:i:s') . $email));
    }
}
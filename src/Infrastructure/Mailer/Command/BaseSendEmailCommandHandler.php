<?php

declare(strict_types=1);

namespace App\Infrastructure\Mailer\Command;

use App\Application\Shared\Command\CommandHandlerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;

readonly class BaseSendEmailCommandHandler implements CommandHandlerInterface
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(BaseSendEmailCommand $command): void
    {
        $email = (new TemplatedEmail())
            ->from($_ENV['EMAIL_SENDER_FROM'])
            ->to($command->getSendTo())
            ->subject($command->getSubject())
            ->htmlTemplate($command->getHtmlTemplate())
            ->context($command->getParams() ?? []);

        if ($command->hasAttachment()) {
            $email->addPart(
                new DataPart($command->getAttachment(), $command->getAttachmentName(), 'application/pdf')
            );
        }

        $this->mailer->send($email);
    }
}
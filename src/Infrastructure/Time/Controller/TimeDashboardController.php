<?php

declare(strict_types=1);

namespace App\Infrastructure\Time\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimeDashboardController extends AbstractController
{
    #[Route('/time-dashboard', name: 'time-dashboard')]
    public function __invoke(): Response
    {
        return $this->render('time-dashboard/time-dashboard.html.twig', []);
    }
}
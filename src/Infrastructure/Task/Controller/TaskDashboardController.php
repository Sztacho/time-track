<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskDashboardController extends AbstractController
{
    #[Route('/tasks', name: 'app_tasks_list')]
    public function __invoke(): Response
    {
        return $this->render('task/list.html.twig');
    }
}
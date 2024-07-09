<?php

declare(strict_types=1);

namespace App\Infrastructure\Task\Controller;

use App\Domain\Task\Entity\Task;
use App\Domain\Time\Entity\TimeEntry;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/api/tasks', name: 'api_tasks', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        $tasks = $entityManager->getRepository(Task::class)->findBy(['user' => $this->getUser()]);
        $timeEntries = $entityManager->getRepository(TimeEntry::class)->findBy(['user' => $this->getUser()]);

        $taskData = [];
        foreach ($tasks as $task) {
            $totalTimeSpent = array_reduce($timeEntries, function ($carry, $entry) use ($task) {
                if ($entry->getTask()->getId() === $task->getId()) {
                    return $carry + $entry->getDuration();
                }

                return $carry;
            }, 0);

            $taskData[] = [
                'id' => $task->getId(),
                'title' => $task->getTitle(),
                'description' => $task->getDescription(),
                'status' => $task->getStatus(),
                'timeSpent' => $totalTimeSpent,
            ];
        }

        return $this->json($taskData);
    }

    #[Route('/api/tasks', name: 'api_task_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task($this->getUser(), $data['title'], $data['status']);
        $task->setDescription($data['description']);
        $task->setCreatedAt(new DateTimeImmutable());
        $task->setUpdatedAt(new DateTimeImmutable());

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->json($task);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (! $task || $task->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Task not found or access denied'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $task->setTitle($data['title']);
        $task->setDescription($data['description']);
        $task->setStatus($data['status']);
        $task->setUpdatedAt(new DateTimeImmutable());

        $entityManager->flush();

        return $this->json($task);
    }

    #[Route('/api/tasks/{id}', name: 'api_task_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (! $task || $task->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Task not found or access denied'], 404);
        }

        $entityManager->remove($task);
        $entityManager->flush();

        return $this->json(['message' => 'Task deleted']);
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Time\Controller;

use App\Domain\Task\Entity\Task;
use App\Domain\Time\Entity\TimeEntry;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TimeEntryController extends AbstractController
{
    #[Route('/api/time-entries', name: 'api_time_entries', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $taskId = $request->query->get('task_id');
        $date = $request->query->get('date');
        $criteria = ['user' => $this->getUser()];

        if ($taskId) {
            $criteria['task'] = $entityManager->getRepository(Task::class)->find($taskId);
        }

        if ($date) {
            $startOfDay = (new \DateTimeImmutable($date))->setTime(0, 0, 0);
            $endOfDay = $startOfDay->modify('+1 day');
            $criteria['startTime'] = ['>=', $startOfDay];
            $criteria['endTime'] = ['<', $endOfDay];
        }

        $timeEntries = $entityManager->getRepository(TimeEntry::class)->findByCriteria($criteria);

        return $this->json($timeEntries);
    }

    #[Route('/api/time-entries', name: 'api_time_entry_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = $entityManager->getRepository(Task::class)->find($data['task_id']);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], 404);
        }

        $startTime = new DateTimeImmutable();
        $timeEntry = new TimeEntry($task, $this->getUser(), $startTime, $startTime);
        $entityManager->persist($timeEntry);
        $entityManager->flush();

        return $this->json($timeEntry);
    }

    #[Route('/api/time-entries/{id}', name: 'api_time_entry_update', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $timeEntry = $entityManager->getRepository(TimeEntry::class)->find($id);

        if (!$timeEntry || $timeEntry->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Time entry not found or access denied'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['end_time'])) {
            $endTime = new DateTimeImmutable($data['end_time']);
            $timeEntry->setEndTime($endTime);
            $timeEntry->setDuration($endTime->getTimestamp() - $timeEntry->getStartTime()->getTimestamp());
        }

        if (isset($data['description'])) {
            $timeEntry->setDescription($data['description']);
        }

        $entityManager->flush();

        return $this->json($timeEntry);
    }

    #[Route('/api/time-entries/{id}', name: 'api_time_entry_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $timeEntry = $entityManager->getRepository(TimeEntry::class)->find($id);

        if (!$timeEntry || $timeEntry->getUser() !== $this->getUser()) {
            return $this->json(['error' => 'Time entry not found or access denied'], 404);
        }

        $entityManager->remove($timeEntry);
        $entityManager->flush();

        return $this->json(['message' => 'Time entry deleted']);
    }
}

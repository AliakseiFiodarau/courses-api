<?php

namespace App\Controller;

use App\Entity\Lecture;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LectureController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route(
        '/lecture/{id}',
        methods: ['GET'],
        name: 'lecture_show'
    )]
    public function show(int $id): JsonResponse
    {
        $repository = $this->em->getRepository(Lecture::class);
        $lecture = $repository->find($id);

        if (!$lecture) {
            return $this->json([
                'error' => 'No entity found for id ' . $id
            ]);
        }

        return $this->json([
            'lecture' => $lecture,
        ]);
    }

    #[Route(
        '/lecture',
        methods: ['GET'],
        name: 'lecture'
    )]
    public function index(): JsonResponse
    {
        $repository = $this->em->getRepository(Lecture::class);
        $lectures = $repository->findAll();

        return $this->json([
            'lectures' => $lectures,
        ]);
    }
}

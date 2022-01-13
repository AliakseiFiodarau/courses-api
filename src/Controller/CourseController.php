<?php

namespace App\Controller;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route(
        '/course/{id}',
        methods: ['GET'],
        name: 'course_show
            ')]
    public function show(int $id): JsonResponse
    {
        $repository = $this->em->getRepository(Course::class);
        $course = $repository->find($id);

        if (!$course) {
            return $this->json([
                'error' => 'No entity found for id ' . $id
            ]);
        }

        return $this->json([
            'course' => $course,
        ]);
    }


    #[Route(
        '/course',
        methods: ['GET'],
        name: 'course'
    )]
    public function index(): JsonResponse
    {
        $repository = $this->em->getRepository(Course::class);
        $courses = $repository->findAll();

        return $this->json([
            'courses' => $courses,
        ]);
    }
}

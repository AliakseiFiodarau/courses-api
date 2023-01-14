<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseFormType;
use App\Repository\CourseRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CourseController extends AbstractController
{
    public function __construct(
        private readonly CourseRepository $repository
    ) {}

    #[Route(
        '/course/{id}',
        name: 'course_show',
        methods: ['GET']
    )]
    public function show(int $id): JsonResponse
    {
        $course = $this->repository->find($id);

        if ($course) {
            return $this->json([
                'course' => $course,
            ]);
        }

        return $this->json([
            'message' => 'No entity found for id ' . $id
        ]);
    }

    #[Route(
        '/course',
        name: 'course',
        methods: ['GET']
    )]
    public function index(): JsonResponse
    {
        $courses = $this->repository->findAll();

        return $this->json([
            'courses' => $courses,
        ]);
    }

    #[Route(
        '/course/create',
        name: 'course_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $course = new Course;

        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm(CourseFormType::class, $course);
        $form->submit($data);

        if ($form->isValid()) {
            $courseName = $form->getData()->getName();
            $course->setName($courseName);
            $course->setCreatedAt(new DateTimeImmutable('now'));

            $this->repository->save($course, true);

            $courseId = $course->getId();

            return $this->json([
                'message' => 'Course resource with id ' . $courseId . ' created'
            ]);
        }

        $errors = $form->getErrors()->__toString();

        return $this->json([
            'message' => 'Unable to create resource. ' . $errors
        ]);
    }

    #[Route(
        '/course/update/{id}',
        name: 'course_update',
        methods: ['POST']
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $course = $this->repository->find($id);

        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm(CourseFormType::class, $course);
        $form->submit($data);

        if ($form->isValid()) {
            $courseName = $form->getData()->getName();
            $course->setName($courseName);

            $this->repository->save($course, true);

            return $this->json([
                'message' => 'Resource with id ' . $id . ' has been updated'
            ]);
        }

        $errors = $form->getErrors()->__toString();

        return $this->json([
            'message' => 'Unable to update resource. ' . $errors
        ]);
    }

    #[Route(
        '/course/delete/{id}',
        name: 'course_delete',
        methods: ['DELETE']
    )]
    public function delete(int $id): JsonResponse
    {
        $course = $this->repository->find($id);

        if ($course) {
            $this->repository->remove($course, true);

            return $this->json([
                'message' => 'Resource with id ' . $id . ' has been deleted'
            ]);
        }

        return $this->json([
            'message' => 'No entity found for id ' . $id
        ]);
    }
}

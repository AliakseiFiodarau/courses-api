<?php

namespace App\Controller;

use App\Entity\Lecture;
use App\Form\LectureType;
use App\Repository\LectureRepository;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LectureController extends AbstractController
{
    public function __construct(
        private readonly LectureRepository $repository
    ) {}

    #[Route(
        '/lecture/{id}',
        name: 'lecture_show',
        methods: ['GET']
    )]
    public function show(int $id): JsonResponse
    {
        $lecture = $this->repository->find($id);

        if ($lecture) {
            return $this->json([
                'lecture' => $lecture,
            ]);
        }

        return $this->json([
            'message' => 'No entity found for id ' . $id
        ]);
    }

    #[Route(
        '/lecture',
        name: 'lecture',
        methods: ['GET']
    )]
    public function index(): JsonResponse
    {
        $lectures = $this->repository->findAll();

        return $this->json([
            'lectures' => $lectures,
        ]);
    }

    #[Route(
        '/lecture/create',
        name: 'lecture_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        $lecture = new Lecture;

        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm(LectureType::class, $lecture);
        $form->submit($data);

        if ($form->isValid()) {
            $lectureName = $form->getData()->getName();
            $lecture->setName($lectureName);
            $blogId = $form->getData()->getBlogId();
            $lecture->setBlogId($blogId);
            $lecture->setCreatedAt(new DateTimeImmutable('now'));

            $this->repository->save($lecture, true);

            $lectureId = $lecture->getId();

            return $this->json([
                'message' => 'Course resource with id ' . $lectureId . ' created'
            ]);
        }

        $errors = $form->getErrors()->__toString();

        return $this->json([
            'message' => 'Unable to update resource. ' . $errors
        ]);
    }

    #[Route(
        '/lecture/update/{id}',
        name: 'lecture_update',
        methods: ['POST']
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $lecture = $this->repository->find($id);

        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm(LectureType::class, $lecture);
        $form->submit($data);

        if ($form->isValid()) {
            $lectureName = $form->getData()->getName();
            $lecture->setName($lectureName);
            $blogId = $form->getData()->getBlogId();
            $lecture->setBlogId($blogId);

            $this->repository->save($lecture, true);

            return $this->json([
                'message' => 'Course resource with id ' . $id . ' created'
            ]);
        }

        $form = $this->createForm(LectureType::class, $lecture);
        $form->submit($data);

        $errors = $form->getErrors()->__toString();

        return $this->json([
            'message' => 'Unable to create resource. ' . $errors
        ]);
    }

    #[Route(
        '/lecture/delete/{id}',
        name: 'lecture_delete',
        methods: ['DELETE']
    )]
    public function delete(int $id): JsonResponse
    {
        $lecture = $this->repository->find($id);
        if ($lecture) {
            $this->repository->remove($lecture, true);

            return $this->json([
                'message' => 'Resource with id ' . $id . ' has been deleted'
            ]);
        }

        return $this->json([
            'message' => 'No entity found for id ' . $id
        ]);
    }

    #[Route(
        '/lecture/course/{id}',
        name: 'lecture_by_course',
        methods: ['GET']
    )]
    public function getByCourse(int $id): JsonResponse
    {
        $lectures = $this->repository->findBy(['blogId' => $id]);
        if ($lectures) {
            return $this->json([
                'lectures' => $lectures,
            ]);
        }

        return $this->json([
            'message' => 'No lectures found for course with id ' . $id
        ]);
    }
}

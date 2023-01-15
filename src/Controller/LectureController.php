<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Lecture;
use App\Form\LectureType;
use App\Repository\LectureRepository;
use App\Service\ResponsePaginator;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class LectureController extends AbstractCourseController
{
    /**
     * String constants for entity name and course id.
     */
    private const COURSE_ENTITY_NAME = 'lecture';
    private const BLOG_ID_PROPERTY_NAME = 'blogId';

    /**
     * LectureController constructor.
     *
     * @param LectureRepository $repository
     * @param ResponsePaginator $paginator
     */
    public function __construct(
        private           readonly LectureRepository $repository,
        ResponsePaginator $paginator
    ) {
        parent::__construct($paginator);
    }

    /**
     * Showing a lecture.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route(
        '/lecture/{id}',
        name: 'lecture_show',
        methods: ['GET']
    )]
    public function show(int $id): JsonResponse
    {
        return $this->showEntity(
            $this->repository,
            $id,
            self::COURSE_ENTITY_NAME
        );
    }

    /**
     * Lectures listing.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(
        '/lecture',
        name: 'lecture',
        methods: ['GET']
    )]
    public function index(Request $request): JsonResponse
    {
        return $this->indexEntity($this->repository, $request);
    }

    /**
     * Get list of lectures by course id.
     *
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(
        '/lecture/course/{id}',
        name: 'lecture_by_course',
        methods: ['GET']
    )]
    public function getByCourse(int $id, Request $request): JsonResponse
    {
        return $this->getByProperty(
            $this->repository,
            $id,
            self::BLOG_ID_PROPERTY_NAME,
            $request,
            self::COURSE_ENTITY_NAME
        );
    }

    /**
     * Creating a lecture.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route(
        '/lecture/create',
        name: 'lecture_create',
        methods: ['POST']
    )]
    public function create(Request $request): JsonResponse
    {
        return $this->createOrUpdate(
            $request,
            self::ENTITY_CREATED,
        );
    }

    /**
     * Updating a lecture.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    #[Route(
        '/lecture/update/{id}',
        name: 'lecture_update',
        methods: ['POST']
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->createOrUpdate(
            $request,
            self::ENTITY_UPDATED,
            $id
        );
    }

    /**
     * Deleting a lecture.
     *
     * @param int $id
     * @return JsonResponse
     */
    #[Route(
        '/lecture/delete/{id}',
        name: 'lecture_delete',
        methods: ['DELETE']
    )]
    public function delete(int $id): JsonResponse
    {
        return $this->deleteEntity(
            $this->repository,
            $id,
            self::COURSE_ENTITY_NAME
        );
    }

    /**
     * Updating a lecture if it exists, otherwise creating it.
     *
     * @param Request $request
     * @param string $createdOrUpdated
     * @param int|null $id
     * @return JsonResponse
     */
    private function createOrUpdate(
        Request $request,
        string  $createdOrUpdated,
        int     $id = null
    ): JsonResponse {
        $lecture = $id ? $this->repository->find($id) : new Lecture;

        if ($lecture === null) {
            return $this->json([
                'No '. self::COURSE_ENTITY_NAME. ' found for id ' . $id
            ]);
        }

        if ($id === null) {
            $lecture->setCreatedAt(new DateTimeImmutable('now'));
        }

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
                'message' => self::COURSE_ENTITY_NAME . " resource with id $id has been $createdOrUpdated."
            ]);
        }

        $errors = $form->getErrors()->__toString();

        if (empty($errors)) {
            return $this->json([
                'message' => self::COURSE_ENTITY_NAME . " resource hasn't been $createdOrUpdated. Attributes are invalid."
            ]);
        }

        return $this->json([
            'message' => self::COURSE_ENTITY_NAME . " resource hasn't been $createdOrUpdated. $errors"
        ]);
    }
}

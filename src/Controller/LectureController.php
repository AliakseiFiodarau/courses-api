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
use Symfony\Component\HttpFoundation\Response;

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
     * @return Response
     */
    #[Route(
        '/api/lecture/{id}',
        name: 'lecture_show',
        methods: ['GET']
    )]
    public function show(int $id): Response
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
     * @return Response
     */
    #[Route(
        '/api/lecture',
        name: 'lecture',
        methods: ['GET']
    )]
    public function index(Request $request): Response
    {
        return $this->indexEntity($this->repository, $request);
    }

    /**
     * Get list of lectures by course id.
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    #[Route(
        '/api/lecture/course/{id}',
        name: 'lecture_by_course',
        methods: ['GET']
    )]
    public function getByCourse(int $id, Request $request): Response
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
     * @return Response
     */
    #[Route(
        '/api/lecture/create',
        name: 'lecture_create',
        methods: ['POST']
    )]
    public function create(Request $request): Response
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
     * @return Response
     */
    #[Route(
        '/api/lecture/update/{id}',
        name: 'lecture_update',
        methods: ['POST']
    )]
    public function update(Request $request, int $id): Response
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
     * @return Response
     */
    #[Route(
        '/api/lecture/delete/{id}',
        name: 'lecture_delete',
        methods: ['DELETE']
    )]
    public function delete(int $id): Response
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
     * @return Response
     */
    private function createOrUpdate(
        Request $request,
        string  $createdOrUpdated,
        int     $id = null
    ): Response {
        $lecture = $id ? $this->repository->find($id) : new Lecture;

        if ($lecture === null) {
            return $this->yaml([
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

            $id = $id ?: $lecture->getId();

            return $this->yaml([
                'message' => self::COURSE_ENTITY_NAME . " resource with id $id has been $createdOrUpdated."
            ]);
        }

        $errors = $form->getErrors()->__toString();

        if (empty($errors)) {
            return $this->yaml([
                'message' => self::COURSE_ENTITY_NAME . " resource hasn't been $createdOrUpdated. Attributes are invalid."
            ]);
        }

        return $this->yaml([
            'message' => self::COURSE_ENTITY_NAME . " resource hasn't been $createdOrUpdated. $errors"
        ]);
    }
}

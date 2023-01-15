<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Service\ResponsePaginator;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class CourseController extends AbstractCourseController
{
    /**
     * String constant for entity name.
     */
    private const COURSE_ENTITY_NAME = 'course';

    /**
     * CourseController constructor.
     *
     * @param CourseRepository $repository
     * @param ResponsePaginator $paginator
     */
    public function __construct(
        private           readonly CourseRepository $repository,
        ResponsePaginator $paginator
    ) {
        parent::__construct($paginator);
    }

    /**
     * Showing a course.
     *
     * @param int $id
     * @return Response
     */
    #[Route(
        '/api/course/{id}',
        name: 'course_show',
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
     * Courses listing.
     *
     * @param Request $request
     * @return Response
     */
    #[Route(
        '/api/course',
        name: 'course',
        methods: ['GET']
    )]
    public function index(Request $request): Response
    {
        return $this->indexEntity($this->repository, $request);
    }

    /**
     * Creating a course.
     *
     * @param Request $request
     * @return Response
     */
    #[Route(
        '/api/course/create',
        name: 'course_create',
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
     * Updating a course.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    #[Route(
        '/api/course/update/{id}',
        name: 'course_update',
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
     * Deleting a course.
     *
     * @param int $id
     * @return Response
     */
    #[Route(
        '/api/course/delete/{id}',
        name: 'course_delete',
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
     * Updating a course if it exists, otherwise creating it.
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
        $course = $id ? $this->repository->find($id) : new Course();

        if ($course === null) {
           return $this->yaml([
               'No '. self::COURSE_ENTITY_NAME. ' found for id ' . $id
           ]);
        }

        if ($id === null) {
            $course->setCreatedAt(new DateTimeImmutable('now'));
        }

        $body = $request->getContent();
        $data = json_decode($body, true);

        $form = $this->createForm(CourseType::class, $course);
        $form->submit($data);

        if ($form->isValid()) {
            $courseName = $form->getData()->getName();
            $course->setName($courseName);

            $this->repository->save($course, true);

            $id = $id ?: $course->getId();

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
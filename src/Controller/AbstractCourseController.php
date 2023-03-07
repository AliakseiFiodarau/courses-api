<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ResponsePaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface as Repository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Tools\Pagination\Paginator;

abstract class AbstractCourseController extends AbstractController
{
    /**
     * String constants for creating and updating entities.
     */
    public const ENTITY_CREATED = 'created';
    public const ENTITY_UPDATED = 'updated';

    /**
     * AbstractCourseController constructor.
     *
     * @param ResponsePaginator $paginator
     */
    public function __construct(
        public ResponsePaginator $paginator
    ) {}

    /**
     * Showing an entity.
     *
     * @param Repository $repository
     * @param int $id
     * @param string $entityName
     * @return JsonResponse
     */
    public function showEntity(
        Repository $repository,
        int        $id,
        string     $entityName
    ): JsonResponse {
        $entity = $repository->find($id);

        if ($entity) {
            return $this->json([
                'resource' => $entity,
            ]);
        }

        return $this->json([
            'message' => "No $entityName found for id $id"
        ]);
    }

    /**
     * Entities listing.
     *
     * @param Repository $repository
     * @param Request $request
     * @return JsonResponse|Paginator
     */
    public function indexEntity(Repository $repository, Request $request): JsonResponse|Paginator
    {
        $pageNumber = $request->query->get(ResponsePaginator::PAGE_NUMBER);
        $query = $repository->getQuery();
        $entities = $this->paginator->paginate($query, $pageNumber);

        return $this->json([
            'page' => $pageNumber,
            'resources' => $entities,
        ]);
    }

    /**
     * Get list of entities by property.
     *
     * @param Repository $repository
     * @param mixed $value
     * @param string $propertyName
     * @param Request $request
     * @return JsonResponse
     */
    public function getByProperty(
        Repository $repository,
        mixed      $value,
        string     $propertyName,
        Request    $request,
    ): JsonResponse {
        $pageNumber = $request->query->get(ResponsePaginator::PAGE_NUMBER);
        $where = "WHERE e.$propertyName = $value";
        $query = $repository->getQuery($where);
        $entities = $this->paginator->paginate($query, $pageNumber);

        return $this->json([
            'page' => $pageNumber,
            'resources' => $entities,
        ]);
    }

    /**
     * Deleting an entity.
     *
     * @param Repository $repository
     * @param int $id
     * @param string $entityName
     * @return JsonResponse
     */
    public function deleteEntity(
        Repository $repository,
        int        $id,
        string     $entityName
    ): JsonResponse {
        $entity = $repository->find($id);
        if ($entity) {
            try {
                $repository->remove($entity, true);

                return $this->json([
                    'message' => "$entityName with id $id has been deleted"
                ]);
            } catch (Exception) {
                return $this->json([
                    'message' => "Unable to delete $entityName with id $id"
                ]);
            }

        }

        return $this->json([
            'message' => "No $entityName found for id $id"
        ]);
    }
}

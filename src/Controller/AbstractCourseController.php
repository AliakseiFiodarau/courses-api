<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\ResponsePaginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface as Repository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AbstractCourseController extends AbstractController
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
     * @return Response
     */
    public function showEntity(
        Repository $repository,
        int        $id,
        string     $entityName
    ): Response {
        $entity = $repository->find($id);

        if ($entity) {
            return $this->yaml([
                'resource' => $entity,
            ]);
        }

        return $this->yaml([
            'message' => "No $entityName found for id $id"
        ]);
    }

    /**
     * Entities listing.
     *
     * @param Repository $repository
     * @param Request $request
     * @return Response
     */
    public function indexEntity(Repository $repository, Request $request): Response
    {
        $totalEntities = $repository->findAll();
        $pageNumber = $request->query->get(ResponsePaginator::PAGE_NUMBER);
        $entities = $this->paginator->paginate($totalEntities, $pageNumber);

        return $this->yaml([
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
     * @param string $entityName
     * @return Response
     */
    public function getByProperty(
        Repository $repository,
        mixed      $value,
        string     $propertyName,
        Request    $request,
        string     $entityName
    ): Response {
        $totalEntities = $repository->findBy([$propertyName => $value]);

        if ($totalEntities) {
            $pageNumber = $request->query->get(ResponsePaginator::PAGE_NUMBER);
            $entities = $this->paginator->paginate($totalEntities, $pageNumber);

            return $this->yaml([
                'page' => $pageNumber,
                'resources' => $entities,
            ]);
        }

        return $this->yaml([
            'message' => "No $entityName found for $propertyName with value $value"
        ]);
    }

    /**
     * Deleting an entity.
     *
     * @param Repository $repository
     * @param int $id
     * @param string $entityName
     * @return Response
     */
    public function deleteEntity(
        Repository $repository,
        int        $id,
        string     $entityName
    ): Response {
        $entity = $repository->find($id);
        if ($entity) {
            try {
                $repository->remove($entity, true);

                return $this->yaml([
                    'message' => "$entityName with id $id has been deleted"
                ]);
            } catch (Exception) {
                return $this->yaml([
                    'message' => "Unable to delete $entityName with id $id"
                ]);
            }

        }

        return $this->yaml([
            'message' => "No $entityName found for id $id"
        ]);
    }


    /**
     * Returns a Response that uses the serializer component if enabled, or json_encode
     *
     * @param mixed $data
     * @param int $status The HTTP status code (200 "OK" by default)
     * @param array $headers
     * @return Response
     */
    public function yaml(
        mixed $data,
        int $status = 200,
        array $headers = [],
    ): Response {
        if ($this->container->has('serializer')) {
            $yaml = $this->container->get('serializer')->serialize($data, 'yaml');

            return new Response($yaml, $status, $headers);
        }

        return new JsonResponse($data, $status, $headers);
    }
}

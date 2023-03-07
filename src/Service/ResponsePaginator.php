<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;
use Doctrine\ORM\Query;

class ResponsePaginator
{
    /**
     * Items per page config name.
     */
    private const ITEMS_PER_PAGE_CONFIG = 'app.items.per.page';

    /**
     * Request parameter name for page number.
     */
    public const PAGE_NUMBER = 'page';

    /**
     * ResponsePaginator constructor.
     *
     * @param ParameterBagInterface $params
     */
    public function __construct(
        private readonly ParameterBagInterface $params
    ) {}

    /**
     * Paginating array of entities.
     *
     * @param Query $query
     * @param string|null $page
     * @return array
     */
    public function paginate(Query $query, ?string $page): array {
        $pageNumber = intval($page);
        $itemsPerPage = $this->params->get(self::ITEMS_PER_PAGE_CONFIG);

        if (empty($pageNumber)) {
            return ['error message' => "incorrect page number: $page"];
        }

        $ormPaginator = new OrmPaginator($query);
        $ormPaginator
            ->getQuery()
            ->setFirstResult($itemsPerPage * ($pageNumber - 1))
            ->setMaxResults($itemsPerPage);
        $total = $ormPaginator->count();

        if ($pageNumber > ceil($total / $itemsPerPage)) {
            return ['error message' => "no items for page: $page"];
        }

        return $query->getResult();
    }
}
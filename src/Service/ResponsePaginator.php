<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

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
     * @param array $totalItems
     * @param string $page
     * @return string[]
     */
    public function paginate(array $totalItems, string $page = '1'): array {
        $pageNumber = intval($page);
        $itemsPerPage = $this->params->get(self::ITEMS_PER_PAGE_CONFIG);

        if (empty($pageNumber)) {
            return ['error message' => "incorrect page number: $page"];
        }

        if ($pageNumber > ceil(count($totalItems) / $itemsPerPage)) {
            return ['error message' => "no items for page: $page"];
        }

        $paginated = array_chunk($totalItems, $itemsPerPage);

        return $paginated[$pageNumber];
    }
}
<?php
/**
 * Shorten service interface.
 */

namespace App\Service;

use App\Entity\Shorten;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface ShortenServiceInterface.
 */
interface ShortenServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save entity.
     *
     * @param Shorten $shorten Shorten entity
     */
    public function save(Shorten $shorten): void;

    /**
     * Delete entity.
     *
     * @param Shorten $shorten Shorten entity
     */
    public function delete(Shorten $shorten): void;
}

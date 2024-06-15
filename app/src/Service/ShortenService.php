<?php
/**
 * Shorten service.
 */

namespace App\Service;

use App\Entity\Shorten;
use App\Repository\ShortenRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ShortenService.
 */
class ShortenService implements ShortenServiceInterface
{
    /**
     * Shorten repository.
     */
    private ShortenRepository $shortenRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param ShortenRepository  $shortenRepository Shorten repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(ShortenRepository $shortenRepository, PaginatorInterface $paginator)
    {
        $this->shortenRepository = $shortenRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get Paginated list.
     *
     * @param int $page Page
     *
     * @return PaginationInterface Interface
     */
    public function getPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->shortenRepository->queryAll(),
            $page,
            ShortenRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Save entity.
     *
     * @param Shorten $shorten Shorten entity
     */
    public function save(Shorten $shorten): void
    {
        $this->shortenRepository->save($shorten);
    }

    /**
     * Delete entity.
     *
     * @param Shorten $shorten Shorten entity
     */
    public function delete(Shorten $shorten): void
    {
        $this->shortenRepository->delete($shorten);
    }
}

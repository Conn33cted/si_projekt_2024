<?php
/**
 * Shorten service.
 */

namespace App\Service;

use App\Entity\Shorten;
use App\Entity\Guest;
use App\Repository\GuestRepository;
use App\Repository\ShortenRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * Guest repository.
     */
    private GuestRepository $guestRepository;

    /**
     * Paginator.
     */
    private PaginatorInterface $paginator;

    /**
     * Constructor.
     *
     * @param ShortenRepository  $shortenRepository Shorten repository
     * @param GuestRepository    $guestRepository   Guest repository
     * @param PaginatorInterface $paginator         Paginator
     */
    public function __construct(ShortenRepository $shortenRepository, GuestRepository $guestRepository, PaginatorInterface $paginator)
    {
        $this->shortenRepository = $shortenRepository;
        $this->guestRepository = $guestRepository;
        $this->paginator = $paginator;
    }

    /**
     * Get paginated list.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface<string, mixed> Paginated list
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

    /**
     * Find entity by shortenOut.
     *
     * @param string $shortenOut Shorten output
     *
     * @return Shorten|null Shorten entity
     *
     * @throws \Exception If the link is blocked
     */
    public function findByShortenOut(string $shortenOut): ?Shorten
    {
        $shorten = $this->shortenRepository->findOneBy(['shortenOut' => $shortenOut]);

        if ($shorten && $shorten->isBlocked()) {
            throw new \Exception('This link is blocked.');
        }

        return $shorten;
    }

    /**
     * Handle create action.
     *
     * @param Shorten            $shorten Shorten entity
     * @param FormInterface      $form    Form
     * @param UserInterface|null $user    User
     * @param string             $ip      Guest IP address
     *
     * @throws \Exception If the limit is reached
     */
    public function handleCreate(Shorten $shorten, FormInterface $form, ?UserInterface $user, string $ip): void
    {
        $creationCount = $this->getCreationCount($user, $ip);
        if (null === $user && 2 <= $creationCount) {  // Yoda condition
            throw new \Exception('Limit reached');
        }

        $newGuest = new Guest();
        $newGuest->setGuestEmail($form->get('guest')->getData());
        $newGuest->setIdentifier($ip);
        $this->guestRepository->save($newGuest);

        $shorten->setGuest($newGuest);
        $shorten->setShortenOut(rand(1, 9999));
        $shorten->setAddDate(new \DateTime('now'));
        $shorten->setClickCounter(0);
        $this->save($shorten);
    }

    /**
     * Get creation count.
     *
     * @param UserInterface|null $user User
     * @param string             $ip   Guest IP address
     *
     * @return int Creation count
     */
    public function getCreationCount(?UserInterface $user, string $ip): int
    {
        if (null !== $user) {  // Yoda condition
            return 0;
        }

        return $this->guestRepository->count(['identifier' => $ip]);
    }

    /**
     * Block the shorten entity.
     *
     * @param Shorten $shorten Shorten entity
     */
    public function block(Shorten $shorten): void
    {
        $shorten->setBlocked(true);
        $this->save($shorten);
    }

    /**
     * Unblock the shorten entity.
     *
     * @param Shorten $shorten Shorten entity
     */
    public function unblock(Shorten $shorten): void
    {
        $shorten->setBlocked(false);
        $this->save($shorten);
    }
}

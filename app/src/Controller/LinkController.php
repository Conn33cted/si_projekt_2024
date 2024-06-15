<?php
/**
 * Link controller.
 */

namespace App\Controller;

use App\Repository\ShortenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *Class LinkController.
 */
#[Route('/link')]
class LinkController extends AbstractController
{
    private ShortenRepository $shortenRepository;

    /**
     * Constructor.
     *
     * @param ShortenRepository $shortenRepository Shorten repository
     */
    public function __construct(ShortenRepository $shortenRepository)
    {
        $this->shortenRepository = $shortenRepository;
    }

    /**
     * Redirect to the original URL based on the shortened URL.
     *
     * @param string $shortenOut Shortened URL suffix
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{shortenOut}',
        name: 'link_index',
        requirements: ['shortenOut' => '[0-9]+'],
        defaults: ['shortenOut' => '0'],
        methods: 'GET'
    )]
    public function index(string $shortenOut): Response
    {
        $shorten = $this->shortenRepository->findOneBy(['shortenOut' => $shortenOut]);

        if (!$shorten) {
            return $this->redirectToRoute('shorten_index');
        }

        $originalUrl = $shorten->getShortenIn();
        $shorten->setClickCounter($shorten->getClickCounter() + 1);
        $this->shortenRepository->save($shorten);

        if (!str_starts_with($originalUrl, 'https://') && !str_starts_with($originalUrl, 'http://')) {
            $originalUrl = 'https://'.$originalUrl;
        }

        return $this->redirect($originalUrl);
    }
}

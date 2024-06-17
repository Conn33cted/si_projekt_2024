<?php
/**
 * Link controller.
 */

namespace App\Controller;

use App\Repository\ShortenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LinkController.
 */
#[Route('/link')]
class LinkController extends AbstractController
{
    private ShortenRepository $shortenRepository;
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param ShortenRepository   $shortenRepository Shorten repository
     * @param TranslatorInterface $translator        Translator
     */
    public function __construct(ShortenRepository $shortenRepository, TranslatorInterface $translator)
    {
        $this->shortenRepository = $shortenRepository;
        $this->translator = $translator;
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
            $this->addFlash('warning', $this->translator->trans('message.link_not_found'));

            return $this->redirectToRoute('shorten_index');
        }

        if ($shorten->isBlocked()) {
            $this->addFlash('warning', $this->translator->trans('message.link_blocked'));

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

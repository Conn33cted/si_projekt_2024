<?php
/**
 * Link controller.
 */

namespace App\Controller;

use App\Repository\ShortenRepository;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 *Class LinkController.
 */
#[Route('/link')]
class LinkController extends AbstractController
{
    /**
     * Shorten repository.
     */
    private ShortenRepository $shortenRepository;

    /**
     * Link index.
     *
     * @param string            $out               Shorten out
     * @param ShortenRepository $shortenRepository Shorten Repo
     *
     * @return Response Response
     */
    #[NoReturn] #[Route(
        '/{out}',
        name: 'link_index',
        requirements: ['out' => '[0-9]+'],
        defaults: ['out' => '0'],
        methods: 'GET'
    )]
    public function index(string $out, ShortenRepository $shortenRepository): Response
    {
        $this->shortenRepository = $shortenRepository;
        $record = $this->shortenRepository->findOneBy(['shortenOut' => $out]);
        $shorten = $record->getShortenIn();
        $record->setClickCounter($record->getClickCounter() + 1);
        $shortenRepository->save($record);
        if (str_starts_with($shorten, 'https://') || str_starts_with($shorten, 'http://')) {
            $link = $shorten;
        } else {
            $link = 'https://'.$shorten;
        }

        return $this->redirect($link);
    }
}

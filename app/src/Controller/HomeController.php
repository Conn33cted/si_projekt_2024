<?php
/**
 * Home controller.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController.
 */
class HomeController extends AbstractController
{
    /**
     * Index function.
     *
     * @return Response Response
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('shorten_index');
    }
}

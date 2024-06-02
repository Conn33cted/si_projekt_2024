<?php
/**
 * Shorten controller.
 */

namespace App\Controller;

use App\Entity\Shorten;
use App\Entity\Guest;
use App\Entity\User;
use App\Form\Type\ShortenType;
use App\Repository\GuestRepository;
use App\Service\ShortenServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ShortenController.
 */
#[Route('/shorten')]
class ShortenController extends AbstractController
{
    private ShortenServiceInterface $shortenService;
    private TranslatorInterface $translator;
    private GuestRepository $guestRepository;

    public function __construct(ShortenServiceInterface $shortenService, TranslatorInterface $translator, GuestRepository $guestRepository)
    {
        $this->shortenService = $shortenService;
        $this->translator = $translator;
        $this->guestRepository = $guestRepository;
    }

    #[Route(name: 'shorten_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->shortenService->getPaginatedList(
            $request->query->getInt('page', 1),
        );

        return $this->render('shorten/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/{id}', name: 'shorten_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Shorten $shorten): Response
    {
        return $this->render('shorten/show.html.twig', ['shorten' => $shorten]);
    }

    #[Route('/create', name: 'shorten_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $shorten = new Shorten();
        $form = $this->createForm(
            ShortenType::class,
            $shorten,
            ['action' => $this->generateUrl('shorten_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newGuest = new Guest();
            $newGuest->setGuestEmail($form->get('guest')->getData());
            $this->guestRepository->save($newGuest);
            $shorten->setGuest($newGuest);
            $shorten->setShortenOut(rand(0001, 9999));
            $shorten->setAddDate(new \DateTime('now'));
            $shorten->setClickCounter(0);
            $this->shortenService->save($shorten);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render(
            'shorten/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    #[Route('/{id}/edit', name: 'shorten_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_ADMIN', subject: 'shorten')]
    public function edit(Request $request, Shorten $shorten): Response
    {
        $form = $this->createForm(
            ShortenType::class,
            $shorten,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('shorten_edit', ['id' => $shorten->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->shortenService->save($shorten);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render(
            'shorten/edit.html.twig',
            [
                'form' => $form->createView(),
                'shorten' => $shorten,
            ]
        );
    }

    #[Route('/{id}/delete', name: 'shorten_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('ROLE_ADMIN', subject: 'shorten')]
    public function delete(Request $request, Shorten $shorten): Response
    {
        $form = $this->createForm(
            FormType::class,
            $shorten,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('shorten_delete', ['id' => $shorten->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->shortenService->delete($shorten);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render(
            'shorten/delete.html.twig',
            [
                'form' => $form->createView(),
                'shorten' => $shorten,
            ]
        );
    }
}

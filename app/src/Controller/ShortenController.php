<?php

/**
 * Shorten Controller.
 */

namespace App\Controller;

use App\Entity\Shorten;
use App\Form\Type\ShortenType;
use App\Service\ShortenServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ShortenController.
 */
#[Route('/shorten')]
class ShortenController extends AbstractController
{
    private ShortenServiceInterface $shortenService;
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param ShortenServiceInterface $shortenService Shorten service
     * @param TranslatorInterface     $translator     Translator
     */
    public function __construct(ShortenServiceInterface $shortenService, TranslatorInterface $translator)
    {
        $this->shortenService = $shortenService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'shorten_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->shortenService->getPaginatedList($request->query->getInt('page', 1));

        return $this->render('shorten/index.html.twig', [
            'pagination' => $pagination,
            'creation_count' => $this->shortenService->getCreationCount($this->getUser(), $request->getClientIp()),
        ]);
    }

    /**
     * Show action.
     *
     * @param Request $request HTTP request
     * @param Shorten $shorten Shorten entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'shorten_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Request $request, Shorten $shorten): Response
    {
        return $this->render('shorten/show.html.twig', [
            'shorten' => $shorten,
            'creation_count' => $this->shortenService->getCreationCount($this->getUser(), $request->getClientIp()),
        ]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'shorten_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $shorten = new Shorten();
        $form = $this->createForm(ShortenType::class, $shorten, ['action' => $this->generateUrl('shorten_create')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Sprawdzenie limitu i utworzenie skróconego linku
                $this->shortenService->handleCreate($shorten, $form, $this->getUser(), $request->getClientIp());
                $this->addFlash('success', $this->translator->trans('message.created_successfully'));

                return $this->redirectToRoute('shorten_index');
            } catch (\Exception $e) {
                // Wyświetlenie komunikatu o przekroczeniu limitu
                $this->addFlash('warning', $e->getMessage());
            }
        }

        return $this->render('shorten/create.html.twig', [
            'form' => $form->createView(),
            'creation_count' => $this->shortenService->getCreationCount($this->getUser(), $request->getClientIp()),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Shorten $shorten Shorten entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'shorten_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Shorten $shorten): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $shorten);

        $form = $this->createForm(ShortenType::class, $shorten, [
            'method' => 'PUT',
            'action' => $this->generateUrl('shorten_edit', ['id' => $shorten->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->shortenService->save($shorten);
            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render('shorten/edit.html.twig', [
            'form' => $form->createView(),
            'shorten' => $shorten,
            'creation_count' => $this->shortenService->getCreationCount($this->getUser(), $request->getClientIp()),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Shorten $shorten Shorten entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'shorten_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Shorten $shorten): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $shorten);

        $form = $this->createForm(FormType::class, $shorten, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('shorten_delete', ['id' => $shorten->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->shortenService->delete($shorten);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render('shorten/delete.html.twig', [
            'form' => $form->createView(),
            'shorten' => $shorten,
            'creation_count' => $this->shortenService->getCreationCount($this->getUser(), $request->getClientIp()),
        ]);
    }

    /**
     * Block action.
     *
     * @param Shorten $shorten Shorten entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/block', name: 'shorten_block', requirements: ['id' => '[1-9]\d*'], methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function block(Shorten $shorten): Response
    {
        $this->shortenService->block($shorten);
        $this->addFlash('success', $this->translator->trans('message.blocked_successfully'));

        return $this->redirectToRoute('shorten_index');
    }

    /**
     * Unblock action.
     *
     * @param Shorten $shorten Shorten entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/unblock', name: 'shorten_unblock', requirements: ['id' => '[1-9]\d*'], methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function unblock(Shorten $shorten): Response
    {
        $this->shortenService->unblock($shorten);
        $this->addFlash('success', $this->translator->trans('message.unblocked_successfully'));

        return $this->redirectToRoute('shorten_index');
    }
}

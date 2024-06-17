<?php

/**
 * Shorten Controller.
 */

namespace App\Controller;

use App\Entity\Shorten;
use App\Entity\Guest;
use App\Form\Type\ShortenType;
use App\Repository\GuestRepository;
use App\Service\ShortenServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
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
    private GuestRepository $guestRepository;
    private $session;

    /**
     * Constructor.
     *
     * @param ShortenServiceInterface $shortenService  Shorten service
     * @param TranslatorInterface     $translator      Translator
     * @param GuestRepository         $guestRepository Guest repository
     * @param RequestStack            $requestStack    Request stack
     */
    public function __construct(ShortenServiceInterface $shortenService, TranslatorInterface $translator, GuestRepository $guestRepository, RequestStack $requestStack)
    {
        $this->shortenService = $shortenService;
        $this->translator = $translator;
        $this->guestRepository = $guestRepository;
        $this->session = $requestStack->getSession();
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
            'creation_count' => $this->getCreationCount(),
        ]);
    }

    /**
     * Show action.
     *
     * @param Shorten $shorten Shorten entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'shorten_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Shorten $shorten): Response
    {
        return $this->render('shorten/show.html.twig', [
            'shorten' => $shorten,
            'creation_count' => $this->getCreationCount(),
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
        $creationCount = 0;
        if (!$this->getUser()) {
            $currentTimestamp = time();
            $creationData = $this->session->get('shorten_creation_data', ['count' => 0, 'timestamp' => $currentTimestamp]);

            if ($currentTimestamp - $creationData['timestamp'] >= 86400) {
                $creationData = ['count' => 0, 'timestamp' => $currentTimestamp];
            }

            $creationCount = $creationData['count'];

            if ($creationData['count'] >= 2) {
                $this->addFlash('warning', $this->translator->trans('message.limit_reached'));

                return $this->redirectToRoute('shorten_index');
            }
        }

        $shorten = new Shorten();
        $shorten->setUser($this->getUser());
        $form = $this->createForm(ShortenType::class, $shorten, ['action' => $this->generateUrl('shorten_create')]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$this->getUser()) {
                ++$creationData['count'];
                $creationData['timestamp'] = $currentTimestamp;
                $this->session->set('shorten_creation_data', $creationData);
            }

            $newGuest = new Guest();
            $newGuest->setGuestEmail($form->get('guest')->getData());
            $this->guestRepository->save($newGuest);
            $shorten->setGuest($newGuest);
            $shorten->setShortenOut(rand(1, 9999));
            $shorten->setAddDate(new \DateTime('now'));
            $shorten->setClickCounter(0);
            $this->shortenService->save($shorten);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render('shorten/create.html.twig', [
            'form' => $form->createView(),
            'creation_count' => $creationCount,
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
            'creation_count' => $this->getCreationCount(),
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
            'creation_count' => $this->getCreationCount(),
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
        $shorten->setBlocked(true);
        $this->shortenService->save($shorten);

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
        $shorten->setBlocked(false);
        $this->shortenService->save($shorten);

        $this->addFlash('success', $this->translator->trans('message.unblocked_successfully'));

        return $this->redirectToRoute('shorten_index');
    }

    /**
     * Session creation count.
     *
     * @return int Creation count
     */
    private function getCreationCount(): int
    {
        $creationData = $this->session->get('shorten_creation_data', ['count' => 0, 'timestamp' => time()]);

        return $creationData['count'];
    }
}

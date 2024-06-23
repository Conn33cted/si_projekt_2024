<?php
/**
 * User Panel controller.
 */

namespace App\Controller;

use App\Form\Type\PasswordChangeType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 *Class UserPanelController.
 */
#[Route('/panel')]
#[IsGranted('ROLE_USER')]
class UserPanelController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    private UserRepository $userRepository;

    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserPasswordHasherInterface $passwordHasher Password Hasher
     * @param UserRepository              $userRepository User Repo
     * @param TranslatorInterface         $translator     Translator
     */
    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, TranslatorInterface $translator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    /**
     * Change password.
     *
     * @param Request $request Request
     *
     * @return Response Response
     */
    #[Route(
        '/password',
        name: 'change_password',
        methods: ['GET', 'POST']
    )]
    #[IsGranted('ROLE_ADMIN')]
    public function changePassword(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(
            PasswordChangeType::class,
            $user,
            ['action' => $this->generateUrl('change_password')]
        );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (password_verify($form['password']->getData(), $user->getPassword())) {
                $newPassword = $this->passwordHasher->hashPassword($user, $form['new_password']->getData());
                $user->setPassword($newPassword);
                $this->userRepository->save($user, true);
                $this->addFlash(
                    'success',
                    $this->translator->trans('message.edited_successfully')
                );
            } else {
                $this->addFlash(
                    'warning',
                    $this->translator->trans('message.error')
                );
            }

            return $this->redirectToRoute('shorten_index');
        }

        return $this->render(
            'panel/index.html.twig',
            ['form' => $form->createView()]
        );
    }
}

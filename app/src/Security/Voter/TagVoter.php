<?php
/**
 * Tag voter.
 */

namespace App\Security\Voter;

use App\Entity\Tag;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class TagVoter.
 */
class TagVoter extends Voter
{
    /**
     * Edit permission.
     *
     * @const string
     */
    public const EDIT = 'EDIT';

    /**
     * Delete permission.
     *
     * @const string
     */
    public const DELETE = 'DELETE';

    /**
     * Security helper.
     */
    private Security $security;

    /**
     * TagVoter constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Tag;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        /** @var Tag $tag */
        $tag = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($tag, $user);
            case self::DELETE:
                return $this->canDelete($tag, $user);
        }

        return false;
    }

    /**
     * Checks if user can edit tag.
     *
     * @param Tag  $tag  Tag entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canEdit(Tag $tag, User $user): bool
    {
        // Implement your logic to check if the user can edit the tag
        // For example, you can check if the user is the creator of the tag
        return $tag->getUser() === $user;
    }

    /**
     * Checks if user can delete tag.
     *
     * @param Tag  $tag  Tag entity
     * @param User $user User
     *
     * @return bool Result
     */
    private function canDelete(Tag $tag, User $user): bool
    {
        // Implement your logic to check if the user can delete the tag
        // For example, you can check if the user is the creator of the tag
        return $tag->getUser() === $user;
    }
}

<?php

namespace App\Security\Voter;

use App\Entity\Movie;
use App\Entity\User;
use App\Movie\Event\MovieUnderageEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class MovieVoter extends Voter
{
    public const UNDERAGE = 'movie.is_underage';
    public const CREATOR = 'movie.is_creator';

    public function __construct(
        protected readonly AuthorizationCheckerInterface $checker,
    )
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $subject instanceof Movie
            && \in_array($attribute, [self::UNDERAGE, self::CREATOR]);
    }

    /**
     * @param Movie $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return $this->checker->isGranted('ROLE_ADMIN');
        }

        return match ($attribute){
            self::UNDERAGE => $this->checkAge($subject, $user),
            self::CREATOR => $this->checkIsCreator($subject, $user),
            default => false,
        };
    }

    private function checkAge(Movie $movie, User $user): bool
    {
        return match ($movie->getRated()) {
            'G' => true,
            'PG', 'PG-13' => $user->getAge() && $user->getAge() >= 13,
            'R', 'NC-17' => $user->getAge() && $user->getAge() >= 17,
            default => false,
        };
    }

    private function checkIsCreator(Movie $movie, User $user): bool
    {
        return $this->checkAge($movie, $user) && $movie->getCreatedBy() === $user;
    }
}

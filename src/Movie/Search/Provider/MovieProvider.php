<?php

namespace App\Movie\Search\Provider;

use App\Entity\Movie;
use App\Entity\User;
use App\Movie\Search\Consumer\OmdbApiConsumer;
use App\Movie\Search\Enum\SearchType;
use App\Movie\Search\Transformer\OmdbMovieTransformer;
use App\Security\Voter\MovieVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MovieProvider
{
    protected ?SymfonyStyle $io = null;

    public function __construct(
        protected readonly EntityManagerInterface $manager,
        protected readonly OmdbApiConsumer $consumer,
        protected readonly OmdbMovieTransformer $transformer,
        protected readonly GenreProvider $genreProvider,
        protected readonly Security $security,
    )
    {
    }

    public function getOne(SearchType $type, string $value): ?Movie
    {
        $movie = $this->manager->getRepository(Movie::class)->findLikeOmdb($type, $value);

        if ($movie instanceof Movie) {
            $this->io?->note('Movie already in database!');

            return $movie;
        }

        try {
            $this->io?->text('Searching on OMDb');
            $data = $this->consumer->fetchMovieData($type, $value);
        } catch (NotFoundHttpException) {
            return null;
        }

        $movie = $this->transformer->transform($data);
        foreach ($this->genreProvider->getFromOmdbString($data['Genre']) as $genre) {
            $movie->addGenre($genre);
        }

        $user = $this->security->getUser();
        if ($user instanceof User && $this->security->isGranted(MovieVoter::UNDERAGE)) {
            $movie->setCreatedBy($user);
        }

        $this->manager->persist($movie);
        $this->manager->flush();
        $this->io?->note('Movie saved in Db!');

        return $movie;
    }

    public function setIo(?SymfonyStyle $io): void
    {
        $this->io = $io;
    }
}

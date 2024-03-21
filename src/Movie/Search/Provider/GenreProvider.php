<?php

namespace App\Movie\Search\Provider;

use App\Entity\Genre;
use App\Movie\Search\Transformer\OmdbGenreTransformer;
use App\Repository\GenreRepository;

class GenreProvider
{
    public function __construct(
        protected readonly GenreRepository $repository,
        protected readonly OmdbGenreTransformer $transformer,
    )
    {
    }

    public function getOne(string $name): Genre
    {
        return $this->repository->findOneBy(['name' => $name])
            ?? $this->transformer->transform($name);
    }

    public function getFromOmdbString(string $omdb): iterable
    {
        foreach (explode(', ', $omdb) as $name) {
            yield $this->getOne($name);
        }
    }
}

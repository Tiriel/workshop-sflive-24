<?php

namespace App\Movie\Search\Transformer;

use App\Entity\Movie;

class OmdbMovieTransformer implements OmdbTransformerInterface
{
    private const KEYS = [
        'Title',
        'Poster',
        'Plot',
        'Country',
        'Year',
        'Released',
        'imdbID',
        'Rated',
    ];

    public function transform(mixed $value): Movie
    {
        if (
            !\is_array($value)
            || 0 < \count(\array_diff(self::KEYS, \array_keys($value)))
        ) {
            throw new \InvalidArgumentException();
        }

        $date = 'N/A' === $value['Released'] ? '01-01-'.$value['Year'] : $value['Released'];

        return (new Movie())
            ->setTitle($value['Title'])
            ->setPlot($value['Plot'])
            ->setPoster($value['Poster'])
            ->setCountry($value['Country'])
            ->setReleasedAt(new \DateTimeImmutable($date))
            ->setImdbId($value['imdbID'])
            ->setRated($value['Rated'])
            ->setPrice(500);
    }
}

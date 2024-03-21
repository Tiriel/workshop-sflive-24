<?php

namespace App\Movie\Search\Transformer;

use App\Entity\Genre;

class OmdbGenreTransformer implements OmdbTransformerInterface
{
    public function transform(mixed $value): Genre
    {
        if (!is_string($value) || str_contains(', ', $value)) {
            throw new \InvalidArgumentException();
        }

        return (new Genre())->setName($value);
    }
}

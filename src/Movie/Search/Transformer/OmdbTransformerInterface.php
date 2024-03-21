<?php

namespace App\Movie\Search\Transformer;

interface OmdbTransformerInterface
{
    public function transform(mixed $value): object;
}

<?php

namespace App\Movie\Search\Consumer;

use App\Movie\Search\Enum\SearchType;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[When('prod')]
#[AsDecorator(OmdbApiConsumer::class, priority: 5)]
class CacheableOmdbApiConsumer extends OmdbApiConsumer
{
    public function __construct(
        protected readonly OmdbApiConsumer $inner,
        protected readonly CacheInterface $cache,
        protected readonly SluggerInterface $slugger,
    )
    {
    }

    public function fetchMovieData(SearchType $type, string $value): array
    {
        $key = sprintf("%s_%s", $type->value, $this->slugger->slug($value));

        return $this->cache->get(
            $key,
            function (CacheItem $item) use ($type, $value) {
                $item->expiresAfter(3600);

                return $this->inner->fetchMovieData($type, $value);
            }
        );
    }
}

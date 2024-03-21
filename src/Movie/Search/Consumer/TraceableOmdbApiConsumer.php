<?php

namespace App\Movie\Search\Consumer;

use App\Movie\Search\Enum\SearchType;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[When('dev')]
#[When('prod')]
#[AsDecorator(OmdbApiConsumer::class, priority: 10)]
class TraceableOmdbApiConsumer extends OmdbApiConsumer
{
    public function __construct(
        protected readonly OmdbApiConsumer $inner,
        protected readonly LoggerInterface $logger,
    )
    {
    }

    public function fetchMovieData(SearchType $type, string $value): array
    {
        $this->logger->log('info', 'New request to OMDb API.');

        return $this->inner->fetchMovieData($type, $value);
    }
}

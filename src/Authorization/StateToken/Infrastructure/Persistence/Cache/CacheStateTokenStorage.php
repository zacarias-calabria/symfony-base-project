<?php

declare(strict_types=1);

namespace App\Authorization\StateToken\Infrastructure\Persistence\Cache;

use App\Authorization\StateToken\Domain\StateToken;
use App\Authorization\StateToken\Domain\StateTokenStorage;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

final class CacheStateTokenStorage implements StateTokenStorage
{
    public const int DEFAULT_TTL = 3600;

    private CacheItemPoolInterface $cache;
    private string $namespace;

    public function __construct(
        CacheItemPoolInterface $cache,
        string $namespace = 'oauth_state_token_cache',
    ) {
        $this->cache = $cache;
        $this->namespace = $namespace;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function hasToken(StateToken $stateToken): bool
    {
        $key = $this->getKey($stateToken);

        return $this->cache->hasItem($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function load(StateToken $stateToken): ?object
    {
        $key = $this->getKey($stateToken);
        $item = $this->cache->getItem($key);

        if (!$item->isHit()) {
            return null;
        }

        return $item->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function save(StateToken $stateToken, ?object $state = null, ?int $expiresIn = null): void
    {
        $key = $this->getKey($stateToken);
        $item = $this->cache->getItem($key);
        $item->set($state);
        $item->expiresAfter($expiresIn ?? self::DEFAULT_TTL);

        $this->cache->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function remove(StateToken $stateToken): void
    {
        $key = $this->getKey($stateToken);

        $this->cache->deleteItem($key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function flush(StateToken $stateToken): ?object
    {
        $return = $this->load($stateToken);
        $this->remove($stateToken);

        return $return;
    }

    private function getKey(StateToken $stateToken): string
    {
        return sprintf('%s_%s', $this->namespace, $stateToken);
    }
}

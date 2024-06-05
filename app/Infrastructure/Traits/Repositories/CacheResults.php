<?php

namespace Infrastructure\Traits\Repositories;

use Illuminate\Contracts\Container\BindingResolutionException;

trait CacheResults
{
    /**
     * Array of predefined method that should cache.
     *
     * @var array
     */
    protected array $baseCacheableMethods = [

    ];

    /**
     * Disable caching on the fly.
     *
     * @return $this
     */
    public function disableCaching(): static
    {
        $this->caching = false;

        return $this;
    }

    /**
     * Get ttl (minutes).
     *
     * @return int
     */
    protected function getCacheTtl(): int
    {
        return $this->cacheTtl ?? 60;
    }

    /**
     * @param $methodName
     * @return bool
     */
    protected function isCacheableMethod($methodName): bool
    {
        return in_array($methodName, $this->getCacheableMethods());
    }

    /**
     * Perform the query and cache if required.
     *
     * @param $callback
     * @param $method
     * @param $args
     * @return mixed
     * @throws BindingResolutionException
     */
    protected function processCacheRequest($callback, $method, $args): mixed
    {
        $key = $this->createCacheKey($method, $args);

        return $this->getCache()->remember($key, $this->getCacheTtl(), $callback);
    }

    /**
     * Make a unique key for this specific request.
     *
     * @param $functionName string Name of method to call.
     * @param $args array Argument to pass into the method.
     * @return string
     */
    protected function createCacheKey(string $functionName, array $args): string
    {
        return sprintf('%s.%s.%s', get_class(), $functionName, md5(implode('|', $args)));
    }

    /**
     * returns Illuminate\Contracts\Cache\Repository
     * @throws BindingResolutionException
     */
    protected function getCache()
    {
        return app()->make('Illuminate\Contracts\Cache\Repository');
    }

    /**
     * @return array
     */
    protected function getCacheableMethods(): array
    {
        $methods = $this->baseCacheableMethods;

        // Include user defined methods.
        if (isset($this->cacheableMethods)) {
            $methods = array_merge($this->baseCacheableMethods, $this->cacheableMethods);
        }

        // Filter any unwanted methods.
        /*if (isset($this->nonCacheableMethods)) {
            $methods = array_filter($methods, function ($methodName) {
                return !in_array($methodName, $this->nonCacheableMethods);
            });
        }*/

        return $methods;
    }
}

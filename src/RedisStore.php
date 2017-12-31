<?php

namespace TillKruss\LaravelPhpRedis;

use Illuminate\Cache\TagSet;
use Illuminate\Cache\RedisStore as Store;
use Illuminate\Support\Facades\Log;

class RedisStore extends Store
{
    /**
     * Retrieve an item from the cache by key.
     *
     * @param  string|array $key
     * @return mixed
     * @throws \Exception
     */
    public function get($key)
    {
        $value = null;
        try {
            $value = $value = $this->connection()->get($this->prefix . $key);
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            if (in_array($message, [
                'read error on connection',
            ])) {
                Log::warning('phpredis ' . $message, compact('key'));
                $value = $value = $this->connection()->get($this->prefix . $key);
            } else {
                throw $exception;
            }
        }

        if (!is_null($value) && $value !== false) {
            return is_numeric($value) ? $value : unserialize($value);
        }
    }

    /**
     * Begin executing a new tags operation.
     *
     * @param  array|mixed $names
     * @return \TillKruss\LaravelPhpRedis\RedisTaggedCache
     */
    public function tags($names)
    {
        return new RedisTaggedCache($this, new TagSet($this, is_array($names) ? $names : func_get_args()));
    }
}

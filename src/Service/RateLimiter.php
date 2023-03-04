<?php

namespace App\Service;

use App\Dto\Config;
use App\Dto\RedisPayload;
use Carbon\Carbon;
use Predis\ClientInterface;

class RateLimiter
{
    private ClientInterface $storage;

    public function __construct(ClientInterface $configStorage)
    {
        $this->storage = $configStorage;
    }

    public function isOk(string $url): bool
    {
        $payload = $this->storage->get($this->getKey($url));

        if (null === $payload) {
            $payload = RedisPayload::create($url);
        } else {
            $payload = RedisPayload::fromArray(json_decode($payload, true));
        }

        if (0 === $payload->countPreviousInterval) {
            $previousPayload = $this->storage->get($this->getKey($url, true));

            if (null !== $previousPayload) {
                $previousPayload = RedisPayload::fromArray(json_decode($previousPayload, true));
                $payload->countPreviousInterval = $previousPayload->countCurrentInterval;
            }
        }

        $koefPreviousInterval = (60 - (Carbon::now())->second) / 60;
        $previousCountRequests = round($koefPreviousInterval * $payload->countPreviousInterval);

        if ($previousCountRequests + $payload->countCurrentInterval >= Config::COUNT_REQUEST_PER_INTERVAL) {
            return false;
        }

        $payload->countCurrentInterval = ++$payload->countCurrentInterval;

        $this->storage->set($this->getKey($url), json_encode($payload->toArray()));

        return true;
    }

    private function getKey(string $url, bool $previous = false):string
    {
        $minute = (Carbon::now())->minute;

        if ($previous) {
            $minute--;
        }
        return $url . $minute;
    }
}

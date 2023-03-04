<?php

namespace App\Dto;

class RedisPayload
{
    public int $countPreviousInterval = 0;
    public int $countCurrentInterval = 0;
    public string $url;

    public static function create(string $url): self {
        $i = new self();
        $i->url = $url;

        return $i;
    }

    public function toArray(): array
    {
        return [
            'count_previous_interval' => $this->countPreviousInterval,
            'count_currency_interval' => $this->countCurrentInterval,
        ];
    }

    public static function fromArray(array $array): self
    {
        $i = new self();
        $i->countPreviousInterval = $array['count_previous_interval'];
        $i->countCurrentInterval = $array['count_currency_interval'];

        return  $i;
    }
}

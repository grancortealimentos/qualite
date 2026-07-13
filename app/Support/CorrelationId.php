<?php

namespace App\Support;

use Illuminate\Support\Str;

class CorrelationId
{
    protected ?string $id = null;

    /**
     * Retorna o correlation_id atual.
    */
    public function get(): string
    {
        return $this->id ??= (string) Str::ulid();
    }

    public function set(string $id): void
    {
        $this->id = $id;
    }

    public function reset(): void
    {
        $this->id = null;
    }
}
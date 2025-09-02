<?php

declare(strict_types=1);

namespace Webmaster\Entrypoint;

interface EntrypointInterface
{
    public function handle(): void;
}

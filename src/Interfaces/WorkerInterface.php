<?php

declare(strict_types=1);

namespace EasyRSA\Interfaces;

interface WorkerInterface
{
    /**
     * Show content of certificate file
     *
     * @param string $filename Only name of file must be set, without path
     *
     * @return string|null
     */
    public function getContent(string $filename): ?string;

    /**
     * Execute some command and return result
     *
     * @param string $cmd
     *
     * @return array<string>
     */
    public function exec(string $cmd): array;
}

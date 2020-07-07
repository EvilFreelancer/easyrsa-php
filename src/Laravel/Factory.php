<?php

namespace EasyRSA\Laravel;

use EasyRSA\Worker;

class Factory
{
    public function make(array $config): Worker
    {
        return new Worker($config);
    }
}

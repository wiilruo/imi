<?php

declare(strict_types=1);

namespace Imi\Db\Query;

class QueryType
{
    /**
     * 读.
     */
    const READ = 1;

    /**
     * 写.
     */
    const WRITE = 2;

    private function __construct()
    {
    }
}

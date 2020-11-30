<?php

declare(strict_types=1);

namespace Imi\Pool;

abstract class ResourceConfigMode
{
    /**
     * 轮流
     */
    const TURN = 1;

    /**
     * 随机.
     */
    const RANDOM = 2;
}

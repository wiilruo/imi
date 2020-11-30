<?php

declare(strict_types=1);

namespace Imi\Process\Pool;

class MessageEventParam extends WorkerEventParam
{
    /**
     * 数据.
     *
     * @var array
     */
    protected $data;

    /**
     * Get 数据.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}

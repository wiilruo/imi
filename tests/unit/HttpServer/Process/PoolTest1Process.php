<?php

declare(strict_types=1);

namespace Imi\Test\HttpServer\Process;

use Imi\Pool\Annotation\PoolClean;
use Imi\Pool\PoolManager;
use Imi\Process\Annotation\Process;
use Imi\Process\BaseProcess;

/**
 * @Process("PoolTest1")
 */
class PoolTest1Process extends BaseProcess
{
    /**
     * @PoolClean
     *
     * @param \Swoole\Process $process
     *
     * @return void
     */
    public function run(\Swoole\Process $process)
    {
        $result = [];
        foreach (PoolManager::getNames() as $poolName)
        {
            $pool = PoolManager::getInstance($poolName);
            $result[$poolName] = $pool->getCount();
        }
        echo json_encode($result), \PHP_EOL;
        $process->exit();
    }
}

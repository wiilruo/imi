<?php

declare(strict_types=1);

namespace Imi\Cron\Consts;

use Imi\Enum\Annotation\EnumItem;
use Imi\Enum\BaseEnum;

/**
 * 定时任务类型.
 */
class CronTaskType extends BaseEnum
{
    /**
     * @EnumItem("随机工作进程任务")
     */
    const RANDOM_WORKER = 'random_worker';

    /**
     * @EnumItem("所有工作进程执行的任务")
     */
    const ALL_WORKER = 'all_worker';

    /**
     * @EnumItem("后台任务")
     */
    const TASK = 'task';

    /**
     * @EnumItem("进程")
     */
    const PROCESS = 'process';

    /**
     * @EnumItem("定时任务进程")
     */
    const CRON_PROCESS = 'cron_process';

    private function __construct()
    {
    }
}

<?php

declare(strict_types=1);

namespace Imi\Redis\Annotation;

use Imi\Aop\Annotation\RequestInject;
use Imi\Bean\Annotation\Parser;
use Imi\Redis\RedisManager;

/**
 * 连接池对象注入.
 *
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 * @Parser("Imi\Aop\Parser\AopParser")
 */
class RedisInject extends RequestInject
{
    /**
     * 获取注入值的真实值
     *
     * @return mixed
     */
    public function getRealValue()
    {
        return RedisManager::getInstance($this->name);
    }
}

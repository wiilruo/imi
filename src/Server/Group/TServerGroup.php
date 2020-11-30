<?php

declare(strict_types=1);

namespace Imi\Server\Group;

use Imi\RequestContext;

trait TServerGroup
{
    /**
     * 组配置.
     *
     * @var \Imi\Server\Group\Group[]
     */
    private array $groups = [];

    /**
     * 组是否存在.
     *
     * @param string $groupName
     *
     * @return bool
     */
    public function hasGroup(string $groupName): bool
    {
        return isset($this->groups[$groupName]);
    }

    /**
     * 创建组，返回组对象
     *
     * @param string $groupName
     * @param int    $maxClients
     *
     * @return \Imi\Server\Group\Group
     */
    public function createGroup(string $groupName, int $maxClients = -1): Group
    {
        $groups = &$this->groups;
        if (!isset($groups[$groupName]))
        {
            $groups[$groupName] = RequestContext::getServerBean('ServerGroup', $this, $groupName, $maxClients);
        }

        return $groups[$groupName];
    }

    /**
     * 获取组对象，不存在返回null.
     *
     * @param string $groupName
     *
     * @return \Imi\Server\Group\Group|null
     */
    public function getGroup(string $groupName): ?Group
    {
        return $this->groups[$groupName] ?? null;
    }

    /**
     * 加入组，组不存在则自动创建.
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return void
     */
    public function joinGroup(string $groupName, int $fd)
    {
        $this->createGroup($groupName)->join($fd);
    }

    /**
     * 离开组，组不存在则自动创建.
     *
     * @param string $groupName
     * @param int    $fd
     *
     * @return void
     */
    public function leaveGroup(string $groupName, int $fd)
    {
        $this->createGroup($groupName)->leave($fd);
    }

    /**
     * 调用组方法.
     *
     * @param string $groupName
     * @param string $methodName
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public function groupCall(string $groupName, string $methodName, ...$args)
    {
        return $this->createGroup($groupName)->$methodName(...$args);
    }

    /**
     * 获取所有组列表.
     *
     * @return \Imi\Server\Group\Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }
}

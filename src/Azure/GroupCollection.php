<?php

namespace SDU\MFA\Azure;

use Illuminate\Contracts\Support\Arrayable;

class GroupCollection implements Arrayable
{
    /** @var Group[] */
    private array $groups = [];

    public function add(array $group)
    {
        $group = $this->parse($group);
        if ($group == null)
            return;

        $this->groups[] = $group;
    }

    public function addAll(array $groups)
    {
        foreach ($groups as $group)
            $this->add($group);
    }

    private function parse(array $group) : ?Group
    {
        if ( ! Group::isValid($group))
            return null;

        $azureGroup = new Group();
        $azureGroup->setId($group['id']);
        $azureGroup->setDisplayName($group['displayName']);
        $azureGroup->setDescription($group['description']);

        return $azureGroup;
    }

    public function toArray() : array
    {
        $groups = [];
        foreach ($this->groups as $group)
            $groups[] = $group->toArray();

        return $groups;
    }

    public function contains(string $group) : bool
    {
        foreach ($this->groups as $currentGroup)
        {
            if (trim($group) == $currentGroup->getId() || trim($group) == $currentGroup->getDisplayName())
                return true;
        }
        return false;
    }
}
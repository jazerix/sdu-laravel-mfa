<?php

namespace SDU\MFA\Azure;

use Illuminate\Contracts\Support\Arrayable;

class Group implements Arrayable
{
    private string $id;

    private ?string $displayName;

    private ?string $description;

    /**
     * @return string
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getDisplayName() : ?string
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(?string $displayName) : void
    {
        $this->displayName = $displayName;
    }

    /**
     * @return string|null
     */
    public function getDescription() : ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description) : void
    {
        $this->description = $description;
    }


    public static function isValid(array $group) : bool
    {
        return $group['@odata.type'] == '#microsoft.graph.group';
    }

    public function toArray() : array
    {
        return [
            'id'          => $this->id,
            'displayName' => $this->displayName,
            'description' => $this->description
        ];
    }
}
<?php

namespace Gloord\DeltaParser\Parser;


class Item implements \JsonSerializable
{
    /**
     * @var integer
     */
    protected $itemId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $itemCharacterStates = [];

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     * @param int $itemId
     */
    public function setItemId(int $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param $iChState
     */
    public function addItemCharacterState($iChState)
    {
        $this->itemCharacterStates[] = $iChState;
    }

    /**
     * @return array
     */
    public function getItemCharacterStates()
    {
        return $this->itemCharacterStates;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'itemId'   => $this->getItemId(),
            'name' => $this->getName(),
            'characterStates' => $this->getItemCharacterStates(),
        ];
    }
}
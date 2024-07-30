<?php

namespace Gloord\DeltaParser\Parser;


class Character implements \JsonSerializable
{
    /**
     * @var integer
     */
    protected $characterId;

    /**
     * @var string
     */
    protected $charDescription;

    /**
     * @var string
     */
    protected $charComment;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var array
     */
    protected $charStates = [];

    /**
     * @var string
     */
    protected $charType;

    /**
     * Character constructor.
     */
    public function __construct()
    {
        //set default character type
        $this->charType = 'UM';
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit(string $unit)
    {
        $this->unit = $unit;
    }

    /**
     * @return string
     */
    public function getCharType()
    {
        return $this->charType;
    }

    /**
     * @param string $charType
     */
    public function setCharType(string $charType)
    {
        $this->charType = $charType;
    }

    /**
     * @return int
     */
    public function getCharacterId()
    {
        return $this->characterId;
    }

    /**
     * @return string
     */
    public function getCharDescription()
    {
        return $this->charDescription;
    }

    /**
     * @return array
     */
    public function getCharStates()
    {
        return $this->charStates;
    }

    /**
     * @param int $characterId
     */
    public function setCharacterId(int $characterId)
    {
        $this->characterId = $characterId;
    }

    /**
     * @param string $charDescription
     */
    public function setCharDescription(string $charDescription)
    {
        $this->charDescription = $charDescription;
    }

    /**
     * @param array $charState
     */
    public function addCharState(array $charState)
    {
        $this->charStates[] = $charState;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return [
            'characterId' => $this->getCharacterId(),
            'characterDescription' => $this->getCharDescription(),
            'characterType' => $this->getCharType(),
            'characterUnit' => $this->getUnit(),
            'characterStates' => $this->getCharStates(),
        ];
    }
}
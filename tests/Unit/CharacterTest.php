<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\Character;
use PHPUnit\Framework\TestCase;

class CharacterTest extends TestCase
{
    /**
     * @var Character
     */
    protected $characterInstance;


    protected function setUp():void
    {
        $this->characterInstance = new Character();
    }

    public function testGetDefaultUnit()
    {
        $this->assertEquals('UM', $this->characterInstance->getCharType());
    }

    public function testAddGetCharState()
    {
        $this->characterInstance->addCharState(['test']);
        $this->assertEquals([0 => ['test']]
            , $this->characterInstance->getCharStates());
    }

    public function testSetGetCharDescription()
    {
        $this->characterInstance->setCharDescription('descriptiontest');
        $this->assertEquals('descriptiontest'
            , $this->characterInstance->getCharDescription());
    }

    public function testSetGetCharacterId()
    {
        $this->characterInstance->setCharacterId(42);
        $this->assertEquals(42, $this->characterInstance->getCharacterId());
    }

    public function testSetGetUnit()
    {
        $this->characterInstance->setUnit('mm');
        $this->assertEquals('mm', $this->characterInstance->getUnit());
    }

    public function testSetGetCharType()
    {
        $this->characterInstance->setCharType('TE');
        $this->assertEquals('TE', $this->characterInstance->getCharType());
    }

    public function testJsonSerialize()
    {
        $this->characterInstance->addCharState(['test']);
        $this->characterInstance->setCharDescription('descriptiontest');
        $this->characterInstance->setCharacterId(42);
        $this->characterInstance->setUnit('mm');
        $this->characterInstance->setCharType('TE');
        $this->assertEquals([
                'characterId' => 42,
                'characterDescription' => 'descriptiontest',
                'characterType' => 'TE',
                'characterUnit' => 'mm',
                'characterStates' => [0 => ['test']],
            ], $this->characterInstance->jsonSerialize());
    }
}
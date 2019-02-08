<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\DeltaSpecs;
use PHPUnit\Framework\TestCase;

class DeltaSpecsTest extends TestCase
{
    /**
     * @var DeltaSpecs
     */
    protected $deltaSpecsInstance;

    protected $characterTestArray = [
        'NUMBER_OF_CHARACTERS' => 21,
        'CHARACTER_TYPES' => [
            '1' => [
                'characterType' => 'TE',
            ],
        ],
    ];

    protected function setUp()
    {
        $this->deltaSpecsInstance = new DeltaSpecs($this->characterTestArray);
    }

    public function testGetSpecValue()
    {
        $this->assertEquals(21, $this->deltaSpecsInstance->getSpecValue('NUMBER_OF_CHARACTERS'));
    }

    public function testGetSpecValueInvalidKeyReturnsFalse()
    {
        $this->assertEquals(false, $this->deltaSpecsInstance->getSpecValue('NUMBER_OF_FREE_BEERS'));
    }

    public function testGetParsedSpecs()
    {
        $this->assertEquals($this->characterTestArray, $this->deltaSpecsInstance->getParsedSpecs());
    }

    public function testGetCharTypeByCharId()
    {
        $this->assertEquals("TE", $this->deltaSpecsInstance->getCharTypeByCharId(1));
    }

    public function testGetCharTypeByIdyNoKeyReturnsFalse()
    {
        $this->assertEquals(false, $this->deltaSpecsInstance->getCharTypeByCharId(84));
    }
}
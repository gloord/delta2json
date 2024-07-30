<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\DeltaSpecsParser;
use PHPUnit\Framework\TestCase;

class DeltaSpecsParserTest extends TestCase
{
    /**
     * @var DeltaSpecsParser
     */
    protected $parserObject;

    public function testGetters()
    {
        $path = 'tests/files/load/specs';

        $result = $this->parserObject->parse(iconv('Windows-1252', 'UTF-8//IGNORE', file_get_contents($path)));
        $this->assertEquals($result['CHARACTER_TYPES'], $this->parserObject->getCharacterTypes());
        $this->assertEquals($result['DEPENDENT_CHARACTERS'], $this->parserObject->getDependentChars());
        $this->assertEquals($result['NUMBERS_OF_STATES'], $this->parserObject->getNumberOfCharacterStates());
        $this->assertEquals($result['MANDATORY_CHARACTERS'], $this->parserObject->getMandatoryCharacters());
        $this->assertEquals($result['IMPLICIT_VALUES'], $this->parserObject->getImplicitValues());
    }

    protected function setUp():void
    {
        $this->parserObject = new DeltaSpecsParser();
    }
}
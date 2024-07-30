<?php

namespace Tests\Unit;

use Gloord\DeltaParser\Parser\CharacterParser;
use Gloord\DeltaParser\Parser\DeltaSpecs;
use Gloord\DeltaParser\Parser\Parser;
use PHPUnit\Framework\TestCase;

class AbstractParserTest extends TestCase
{
    /**
     * @var CharacterParser
     */
    protected $mockedInstance;


    protected function setUp(): void
    {
        $this->mockedInstance = new CharacterParser(new DeltaSpecs([]));
    }

    public function testReplaceMarkup()
    {
        $string = '\i{}italic\i0{} \sub{}superscript\nosupersub{} \b{}bold\b0{} \lquotebetween quotes\rquote';

        $result = $this->mockedInstance->replaceMarkup($string);

        $this->assertEquals('<i>italic</i> <sup>superscript</sup> bold ‘between quotes’', $result);
    }

    public function testRemoveBlanks()
    {
        $string = 'test consecutive   blanks     removal';

        $this->assertEquals('test consecutive blanks removal',
            $this->mockedInstance->removeBlanks($string));
    }
}
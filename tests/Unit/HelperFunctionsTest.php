<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\HelperFunctions;
use PHPUnit\Framework\TestCase;

class HelperFunctionsTest extends TestCase
{
    use HelperFunctions;

    /**
     * @var HelperFunctions
     */
    protected $traitObject;

    protected function setUp()
    {
        $this->traitObject = $this->getMockForTrait(HelperFunctions::class,[],'HelperFunctions');
    }

    public function testChunkMapFunctionGetsExpectedResult()
    {
        $string = 't <es <t> this> result';
        $result = $this->traitObject->mapChunks($string, ['<', '>']);

        $expected = [
            ['level' => 1, 'start' => 6, 'end' => 8,],
            ['level' => 0, 'start' => 2, 'end' => 14,],
        ];

        $this->assertEquals($expected, $result);
    }

    public function testChunkMapFunctionEmptyDelimiterResult()
    {
        $string = 'this is a mockup string';
        $result = $this->traitObject->mapChunks($string, ['', '']);

        $this->assertEquals([], $result);
    }

    public function testChunkMapFunctionNoMatchResult()
    {
        $string = 'this is a mockup string';
        $result = $this->traitObject->mapChunks($string, ['[', ']']);

        $this->assertEquals([], $result);
    }

    public function testSplitSpecialSplitSimpleFunction()
    {
        $string = 'This is, just a <test <with, nested> tokens, and> sample <nested, values> the, text';
        $result = $this->traitObject->splitSpecial($string, ',', [['<', '>']]);

        $expected = [
            'This is',
            ' just a <test <with, nested> tokens, and> sample <nested, values> the',
            ' text',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSplitSpecialDeeplyNestesNoSpliton()
    {
        //no spliting deeply nested
        $string = '<test, with> deeply <<<<nested>>>> text';
        $result = $this->traitObject->splitSpecial($string, ',', [['<', '>']]);

        $expected = [
            '<test, with> deeply <<<<nested>>>> text',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSplitSpecialNoContainerMarks()
    {
        //no container marks
        $string = 'Text with, no container marks';
        $result = $this->traitObject->splitSpecial($string, ',', [['<', '>']]);

        $expected = [
            'Text with',
            ' no container marks',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSplitSpecialEdgeCase()
    {
        $string = 'Text with <,> no container marks edge case,';
        $result = $this->traitObject->splitSpecial($string, ',', [['<', '>']]);

        $expected = [
            'Text with <,> no container marks edge case',
            '',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSplitSpecialWithMultiContainerMarkers()
    {
        $string = 'Text with <some, tags> and, some {other, tags} test';
        $result = $this->traitObject->splitSpecial($string, ',', [['<', '>'], ['{', '}']]);

        $expected = [
            'Text with <some, tags> and',
            ' some {other, tags} test',
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSplitSpecialChainedContainerMarks()
    {
        $string = 'Text with <{some, tags}> and, some other, tags test';
        $result = $this->traitObject->splitSpecial($string, ',', [['<{', '}>'], ['{<', '>}']]);

        $expected = [
            'Text with <{some, tags}> and',
            ' some other',
            ' tags test'
        ];

        $this->assertEquals($expected, $result);
    }

    public function testSplitSpecialWithUTF8()
    {
        $string = 'Text with <{someä, tags}> änd, some öther, tags test';
        $result = $this->traitObject->splitSpecial($string, ',', [['<{', '}>'], ['{<', '>}']]);

        $expected = [
            'Text with <{someä, tags}> änd',
            ' some öther',
            ' tags test'
        ];

        $this->assertEquals($expected, $result);
    }


    public function testSplitMatchReturnsArrayWithString()
    {
        $string = 'Text without tags test';
        $result = $this->traitObject->splitSpecial($string, ',', [['<', '>']]);

        $expected = [
            'Text without tags test'
        ];

        $this->assertEquals($expected, $result);
    }
}
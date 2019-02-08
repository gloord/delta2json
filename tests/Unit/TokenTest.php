<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\Token;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{

    /**
     * @var Token
     */
    protected $tokenInstance;

    protected function setUp()
    {
        $this->tokenInstance = new Token(Token::T_NONE, 0);
    }

    public function testGetStartPosition()
    {
        $this->assertEquals($this->tokenInstance->getStartPosition(), 0);
        $this->assertEquals(Token::T_NONE, $this->tokenInstance->getType());
    }

    public function testAddData()
    {
        $this->tokenInstance->addData('test');
        $this->assertEquals('test', $this->tokenInstance->getData());
    }

    public function testSetGetType()
    {
        $this->tokenInstance->setType(Token::T_ITEM);
        $this->assertEquals(Token::T_ITEM, $this->tokenInstance->getType());
    }

    public function testSetTypeIfNone()
    {
        $this->tokenInstance->setTypeIfNone(Token::T_CHARACTER_DESC);
        $this->assertEquals(Token::T_CHARACTER_DESC, $this->tokenInstance->getType());
    }

    public function testTypeNoneOr()
    {
        $this->tokenInstance->setType(Token::T_ITEM);
        $this->assertEquals(true, $this->tokenInstance->isTypeNoneOr(Token::T_ITEM));
        $this->assertEquals(false, $this->tokenInstance->isTypeNoneOr(Token::T_NONE));
    }

    public function testStaticGetName()
    {
        $this->assertEquals('T_NONE', Token::getName(0));
        $this->assertEquals('T_CHARACTER_DESC', Token::getName(1));
        $this->assertEquals('T_STATES', Token::getName(2));
        $this->assertEquals('T_ITEM', Token::getName(3));
        $this->assertEquals('T_ITEM_C_STATE', Token::getName(4));
    }

    public function testGetNameUnknown()
    {
        $this->assertEquals('UNKNOWN_TOKEN', Token::getName(10));
    }
}
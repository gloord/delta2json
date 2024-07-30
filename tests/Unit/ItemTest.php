<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\Item;
use PHPUnit\Framework\TestCase;

class ItemTest extends TestCase
{
    /**
     * @var Item
     */
    protected $itemInstance;

    protected function setUp(): void
    {
        $this->itemInstance = new Item();
    }

    public function testAddGetItemCharacterState()
    {
        $this->itemInstance->addItemCharacterState('1/2/3');
        $this->assertEquals([0 => '1/2/3'], $this->itemInstance->getItemCharacterStates());
    }

    public function testSetGetName()
    {
        $this->itemInstance->setName('testname');
        $this->assertEquals('testname', $this->itemInstance->getName());
    }

    public function testSetGetItemId()
    {
        $this->itemInstance->setItemId(42);
        $this->assertEquals(42, $this->itemInstance->getItemId());
    }

    public function testJsonSerialize()
    {
        $this->itemInstance->addItemCharacterState('1/2/3');
        $this->itemInstance->setName('testname');
        $this->itemInstance->setItemId(42);
        $this->assertEquals([
            'itemId'   => 42,
            'name' => 'testname',
            'characterStates' => [0 => '1/2/3'],
        ], $this->itemInstance->jsonSerialize());
    }
}
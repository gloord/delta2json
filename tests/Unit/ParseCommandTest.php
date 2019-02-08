<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Command\ParseCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class ParseCommandTest extends TestCase
{
    /**
     * @var CommandTester
     */
    protected $commandTester;

    protected function setUp()
    {
        $application = new Application();
        $application->add(new ParseCommand());
        $command = $application->find('parse');
        $this->commandTester = new CommandTester($command);
    }

    protected function tearDown()
    {
        $fileSystem = new Filesystem();

        $fileSystem->remove([
            'tests/files/specs.json',
            'tests/files/items.json',
            'tests/files/chars.json'
        ]);
    }

    public function testExecuteParseCommand()
    {
        $this->commandTester->execute(['filepath' => 'tests/files/load/', 'savepath' => 'tests/files/']);

        $this->assertJsonFileEqualsJsonFile('tests/files/expected/expected_specs', 'tests/files/specs.json');
        $this->assertJsonFileEqualsJsonFile('tests/files/expected/expected_chars', 'tests/files/chars.json');
        $this->assertJsonFileEqualsJsonFile('tests/files/expected/expected_items', 'tests/files/items.json');
    }

    public function testNonExistentPathDisplaysErrorMassage()
    {
        $this->commandTester->execute(['filepath' => 'tests/files/fake/']);

        $this->assertRegExp('/Specs file does not exist\!/', $this->commandTester->getDisplay());
    }
}
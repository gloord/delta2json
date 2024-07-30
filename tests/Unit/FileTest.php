<?php

namespace Tests\Unit;


use Gloord\DeltaParser\Parser\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /**
     * @var File
     */
    protected $fileInstance;

    protected function setUp(): void
    {
        $this->fileInstance = new File();
    }

    public function testNonExistentPathThrowsExceptionErrorMassage()
    {
        $this->expectExceptionMessage('Specs file does not exist!');
        $this->fileInstance->getFileContent('fake/path/to/file', 'specs');

        $this->expectExceptionMessage('Chars file does not exist!');
        $this->fileInstance->getFileContent('fake/path/to/file', 'chars');

        $this->expectExceptionMessage('Items file does not exist!');
        $this->fileInstance->getFileContent('fake/path/to/file', 'items');
    }
}
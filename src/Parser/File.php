<?php

namespace Gloord\DeltaParser\Parser;


use Symfony\Component\Filesystem\Filesystem;

class File
{
    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * File constructor.
     */
    public function __construct()
    {
        $this->fileSystem = new Filesystem();
    }

    /**
     * Load file content and convert it to UTF-8
     *
     * @param string $path
     * @param string $type
     * @return string
     * @throws \Exception
     */
    public function getFileContent(string $path, string $type)
    {
        if (!$this->fileSystem->exists($path)) {
            throw new \Exception(ucfirst($type) . ' file does not exist!');
        }

        return iconv('Windows-1252', 'UTF-8//IGNORE', file_get_contents($path));
    }

    /**
     * Save file content
     *
     * @param string $path
     * @param $data
     * @throws \Exception
     */
    public function saveFile(string $path, &$data, string $type)
    {
        if (!file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT))) {
            throw new \Exception('Storing parsed ' . ucfirst($type) . ' data failed');
        }
    }
}
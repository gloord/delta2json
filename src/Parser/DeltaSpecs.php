<?php

namespace Gloord\DeltaParser\Parser;


class DeltaSpecs
{
    /**
     * Simple array containing the parsed delta specs
     * @var array
     */
    protected $parsedSpecs;

    /**
     * DeltaSpecs constructor.
     * @param array $parsedSpecs
     */
    public function __construct(array $parsedSpecs)
    {
        $this->parsedSpecs = $parsedSpecs;
    }

    /**
     * @param $id
     * @return bool
     */
    public function getCharTypeByCharId($id)
    {
        if (array_key_exists($id, $this->parsedSpecs['CHARACTER_TYPES'])) {
            return $this->parsedSpecs['CHARACTER_TYPES'][$id]['characterType'];
        }
        
        return false;
    }

    /**
     * @param $key
     * @return bool|mixed
     */
    public function getSpecValue($key)
    {
        if (array_key_exists($key, $this->parsedSpecs)) {
            return $this->parsedSpecs[$key];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getParsedSpecs()
    {
        return $this->parsedSpecs;
    }

    /**
     * @param string $path
     * @param $data
     * @throws \Exception
     */
    public function saveParsedSpecArray(string $path, &$data)
    {
        if (!file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT))) {
            throw new \Exception('Storing specs array failed');
        }
    }

    /**
     * @param string $path
     * @throws \Exception
     */
    public function loadParsedSpecArray(string $path)
    {
        if(!file_exists($path)) {
            throw new \Exception('File does not exist');
        }

        $this->parsedSpecs = json_decode(file_get_contents($path));
    }
}
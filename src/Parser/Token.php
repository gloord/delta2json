<?php

namespace Gloord\DeltaParser\Parser;


class Token
{
    const T_NONE = 0;
    const T_CHARACTER_DESC = 1;
    const T_STATES = 2;
    const T_ITEM = 3;
    const T_ITEM_C_STATE = 4;

    /**
     * @var
     */
    protected $type;

    /**
     * @var null
     */
    protected $data = null;

    /**
     * @var
     */
    protected $startPos;

    /**
     * Token constructor
     *
     * @param $type
     * @param $startPos
     */
    public function __construct($type, $startPos)
    {
        $this->type = $type;
        $this->startPos = $startPos;
    }

    /**
     * @param $data
     */
    public function addData($data)
    {
        $this->data .= $data;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param $type
     */
    public function setTypeIfNone($type)
    {
        if ($this->type == self::T_NONE) {
            $this->type = $type;
        }
    }

    /**
     * @param $type
     * @return bool
     */
    public function isTypeNoneOr($type)
    {
        return ($this->type == self::T_NONE || $this->type == $type);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getStartPosition()
    {
        return $this->startPos;
    }

    /**
     * @param $token
     * @return false|int|string
     * @throws \ReflectionException
     */
    public static function getName($token)
    {
        $reflection = new \ReflectionClass(__CLASS__);
        $constants = $reflection->getConstants();
        $token_name = array_search($token, $constants);
        if ($token_name !== false) {
            return $token_name;
        }
        return 'UNKNOWN_TOKEN';
    }
}
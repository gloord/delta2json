<?php

namespace Gloord\DeltaParser\Parser;


use Symfony\Component\Console\Output\OutputInterface;


abstract class Parser
{
    /**
     * @var $tokens Token[]
     */
    protected $tokens = [];

    /**
     *
     * @var Token
     */
    protected $currentToken;

    /**
     *
     * @var DeltaSpecs
     */
    protected $deltaSpecs;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * markup => replace_value
     *
     * @var array
     */
    protected $specialMarkup = [
        '\i{}' => '<i>',
        '\i0{}' => '</i>',
        '\sub{}' => '<sup>',
        '\nosupersub{}' => '</sup>',
        '\b{}' => '',
        '\b0{}' => '',
        '\lquote' => '‘',
        '\rquote' => '’',
    ];

    /**
     * Parser constructor.
     * @param DeltaSpecs $deltaSpecs
     * @param OutputInterface|null $output
     */
    public function __construct(DeltaSpecs $deltaSpecs, OutputInterface $output = null)
    {
        $this->deltaSpecs = $deltaSpecs;
        $this->output = $output;
    }

    /**
     * Remove consecutive blanks
     *
     * @param $string
     * @return mixed
     */
    public function removeBlanks($string)
    {
        return preg_replace('/\s+/', ' ', trim($string));
    }

    /**
     * @param $i
     */
    protected function pushToken($i)
    {
        if ($this->currentToken->getData() === null) {
            return;
        }
        $this->tokens[] = $this->currentToken;
        $this->currentToken = new Token(Token::T_NONE, $i);
    }

    /**
     * Replace DELTA specific markup
     *
     * @param string $string
     * @return string
     */
    public function replaceMarkup($string)
    {
        foreach ($this->specialMarkup as $search => $replace) {
            $string = str_replace($search, $replace, $string);
        }

        return $string;
    }
}
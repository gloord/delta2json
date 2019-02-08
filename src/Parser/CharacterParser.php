<?php

namespace Gloord\DeltaParser\Parser;


class CharacterParser extends Parser
{
    /**
     * @var Character
     */
    protected $currentCharacter;

    /**
     * @var Character[];
     */
    protected $characters = [];

    /**
     * @param $string
     */
    public function parse($string)
    {
        $this->tokenize($string);
        $this->parseCharacters();
    }

    /**
     * @return array
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * @param $string
     */
    protected function tokenize($string)
    {
        $len = strlen($string);

        $this->currentToken = new Token(Token::T_NONE, 0);
        $i = 0;
        while ($i < $len) {
            $c = $string[$i];
            switch ($c) {

                case '#' :
                    //must be at the beginning of line or separated by a space

                    if ($string[$i - 1] == "\n" || $string[$i - 1] == ' ') {

                        $this->pushToken($i);

                        $this->currentToken->setType(Token::T_CHARACTER_DESC);
                    }
                    break;

                case '/' :
                    if ($i + 1 == $len || $string[$i + 1] == "\r") {
                        $this->pushToken($i);
                        $this->currentToken->setType(Token::T_STATES);

                        break;
                    }
                    $this->currentToken->addData($c);
                    break;

                case "\r":
                case "\n":
                case "\t":
                    break;

                default :
                    $this->currentToken->addData($c);
            }

            $i++;
            if ($i === $len) {
                $this->pushToken($i);
            }
        }
    }

    /**
     * Parse
     */
    protected function parseCharacters()
    {
        foreach ($this->tokens as $character) {

            switch ($character->getType()) {
                case 1:
                    $this->pushCharacter();
                    $string = preg_replace('/\s+/', ' '
                        , trim($character->getData()));
                    $this->parseCharacterDescription($string);
                    break;
                case 2:

                    $this->parseCharacterStates(preg_replace('/\s+/', ' '
                        , trim($character->getData())));
                    break;

                default:
                    break;
            }
        }
        //last
        $this->pushCharacter();
    }

    /**
     * Push to array
     */
    protected function pushCharacter()
    {
        if (!is_object($this->currentCharacter) ||
            $this->currentCharacter->getCharDescription() === null) {
            $this->currentCharacter = new Character();
            return;
        }
        $this->characters[] = $this->currentCharacter;

        $this->currentCharacter = new Character();
    }

    /**
     * Parse character description
     *
     * @param $string
     * @throws \Exception
     */
    protected function parseCharacterDescription($string)
    {
        if (preg_match('/^[\d]/', $string) === false) {
            throw new \Exception('Error in character parsing: '
                . 'No character number found');
        }
        $id = 0;
        preg_match('/^\d+(?=\.)/', $string, $id);
        $this->currentCharacter->setCharacterId((int)trim($id[0]));
        $charDesc = '';
        preg_match('/(?<=\d\.).*?$/s', $string, $charDesc);
        $this->currentCharacter->setCharDescription(
            $this->replaceMarkup(trim($charDesc[0]))
        );
        $this->setCharacterTypeFromSpec((int)trim($id[0]));
    }

    /**
     * Set character type from parsed specification
     *
     * @param int $id
     */
    protected function setCharacterTypeFromSpec($id)
    {
        $charType = $this->deltaSpecs->getCharTypeByCharId($id);
        if ($charType) {
            $this->currentCharacter->setCharType($charType);
        }
    }

    /**
     * Parse character states
     *
     * @param string $string
     */
    protected function parseCharacterStates($string)
    {
        //character unit
        if (preg_match('/^[1-9]/', trim($string)) === 0) {
            $this->currentCharacter->setUnit($string);
        } else {
            $id = 0;
            preg_match('/^\d+(?=\.)/', $string, $id);

            $stateDesc = '';
            preg_match('/(?<=\d\.).*?$/s', $string, $stateDesc);

            $charState = array(
                'id' => (int)trim($id[0]),
                'stateDesc' => $this->replaceMarkup(trim($stateDesc[0])),
            );
            $this->currentCharacter->addCharState($charState);
        }
    }
}
<?php

namespace Gloord\DeltaParser\Parser;


class DeltaSpecsParser
{
    /**
     * Parsed character types
     *
     * @var array
     */
    protected $characterTypes = [];

    /**
     * Parsed dependent characters
     *
     * @var array
     */
    protected $dependentChars = [];

    /**
     * Parsed number of character states
     * per character
     *
     * @var array
     */
    protected $numberOfCharacterStates = [];

    /**
     * Parsed mandatory characters
     *
     * @var array
     */
    protected $mandatoryCharacters = [];

    /**
     * Parsed implicit values
     *
     * @var array
     */
    protected $implicitValues = [];

    /**
     * Delta directives for extraction
     *
     * @var array
     */
    protected $directives = [
        'NUMBER OF CHARACTERS',
        'MAXIMUM NUMBER OF STATES',
        'MAXIMUM NUMBER OF ITEMS',
        'CHARACTER TYPES',
        'NUMBERS OF STATES',
        'IMPLICIT VALUES',
        'DEPENDENT CHARACTERS',
        'MANDATORY CHARACTERS',
    ];

    /**
     * Parse Delta specs file
     *
     * @param string $string
     * @return array
     * @throws \Exception
     */
    public function parse(string $string)
    {
        $contentArray = explode('*', $string);

        $parsedDirectives = $this->parseDirectives($contentArray);

        $this->parseCharacterTypes($parsedDirectives['CHARACTER_TYPES']);
        $this->parseDependentCharacters($parsedDirectives['DEPENDENT_CHARACTERS']);
        $this->numberOfCharacterStates = $this->parseGenericLine($parsedDirectives['NUMBERS_OF_STATES']);
        $this->parseMandatoryCharacters($parsedDirectives['MANDATORY_CHARACTERS']);
        //same format as numbers of states directive
        $this->implicitValues = $this->parseGenericLine($parsedDirectives['IMPLICIT_VALUES']
            , ['characterId', 'characterState']);

        //replace parsed characters
        $parsedDirectives['CHARACTER_TYPES'] = $this->characterTypes;

        //replace parsed dependent characters
        $parsedDirectives['DEPENDENT_CHARACTERS'] = $this->dependentChars;

        //replace number of character states
        $parsedDirectives['NUMBERS_OF_STATES'] = $this->numberOfCharacterStates;

        //replace mandatory characters
        $parsedDirectives['MANDATORY_CHARACTERS'] = $this->mandatoryCharacters;

        $parsedDirectives['IMPLICIT_VALUES'] = $this->implicitValues;

        return $parsedDirectives;
    }

    /**
     * @return array
     */
    public function getDependentChars()
    {
        return $this->dependentChars;
    }

    /**
     * @return array
     */
    public function getCharacterTypes()
    {
        return $this->characterTypes;
    }

    /**
     * @return array
     */
    public function getImplicitValues()
    {
        return $this->implicitValues;
    }

    /**
     * @return array
     */
    public function getNumberOfCharacterStates()
    {
        return $this->numberOfCharacterStates;
    }

    /**
     * @return array
     */
    public function getMandatoryCharacters()
    {
        return $this->mandatoryCharacters;
    }

    /**
     * Get specified Delta directives
     *
     * @param array $content
     * @return array
     * @throws \Exception
     */
    protected function parseDirectives(array &$content)
    {
        $result_array = [];

        foreach ($this->directives as $directive){
            $entries = preg_grep('/' . $directive . '/', $content);

            if (count($entries) > 1) {
                throw new \Exception('Parse error in specification file');
            }
            //store in array
            $result_array[str_replace(' ','_', $directive)] = trim(str_replace([$directive, "\r\n", "\r", "\n"],
                ['', ' '], array_values($entries)[0] ?? ''));
        }

        return $result_array;
    }

    /**
     * Character types
     *
     * @param $charTypeLine
     * @throws \Exception
     */
    protected function parseCharacterTypes(string &$charTypeLine)
    {
        $charTypeArray = preg_split('/\s/', $charTypeLine);

        $this->parseCharTypeLine($charTypeArray);
    }

    /**
     * Dependent characters
     *
     * @param $dependentCharsLine
     * @throws \Exception
     */
    protected function parseDependentCharacters(string &$dependentCharsLine)
    {
        $dependentCharsArray = preg_split('/\s/', $dependentCharsLine);

        if (!empty($dependentCharsArray)) {
            $this->parseDependentCharactersLine($dependentCharsArray);
        }
    }

    /**
     * Process char type entries
     *
     * @param $charArray
     * @throws \Exception
     */
    protected function parseCharTypeLine(array &$charArray)
    {
        foreach ($charArray as $value) {

            $helper = explode(',', $value);
            if (!isset($helper[0]) || !isset($helper[1])) {
                throw new \Exception('CharList: character type parsing error');
            }
            if (strpos($helper[0], '-') !== false) {

                $limit = explode('-', $helper[0]);

                for ($i = (int)$limit[0]; $i <= $limit[1]; $i++) {
                    $this->characterTypes[$i] = [
                        'characterId' => $i,
                        'characterType' => $helper[1]
                    ];
                }

            } else {
                $this->characterTypes[(int)$helper[0]] = [
                    'characterId' => (int)$helper[0],
                    'characterType' => $helper[1],
                ];
            }
        }
    }

    /**
     * Parse line of dependent characters
     *
     * @param $dependentCharsArray
     * @throws \Exception
     */
    protected function parseDependentCharactersLine(&$dependentCharsArray)
    {
        if (empty($dependentCharsArray)) {
            throw new \Exception('Error while parsing dependent chars');
        }

        foreach ($dependentCharsArray as $value) {
            $len = strlen($value);
            $i = 0;
            $buffer = '';
            $charId = null;
            $charState = null;

            while ($i < $len) {

                switch ($value[$i]) {
                    case ',':
                        $charId = (int)$buffer;
                        $buffer = '';
                        break;
                    case ':' :
                        if (is_null($charState)) {
                            $charState = (int)$buffer;
                            $buffer = '';
                        } else {
                            $buffer .= $value[$i];
                        }
                        break;

                    default:
                        $buffer .= $value[$i];
                        break;
                }

                $i++;
            }

            $this->parseDepChars($buffer, $charId, $charState);
        }
    }

    /**
     * Process dependent characters
     *
     * @param string $line
     * @param int $charId
     * @param $charState
     */
    protected function parseDepChars(string $line, int $charId, $charState)
    {
        $entries = explode(':', $line);

        $tempArray = [];

        foreach ($entries as $entry) {
            if (strpos($entry, '-') !== false) {
                $limit = explode('-', $entry);

                for ($i = (int)$limit[0]; $i <= $limit[1]; $i++) {
                    array_push($tempArray, $i);
                }

            } else {
                array_push($tempArray, (int)$entry);
            }
        }
        $this->dependentChars[] = [
            'characterId' => $charId,
            'characterState' => $charState,
            'dependentCharacter' => $tempArray,
        ];
    }

    /**
     * Parse generic character directive
     *
     * @param string $line
     * @param array $arrayKeys
     * @return array
     */
    protected function parseGenericLine(string $line, array $arrayKeys = ['characterId', 'numberOfCharacterStates'])
    {
        if (empty($line)) {
            return [];
        }

        $numberOfStates = preg_split('/\s/', preg_replace('/\s\s+/', ' ', $line));

        $result = [];
        $tempArray = [];

        foreach($numberOfStates as $numberOfState) {
            $tempArray = explode(',', $numberOfState);

            if (strpos($tempArray[0], '-') !== false) {
                $range = explode('-', $tempArray[0]);
                for ($i = (int) $range[0]; $i <= $range[1]; $i++) {
                    $result[] = [
                        $arrayKeys[0] => $i,
                        $arrayKeys[1] => (int) $tempArray[1],
                    ];
                }
            } else {
                $result[] = [
                    $arrayKeys[0] => (int) $tempArray[0],
                    $arrayKeys[1] => (int) $tempArray[1],
                ];
            }
        }

        return $result;
    }

    /**
     * @param string $line
     */
    protected function parseMandatoryCharacters(string $line)
    {
        if (empty($line)) {
            $this->dependentChars[] = [];
            return;
        }
        $tempArray = [];

        $mandatoryCharacters = preg_split('/\s/', preg_replace('/\s\s+/', ' ', $line));

        foreach ($mandatoryCharacters as $mandatoryCharacter) {
            if (strpos($mandatoryCharacter, '-') !== false) {
                $limit = explode('-', $mandatoryCharacter);
                for ($i = (int)$limit[0]; $i <= $limit[1]; $i++){
                    array_push($tempArray, $i);
                }
            } else {
                array_push($tempArray, (int)$mandatoryCharacter);
            }
        }

        $this->mandatoryCharacters = $tempArray;
    }
}
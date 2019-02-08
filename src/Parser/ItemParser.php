<?php

namespace Gloord\DeltaParser\Parser;


use Symfony\Component\Console\Helper\ProgressBar;


class ItemParser extends Parser
{
    use HelperFunctions;

    /**
     * @var array
     */
    protected $items = [];

    /**
     *
     * @var Item
     */
    protected $currentItem;

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $string
     * @throws \Exception
     */
    public function parse($string)
    {
        $this->tokenize($string);

        $this->parseItems();
    }

    /**
     * @param $string
     */
    public function tokenize($string)
    {
        $len = strlen($string);

        $this->currentToken = new Token(Token::T_NONE, 0);
        $i = 0;

        while ($i < $len) {
            $c = $string[$i];
            switch ($c) {

                case '#' :
                    if ($string[$i - 1] == "\n" || $string[$i - 1] == ' ') {

                        $this->pushToken($i);
                        $this->currentToken->setType(Token::T_ITEM);
                    }

                    break;

                case '/' :
                    //echo "CHAR_END\n";
                    if ($i + 1 == $len || $string[$i + 1] == "\r" || $string[$i + 1] == ' ') {

                        $this->pushToken($i);
                        $this->currentToken->setType(Token::T_ITEM_C_STATE);

                        break;
                    }
                    $this->currentToken->addData($c);
                    break;

                case "\r":
                    //keep blank in TE states
                    $this->currentToken->addData(' ');
                    break;

                case "\t":
                case "\n":
                    break;

                default:
                    $this->currentToken->addData($c);
            }

            $i++;
            if ($i === $len) {
                $this->pushToken($i);
            }
        }
    }

    /**
     * Parse items
     */
    protected function parseItems()
    {
        $progressBar = new ProgressBar($this->output, count($this->tokens));

        // start and display the progress bar
        $progressBar->start();

        //check
        $checked = [];
        $currentType = 0;
        $index = 0;

        foreach ($this->tokens as $token) {
            $currentType = $this->tokens[$index]->getType();
            //after a character character states must follow
            if ($currentType == 3 && $this->tokens[$index + 1]->getType() != 4) {
                throw new \Exception('Item - character state '
                    . 'order messed up at position: ' . $index);
            }

            switch ($currentType) {
                case 3:
                    $checked[$index]['item'] = $this->tokens[$index];
                    break;
                case 4:
                    //set index to previous character and add state
                    $checked[$index - 1]['charState'] = $this->tokens[$index];
                    break;
                default:
                    break;
            }
            $index++;
            $progressBar->advance();
        }

        //sort
        usort($checked, array($this, 'compareItemString'));

        $id = 1;
        foreach ($checked as $item) {
            $this->pushItem();
            $this->parseItemDescription($item['item']->getData(), $id);
            $this->parseItemCharState($item['charState']->getData());
            $id++;
        }
        //last
        $this->pushItem();
        $progressBar->finish();
        $this->output->writeln(['']);
    }

    /**
     * Push item
     */
    protected function pushItem()
    {
        if (!is_object($this->currentItem) ||
            $this->currentItem->getName() === null) {
            $this->currentItem = new Item();
            return;
        }
        $this->items[] = $this->currentItem;

        $this->currentItem = new Item();
    }

    /**
     * Parse description
     *
     * @param string $string
     * @param int $id
     */
    protected function parseItemDescription($string, $id)
    {
        $this->currentItem->setItemId($id);

        $string = $this->removeBlanks($this->replaceMarkup($string));

        $this->currentItem->setName($string);
    }

    /**
     * Get all character states of an item
     *
     * @param string $string
     */
    protected function parseItemCharState($string)
    {
        $states = [];

        $pattern = '/(\d+(\,|<.*?>)(\d+(<.*?>))?.*?(?=\s\d+(\,|<.*?>)))/s';

        //split
        preg_match_all($pattern, $string . " 999999,END", $states);

        foreach ($states[0] as $state) {

            $characterState = $this->splitSpecial($state, ',', [['<', '>']]);

            $this->parseCharState($characterState);
        }
    }

    /**
     * @param array $characterState
     */
    protected function parseCharState(array &$characterState)
    {
        $characterArray = $this->parseCharacterId($characterState[0]);

        if (isset($characterState[1])) {
            $characterArray['characterState'] = $this->replaceMarkup($characterState[1]);
        }

        $this->currentItem->addItemCharacterState($characterArray);
    }

    /**
     * Parse character id
     *
     * @param $string
     * @return array
     */
    public function parseCharacterId($string)
    {
        $resultArray = [];

        //check text/comment
        $chunk = $this->mapChunks($string);

        if ($chunk) {
            $resultArray['characterId'] = (int) trim(substr($string, 0, $chunk[0]['start']));
            $resultArray['characterText'] = $this->replaceMarkup(trim(substr($string, $chunk[0]['start'])));
        } else {
            $resultArray['characterId'] = (int) $string;
        }

        return $resultArray;
    }

    /**
     * Sort array
     *
     * @param $a
     * @param $b
     * @return int
     */
    protected function compareItemString($a, $b)
    {
        return strcmp($a['item']->getData(), $b['item']->getData());
    }
}
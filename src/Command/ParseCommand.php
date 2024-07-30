<?php

namespace Gloord\DeltaParser\Command;


use Gloord\DeltaParser\Parser\CharacterParser;
use Gloord\DeltaParser\Parser\DeltaSpecs;
use Gloord\DeltaParser\Parser\DeltaSpecsParser;
use Gloord\DeltaParser\Parser\File;
use Gloord\DeltaParser\Parser\ItemParser;
use Symfony\Component\Console\Command\Command as SymfonyConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCommand extends SymfonyConsoleCommand
{
    /**
     * @var DeltaSpecs
     */
    protected $specsObject;

    private $file;

    public function __construct()
    {
        parent::__construct();

        $this->file = new File();
    }

    protected function configure()
    {
        //command name
        $this->setName('parse');

        //path to files argument
        $this->addArgument('filepath',  InputArgument::REQUIRED,
            'Path to specs, chars and items files, e.g. /foo/bar/');
        //path to output folder argument (default false)
        $this->addArgument('savepath',  InputArgument::OPTIONAL,
            'Path where output files should be stored, e.g. /foo/bar/', false);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $formatter = $this->getHelper('formatter');

        try {
            $this->readSpecs($input, $output);
            $this->readCharacters($input, $output);
            $this->readItems($input, $output);
            return 0;
        } catch (\Exception $e) {
            $formattedBlock = $formatter->formatBlock($e->getMessage(), 'error');
            $output->writeln([
                $formattedBlock,
            ]);
            return 1;
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function readSpecs(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Reading specs file...'
        ]);

        $specsParser = new DeltaSpecsParser();

        $values = $specsParser->parse($this->file->getFileContent($input->getArgument('filepath') . 'specs', 'specs'));

        $this->specsObject = new DeltaSpecs($values);

        $output->writeln([
            'Storing parsed specs...'
        ]);

        $this->file->saveFile(
            ($input->getArgument('savepath')?$input->getArgument('savepath'):$input->getArgument('filepath'))
            . 'specs.json', $values, 'chars');
        $output->writeln([
            'Done.'
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function readCharacters(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Parsing characters...'
        ]);

        $string = $this->file->getFileContent($input->getArgument('filepath') . 'chars', 'chars');

        $characterParser = new CharacterParser($this->specsObject);

        $characterParser->parse($string);

        $characters = $characterParser->getCharacters();

        $totalNumberParsedCharacter = count($characters);

        $output->writeln([
            'Storing parsed characters...'
        ]);

        $this->file->saveFile(
            ($input->getArgument('savepath')?$input->getArgument('savepath'):$input->getArgument('filepath'))
            . 'chars.json', $characters, 'chars');

        $output->writeln([
            'Done. Parsed '. $totalNumberParsedCharacter . ' characters...',
            'Specs file listed ' . $this->specsObject->getSpecValue('NUMBER_OF_CHARACTERS'),
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    protected function readItems(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Parsing item tokens...'
        ]);

        $string = $this->file->getFileContent($input->getArgument('filepath') . 'items', 'items');

        $parser = new ItemParser($this->specsObject, $output);
        $parser->parse($string);

        $items = $parser->getItems();

        $output->writeln([
            'Storing parsed items...'
        ]);

        $this->file->saveFile(
            ($input->getArgument('savepath')?$input->getArgument('savepath'):$input->getArgument('filepath'))
            . 'items.json',$items, 'items');

        $totalNumberParsedItems = count($items);
        $output->writeln([
            'Done. Parsed '. $totalNumberParsedItems . ' items...',
            'Specs file listed ' . $this->specsObject->getSpecValue('MAXIMUM_NUMBER_OF_ITEMS'),
        ]);
    }
}
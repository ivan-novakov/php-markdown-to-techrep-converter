<?php

namespace InoMdReport\Console\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;


class TidyXml extends Command
{

    /**
     * @var \tidy
     */
    protected $tidy;

    protected $tidyParseParams = array(
        'indent' => true,
        'input-xml' => true,
        'input-encoding' => 'utf8',
        'output-encoding' => 'utf8',
        'wrap' => 90
    );


    /**
     * @return \tidy
     */
    public function getTidy()
    {
        if (! $this->tidy instanceof \tidy) {
            $this->tidy = new \tidy();
        }
        return $this->tidy;
    }


    /**
     * @param \tidy $tidy
     */
    public function setTidy(\tidy $tidy)
    {
        $this->tidy = $tidy;
    }


    public function configure()
    {
        $this->setName('tidyxml')
            ->setDescription('Formats XML in a readable way')
            ->addArgument('file', InputArgument::REQUIRED, 'Which file do you want to format?');
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlFile = $input->getArgument('file');
        if (! is_file($xmlFile) || ! is_readable($xmlFile)) {
            throw new \RuntimeException(sprintf("Cannot read file '%s'", $xmlFile));
        }
        
        $xmlString = file_get_contents($xmlFile);
        
        $this->getTidy()->parseString($xmlString, $this->tidyParseParams);
        
        $output->writeln((string) $this->getTidy());
    }
}
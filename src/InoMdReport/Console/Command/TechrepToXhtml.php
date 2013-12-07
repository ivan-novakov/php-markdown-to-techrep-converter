<?php

namespace InoMdReport\Console\Command;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use DomDocument;
use XSLTProcessor;


class TechrepToXhtml extends Command
{

    protected $xslFile;


    public function __construct($xslFile)
    {
        $this->xslFile = $xslFile;
        parent::__construct();
    }


    public function configure()
    {
        $this->setName('rep2xhtml')
            ->setDescription('Converts a Techrep2 document to XHTML')
            ->addArgument('file', InputArgument::REQUIRED);
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $xmlFile = $input->getArgument('file');
        
        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->load($xmlFile);
        
        $xsl = new DOMDocument('1.0', 'utf-8');
        
        $xsl->load($this->xslFile);
        
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
        
        $xhtml = $proc->transformToXml($xml);
        
        $output->writeln($xhtml);
    }
}
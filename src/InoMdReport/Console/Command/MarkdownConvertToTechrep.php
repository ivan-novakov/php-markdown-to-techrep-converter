<?php

namespace InoMdReport\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Command\Command;
use InoMdReport\Service;


class MarkdownConvertToTechrep extends Command
{

    /**
     * @var Service\Report
     */
    protected $reportService;


    /**
     * @return Service\Report
     */
    public function getService()
    {
        if (! $this->reportService instanceof Service\Report) {
            $this->reportService = new Service\Report();
        }
        return $this->reportService;
    }


    /**
     * @param Service\Report $service
     */
    public function setService(Service\Report $service)
    {
        $this->reportService = $service;
    }


    protected function configure()
    {
        $this->setName('md2rep')
            ->setDescription('Converts a markdown document to the Techrep2 format')
            ->addArgument('file', InputArgument::REQUIRED, 'Which file do you want to convert?')
            ->addOption('metadata', 'm', InputOption::VALUE_OPTIONAL);
    }


    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $metadataFile = $input->getOption('metadata');
        $metadata = array();
        if ($metadataFile) {
            if (! is_file($metadataFile) || ! is_readable($metadataFile)) {
                throw new \InvalidArgumentException(sprintf("Cannot read file '%s'", $metadataFile));
            }
            
            $metadata = require $metadataFile;
        }
        
        $markdown = $input->getArgument('file');
        $xml = $this->getService()->convertMarkdownToTechrep($markdown, $metadata);
        
        $output->writeln($xml);
    }
}
#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use InoMdReport\Console\Command\MarkdownConvertToTechrep;
use InoMdReport\Console\Command\TidyXml;
use InoMdReport\Console\Command\TechrepToXhtml;

require __DIR__ . '/../vendor/autoload.php';

$application = new Application('Markdown 2 Techrep Console Tool');
$application->add(new MarkdownConvertToTechrep());
$application->add(new TidyXml());
$application->add(new TechrepToXhtml(__DIR__ . '/../xsl/trtoxhtml.xsl'));

$application->run();
#!/usr/bin/env php
<?php

use Sundown\Render;
use Sundown\Markdown;

$mdFile = __DIR__ . '/test.md';
$mdString = file_get_contents($mdFile);

$render = new Render\HTML();
$config = array();

$md = new Markdown($render, $config);
echo $md->render($mdString);

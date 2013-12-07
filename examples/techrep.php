#!/usr/bin/env php
<?php

use InoMdReport\Sundown\Render;
use Sundown\Markdown;

require __DIR__ . '/../vendor/autoload.php';

$renderConfig = array();
$metadata = array(
    'header' => array(
        'authors' => array(
            'Ivan Novakov'
        ),
        'date' => '01.12.2013',
        'keywords' => 'Shibboleth, Attribute Authority, SAML',
        'abstract' => 'Abstract para...'
    ),
    'biblist' => array(
        'specs-samlcore' => 'http://docs.oasis-open.org/security/saml/v2.0/saml-core-2.0-os.pdf',
        'wikipedia-saml' => 'https://en.wikipedia.org/wiki/Security_Assertion_Markup_Language',
        'shib-nameidattr' => 'https://wiki.shibboleth.net/confluence/display/SHIB2/NameIDAttributes',
        'shib-sharedpc' => 'https://wiki.shibboleth.net/confluence/display/SHIB2/DirectPrincipalConnector',
        'shib-spattrresolv' => 'https://wiki.shibboleth.net/confluence/display/SHIB2/NativeSPAttributeResolver',
        'feide-aa' => 'https://rnd.feide.no/2009/08/24/sp-centric_attribute_aggregation/'
    )
);
$render = new Render\Techrep($renderConfig, $metadata);

// $mdFile = __DIR__ . '/article.md';
$mdFile = $_SERVER['argv'][1];

$mdString = file_get_contents($mdFile);
$config = array(
    'fenced_code_blocks' => true
);

$md = new Markdown($render, $config);
$xml = $md->render($mdString);
// $xml = tidyXml($xml);

// echo "---------------------------\n";
echo $xml . "\n";
// echo "---------------------------\n";

// ---
function tidyXml($value)
{
    $tidy = new tidy();
    $t = $tidy->parseString($value, array(
        'indent' => true,
        'input-xml' => true,
        'input-encoding' => 'utf8',
        'output-encoding' => 'utf8',
        'wrap' => 90
    ));
    return (string) $tidy;
}


function _dump($value)
{
    error_log(print_r($value, true));
}
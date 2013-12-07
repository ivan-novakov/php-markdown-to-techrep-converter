<?php

namespace InoMdReport\Service;

use Symfony\Component\Console\Descriptor\MarkdownDescriptor;
use Sundown\Markdown;
use InoMdReport\Sundown\Render;


class Report
{

    protected $parserConfig = array(
        'fenced_code_blocks' => true
    );

    protected $renderConfig = array();


    /**
     * @return Markdown
     */
    public function initMarkdownParser(array $metadata = array(), array $parserConfig = null, array $renderConfig = null)
    {
        if ($parserConfig === null) {
            $parserConfig = $this->parserConfig;
        }
        
        if ($renderConfig === null) {
            $renderConfig = $this->renderConfig;
        }
        
        $render = new Render\Techrep($renderConfig, $metadata);
        $parser = new Markdown($render, $parserConfig);
        
        return $parser;
    }


    /**
     * @param Markdown $markdownParser
     */
    public function setMarkdownParser(Markdown $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }


    public function convertMarkdownToTechrep($markdown, array $metadata = array())
    {
        $parser = $this->initMarkdownParser($metadata);
        if (is_file($markdown)) {
            $markdown = file_get_contents($markdown);
        }
        
        return $parser->render($markdown);
    }
}
<?php

namespace InoMdReport\Sundown\Render;

use Sundown\Render\Base;


class Techrep extends Base
{

    const TAG_REPORT = 'report';

    const TAG_TITLE = 'title';

    const TAG_BODY = 'body';

    const TAG_H1 = 'h1';

    const TAG_H2 = 'h2';

    const TAG_H3 = 'h3';

    const TAG_A = 'a';

    const TAG_PRE = 'pre';

    const TAG_EM = 'em';

    const TAG_TT = 'tt';

    const TAG_OL = 'ol';

    const TAG_UL = 'ul';

    const TAG_LI = 'li';

    const TAG_STRONG = 'strong';

    const TAG_SOURCE = 'source';

    const TAG_IMAGE = 'image';

    const TAG_FIGURE = 'figure';

    const TAG_CAPTION = 'caption';

    const TAG_AUTHORS = 'authors';

    const TAG_AUTHOR = 'author';

    const TAG_DATE = 'date';

    const TAG_KEYWORDS = 'keywords';

    const TAG_ABSTRACT = 'abstract';

    const TAG_BIBLIST = 'biblist';

    const TAG_BIBITEM = 'bibitem';

    protected $xmlNamespace = 'http://cesnet.cz/ns/techrep/base/2.0';

    protected $repository;

    protected $metadata;


    public function __construct(array $config = array(), array $metadata = array())
    {
        parent::__construct($config);
        $this->repository = new \ArrayObject(array());
        $this->metadata = $metadata;
    }


    public function postprocess($bodyContent)
    {
        $header = $this->renderHeader();
        $bodyContent .= $this->renderBiblist();
        
        $body = $this->renderTag(self::TAG_BODY, $bodyContent);
        
        $content = $header . $body;
        
        return $this->renderTag(self::TAG_REPORT, $content, array(
            'xmlns' => $this->xmlNamespace
        ));
    }


    public function header($text, $level)
    {
        $level = intval($level);
        if ($level == 1) {
            $this->store('title', $text);
            return;
        }
        
        $tag = null;
        switch ($level) {
            case 2:
                $tag = self::TAG_H1;
                break;
            case 3:
                $tag = self::TAG_H2;
                break;
            case 4:
                $tag = self::TAG_H3;
                break;
            default:
                break;
        }
        
        if (null !== $tag) {
            $text = $this->renderTag($tag, $text);
        }
        return $text;
    }


    public function paragraph($text)
    {
        return $this->renderTag('p', $text);
    }


    public function blockCode($code, $language)
    {
        $code = sprintf("<![CDATA[%s]]>", $code);
        return $this->renderTag(self::TAG_PRE, $code, array(
            'language' => $language
        ));
    }


    public function codespan($code)
    {
        return $this->renderTag(self::TAG_TT, $code);
    }


    public function emphasis($text)
    {
        return $this->renderTag(self::TAG_EM, $text);
    }


    public function doubleEmphasis($text)
    {
        return $this->renderTag(self::TAG_STRONG, $text);
    }


    /**
     * Renders a link.
     * 
     * @param string $link
     * @param string $title
     * @param string $content
     */
    public function link($link, $title = '', $content = '')
    {
        if ('' === $content) {
            $content = $link;
        }
        
        return $this->renderTag(self::TAG_A, $content, array(
            'href' => $link
        ));
    }


    public function image($link, $title = '', $altText = '')
    {
        $format = strtoupper(substr($link, strrpos($link, '.') + 1));
        
        $source = $this->renderTag(self::TAG_SOURCE, '', array(
            'format' => $format,
            'file' => $link
        ));
        
        $image = $this->renderTag(self::TAG_IMAGE, $source);
        $caption = $this->renderTag(self::TAG_CAPTION, $altText);
        $figure = $this->renderTag(self::TAG_FIGURE, $image . $caption, array(
            'xml:id' => $link
        ));
        
        return $figure;
    }


    public function listBox($contents, $listType)
    {
        $tag = null;
        switch ($listType) {
            case 9:
                $tag = self::TAG_OL;
                break;
            case 8:
            default:
                $tag = self::TAG_UL;
                break;
        }
        
        return $this->renderTag($tag, $contents);
    }


    public function listItem($text, $listType)
    {
        return $this->renderTag(self::TAG_LI, $text);
    }
    
    /*
     * 
     */
    protected function renderHeader()
    {
        $header = '';
        $header .= $this->renderTitle();
        $header .= $this->renderAuthors();
        $header .= $this->renderDate();
        $header .= $this->renderKeywords();
        $header .= $this->renderAbstract();
        
        return $header;
    }


    protected function renderTitle()
    {
        $title = $this->fetch('title');
        return $this->renderTag(self::TAG_TITLE, $title);
    }


    protected function renderAuthors()
    {
        $output = '';
        
        $authors = $this->getHeaderMetadata('authors');
        if (is_array($authors)) {
            foreach ($authors as $author) {
                $output .= $this->renderTag(self::TAG_AUTHOR, $author);
            }
        }
        
        return $this->renderTag(self::TAG_AUTHORS, $output);
    }


    protected function renderDate()
    {
        $date = $this->getHeaderMetadata('date');
        return $this->renderTag(self::TAG_DATE, $date);
    }


    protected function renderKeywords()
    {
        $keywords = $this->getHeaderMetadata('keywords');
        return $this->renderTag(self::TAG_KEYWORDS, $keywords);
    }


    protected function renderAbstract()
    {
        $abstract = $this->getHeaderMetadata('abstract');
        return $this->renderTag(self::TAG_ABSTRACT, $abstract);
    }


    protected function renderBiblist()
    {
        $biblist = '';
        
        $biblistItems = $this->getBiblistMetadata();
        if (! $biblistItems) {
            return $biblist;
        }
        
        $biblistElements = '';
        foreach ($biblistItems as $id => $item) {
            $biblistElements .= $this->renderTag(self::TAG_BIBITEM, $item, array(
                'xml:id' => $id
            ));
        }
        
        if ($biblistElements) {
            $biblist = $this->renderTag(self::TAG_BIBLIST, $biblistElements);
        }
        
        return $biblist;
    }


    protected function renderTag($name, $content = '', array $attributes = array())
    {
        $attributeString = '';
        foreach ($attributes as $attrName => $attrValue) {
            $attributeString .= sprintf(" %s=\"%s\"", $attrName, $attrValue);
        }
        
        return sprintf("<%s%s>%s</%s>", $name, $attributeString, $content, $name);
    }


    protected function getHeaderMetadata($index)
    {
        if (isset($this->metadata['header'][$index])) {
            return $this->metadata['header'][$index];
        }
        
        return null;
    }


    protected function getBiblistMetadata()
    {
        if (isset($this->metadata['biblist'])) {
            return $this->metadata['biblist'];
        }
        
        return null;
    }


    protected function store($key, $content)
    {
        $this->repository->offsetSet($key, $content);
    }


    protected function fetch($key)
    {
        if ($this->repository->offsetExists($key)) {
            return $this->repository->offsetGet($key);
        }
        
        return null;
    }
}
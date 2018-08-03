<?php

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Indexer\DefaultIndexer;
use Smartindex\Utils\HtmlHelper;
use Smartindex\Utils\IndexTools;
use Smartindex\Renderer\iIndexRenderer;
use Smartindex\Renderer\iRenderer;
use Smartindex\Indexer\iIndexer;

class TreeIndexRenderer implements iIndexRenderer {
    private $config;
    private $index;
    
    private $hasFiles = array();

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function render(&$document) {
        $indexer = new DefaultIndexer($this->config);
        $this->index = $indexer->getIndex();

        $this->buildStructure($this->config->getAttribute('namespace'), $document, 1);
    }
    
    private function buildStructure($namespace, &$document, $level) {
        if ( ! array_key_exists($namespace, $this->index))
            return "";
        
        $this->hasFiles[$level] = !empty($this->index[$namespace][iIndexer::KEY_PAGES]);
        
        $document .= "<ul>";
        
        foreach($this->index[$namespace][iIndexer::KEY_DIRS] as $ns)  {
            $classes = array(self::CLASS_NAMESPACE);
            if (($this->config->openDepth >= $level) || IndexTools::isPathPart($this->config->followPath, IndexTools::constructPageName($namespace, $ns))) {
                $classes[] = self::CLASS_OPEN;
            } else {
                $classes[] = self::CLASS_CLOSED;
            }
                
            $document .= "<li".HtmlHelper::createIdClassesPart(NULL, $classes)."><div>";
            
            for ($i = 1; $i < $level; $i++) {
                $document .= "<span class=\"".(($this->hasFiles[$i])?"line":"noline")."\"></span>";
            }
            
            $document .= "<span class=\"coll\"></span><span class=\"folder\"></span>"
                        .HtmlHelper::createSitemapLink(IndexTools::constructPageName($namespace, $ns), $ns)
                        ."</div>";
            
            $this->buildStructure($this->index, IndexTools::constructPageName($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($this->index[$namespace][iIndexer::KEY_PAGES] as $page) {
            $heading = p_get_first_heading(IndexTools::constructPageName($namespace, $page), false);
            if ($heading == "")
                $heading = $page;
            $document .= "<li".HtmlHelper::createIdClassesPart(NULL, array(self::CLASS_PAGE))."><div>";
            
            for ($i = 1; $i < $level; $i++) {
                $document .= "<span class=\"".(($this->hasFiles[$i])?"line":"noline")."\"></span>";
            }

            $document .= "<span class=\"cross\"></span><span class=\"file\"></span>"
                         .HtmlHelper::createInternalLink(IndexTools::constructPageName($namespace, $page), NULL, $heading, NULL, NULL)
                         ."</div ></li>";
        }
        
        $document .= "</ul>";
        
    }

}

<?php

use Smartindex\Configuration\IndexConfiguration;
use Smartindex\Indexer\DefaultIndexer;
use Smartindex\Utils\HtmlHelper;
use Smartindex\Utils\IndexTools;

class TreeRenderer implements iIndexRenderer {
    const CLASS_OPEN = "open";
    const CLASS_CLOSED = "closed";
    const CLASS_NAMESPACE = "namespace";
    const CLASS_PAGE = "page";
    
    private $useWrapper = true;
    private $wrapperClasses = array();
    private $wrapperId;
    
    private $config;
    
    private $hasFiles = array();

    public function __construct(IndexConfiguration $config) {
        $this->config = $config;
    }
    
    public function setWrapper($useWrapper, $id = NULL) {
        $this->useWrapper = $useWrapper;
        
        $this->wrapperClasses[] = IndexConfiguration::TREE_CLASS;
        $this->wrapperClasses[] = $this->config->cssClass;
        if ($this->config->highlite) {
            $this->wrapperClasses[] = IndexConfiguration::HIGHLIGHT_CLASS;
        }
        
        $this->wrapperId = $id;
    }
    
    public function render($data, &$document) {
        if ($this->useWrapper) {
            $document .= "<div".HtmlHelper::createIdClassesPart($this->wrapperId, $this->wrapperClasses).">";
        }
        
        //$document .= HtmlHelper::createHiddenInput("{'name' : 'John'}", "neco");
       
        $this->basicData = $data;
        $this->buildStructure($data, $this->config->namespace, $document, 1);
        
        if ($this->useWrapper) {
            $document .= "</div>";
        }
    }
    
    private function buildStructure($data, $namespace, &$document, $level) {
        if (!array_key_exists($namespace, $data))
                return "";
        
        
        $this->hasFiles[$level] = !empty($data[$namespace][iIndexer::KEY_PAGES]);
        
        $document .= "<ul>";
        
        foreach($data[$namespace][iIndexer::KEY_DIRS] as $ns)  {
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
            
            $this->buildStructure($data, IndexTools::constructPageName($namespace, $ns), $document, $level+1);
            $document .= "</li>";
        }
        
        
        foreach($data[$namespace][iIndexer::KEY_PAGES] as $page) {
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

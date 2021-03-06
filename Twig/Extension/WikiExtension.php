<?php

/*
 * This file is part of WikiBundle
 *
 * (c) Christian Hoffmeister <choffmeister.github@googlemail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thekwasti\WikiBundle\Twig\Extension;

use Thekwasti\WikiBundle\Parser;
use Thekwasti\WikiBundle\Tree\Document;
use Symfony\Component\DependencyInjection\Container;

/**
 * WikiExtension
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
class WikiExtension extends \Twig_Extension
{
    private $container;
    private $renderer;
    
    public function __construct(Container $container, array $renderer)
    {
        $this->container = $container;
        $this->renderer = $renderer;
    }
    
    public function renderWiki($markup, $currentWiki = null, $renderer = 'Xhtml')
    {
        $parser = new Parser();
        $doc = $parser->parse($markup);
        return $this->renderer[$renderer]->render($doc, $currentWiki);
    }
    
    public function renderWikiPrecompiled($precompiled, $currentWiki = null, $renderer = 'Xhtml')
    {
        if (!$precompiled instanceof Document) {
            $precompiled = serialize($precompiled);
        }

        return $this->renderer[$renderer]->render($precompiled, $currentWiki);
    }
    
    public function getFunctions()
    {
        return array(
            'wiki' => new \Twig_Function_Method($this, 'renderWiki', array('is_safe' => array('html'))),
        	'wiki_pc' => new \Twig_Function_Method($this, 'renderWikiPrecompiled', array('is_safe' => array('html'))),
        );
    }    
    
    public function getName()
    {
        return 'wiki';
    }
}

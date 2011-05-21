<?php

namespace Thekwasti\WikiBundle\Tests;

use Thekwasti\WikiBundle\Parser;
use Thekwasti\WikiBundle\UrlGenerator;
use Thekwasti\WikiBundle\Renderer\LatexRenderer;
use Symfony\Component\Process\Process;

class PdflatexFuzyTest extends \PHPUnit_Framework_TestCase
{
    protected $tempName;
    protected $snippets;
    protected $snippetCount;
    protected $alphabet;
    protected $alphabetSize;
    
    protected function setUp()
    {
        $process = new Process('pdflatex', '/tmp');
        
        if ($process->run() == 127) {
            $this->markTestSkipped('PDFLatex is not available.');
        }
    }
    
    public function __construct()
    {
        $this->tempName = 'thekwasti_wikibundle_pdflatex_fuzy_test';
        $this->alphabet = '';
        for ($i = 0; $i < 0xD800; $i++)
            $this->alphabet .= iconv('UCS-4LE', 'UTF-8', pack('V', $i));
        for ($i = 0xE000; $i < 0xFFFF; $i++)
            $this->alphabet .= iconv('UCS-4LE', 'UTF-8', pack('V', $i));
            
        $this->alphabet = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890_.,#'\"+*-\n";
        
        $this->alphabetSize = strlen($this->alphabet);
        $this->snippets = array(
            "=",
            "==",
            "===",
            "[[",
            "]]",
            "|",
            "**",
            "//",
            "\n",
            "\n\n",
            "{{{",
            "}}}",
            "|",
        );
        $this->snippetCount = count($this->snippets);
    }
    
    public function testRandomMarkup1()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertPdfLatexSucceed($this->generateRandomMarkup());
        }
    }
    
    public function testRandomMarkup2()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertPdfLatexSucceed($this->generateRandomMarkup());
        }
    }
    
    public function testRandomMarkup3()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertPdfLatexSucceed($this->generateRandomMarkup());
        }
    }
    
    public function testRandomMarkup4()
    {
        for ($i = 0; $i < 25; $i++) {
            $this->assertPdfLatexSucceed($this->generateRandomMarkup());
        }
    }
    
    public function assertPdfLatexSucceed($markup)
    {
        $parser = new Parser();
        $doc = $parser->parse($markup);
        
        $urlGenerator = new UrlGenerator('http://invalid.domain/{wiki}/{page}');
        $urlGenerator->setCurrentWiki('test');
        
        $renderer = new LatexRenderer($urlGenerator);
        $latex = $renderer->render($doc);
        
        file_put_contents(sprintf('/tmp/%s.tex', $this->tempName), $latex);
        $process = new Process(sprintf('pdflatex %s.tex', $this->tempName), '/tmp');
        if ($process->run() != 0) {
            file_put_contents(sprintf('/tmp/%s.fail.md', $this->tempName), $markup);
            file_put_contents(sprintf('/tmp/%s.fail.tex', $this->tempName), $latex);
            $this->fail(sprintf('pdflatex failed with random markup (saved in /tmp/%s.fail.md)', $this->tempName));
        }
    }
    
    protected function generateRandomMarkup()
    {
        $length = rand(0, 1000);
        $markup = '';
        
        for ($i = 0; $i < $length; $i++) {
            $c = rand(0,3);
            
            if ($c == 0) {
                $markup .= $this->snippets[rand(0, $this->snippetCount - 1)];
            } else {
                $markup .= $this->alphabet[rand(0, $this->alphabetSize - 1)];
            }
        }
        
        return $markup;
    }
}

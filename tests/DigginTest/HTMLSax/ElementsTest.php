<?php
namespace DigginTest\HTMLSax;


class ElementsTest extends \PHPUnit_Framework_TestCase
{
    use BasicSetupTrait;

    function testEmptyElement() {
        $this->listener->expects($this->once())
            ->method('startHandler')->with($this->parser, 'tag', array(),FALSE);
        $this->listener->expects($this->once())
            ->method('endHandler')->with($this->parser, 'tag',FALSE);        
        $this->listener->expects($this->never())
            ->method('dataHandler');
        
        $this->parser->parse('<tag></tag>');
    }
    
    function testElementWithContent() {
        
        $this->listener->expects($this->once())
            ->method('startHandler')->with($this->parser, 'tag', array(),FALSE);
        $this->listener->expects($this->once())
            ->method('dataHandler')->with($this->parser, 'stuff');
        $this->listener->expects($this->once())
            ->method('endHandler')->with($this->parser, 'tag',FALSE);
        $this->parser->parse('<tag>stuff</tag>');        
    }
    function testMismatchedElements() {
        // $this->listener->expectArgumentsAt(0, 'startHandler', array('*', 'b', array(),FALSE));
        // $this->listener->expectArgumentsAt(1, 'startHandler', array('*', 'i', array(),FALSE));
        // $this->listener->expectArgumentsAt(0, 'endHandler', array('*', 'b',FALSE));
        // $this->listener->expectArgumentsAt(1, 'endHandler', array('*', 'i',FALSE));
    
        $this->listener->expects($this->exactly(2))->method('startHandler');
        $this->listener->expects($this->exactly(2))->method('endHandler');
        $this->parser->parse('<b><i>stuff</b></i>');
    }
    function testCaseFolding() {
       
        $this->listener->expects($this->once())
            ->method('startHandler')->with($this->parser, 'TAG', array(),FALSE);
        $this->listener->expects($this->once())
            ->method('dataHandler')->with($this->parser, 'stuff');
        $this->listener->expects($this->once())
            ->method('endHandler')->with($this->parser, 'TAG',FALSE);
        $this->parser->set_option('XML_OPTION_CASE_FOLDING');
        $this->parser->parse('<tag>stuff</tag>');      
        
    }
    function testEmptyTag() {
        
        $this->listener->expects($this->once())
            ->method('startHandler')->with($this->parser, 'tag', array(),TRUE);
        $this->listener->expects($this->never())
            ->method('dataHandler');        
        $this->listener->expects($this->once())
            ->method('endHandler')->with($this->parser, 'tag',TRUE);        
            
        $this->parser->parse('<tag />');
    }
    function testAttributes() {
        
        $this->listener->expects($this->once())
            ->method('startHandler')
            ->with($this->parser, 'tag', array("a" => "A", "b" => "B", "c" => "C"),FALSE);
        $this->parser->parse('<tag a="A" b=\'B\' c = "C">');
    }
    
    function testEmptyAttributes() {
       
        $this->listener->expects($this->once())
            ->method('startHandler')
            ->with($this->parser, 'tag', array("a" => NULL, "b" => NULL, "c" => NULL),FALSE);
                 
        $this->parser->parse('<tag a b c>');
    }
    function testNastyAttributes() {
        
        $this->listener->expects($this->once())
            ->method('startHandler')
            ->with($this->parser, 'tag', array("a" => "&%$'?<>", "b" => "\r\n\t\"", "c" => ""),FALSE);
                
        $this->parser->parse("<tag a=\"&%$'?<>\" b='\r\n\t\"' c = ''>");
    }
    function testAttributesPadding() {
                
        $this->listener->expects($this->once())
            ->method('startHandler')
            ->with($this->parser, 'tag', array("a" => "A", "b" => "B", "c" => "C"),FALSE);        
        
        $this->parser->parse("<tag\ta=\"A\"\rb='B'\nc = \"C\"\n>");
    }
}


<?php
// vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 encoding=utf-8 fdm=marker :

ini_set('include_path', dirname(__FILE__).PATH_SEPARATOR.ini_get('include_path'));

require_once 'PHPUnit/Framework.php';
require_once 'JSONWriter.php';

class JSONWriterTest extends PHPUnit_Framework_TestCase
{
    protected $j;
    protected $uri;
    function setUp()
    {
        $this->j = new JSONWriter();
        $this->uri = tempnam(sys_get_temp_dir(), 'JSONWriter');
    }
    function tearDown()
    {
        if(is_file($this->uri)) unlink($this->uri);
    }

    function test_construct()
    {
        $this->assertTrue($this->j instanceof JSONWriter);
    }
    function test_openUri()
    {
        $this->assertTrue($this->j->openUri($this->uri));
    }
    /*
    function test_Document()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['version'], '1.0');
        $this->assertEquals($r['encoding'], 'UTF-8');
    }
    function test_Element1()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertTrue(isset($r['Root']));
    }
    function test_Element2()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->setIndent(true));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startElement('Item'));
        $this->assertTrue($this->j->text('#1'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->startElement('Item'));
        $this->assertTrue($this->j->text('#2'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertTrue(isset($r['Root']));
        $this->assertEquals($r['Root']['Item'][0]['$t'], '#1');
        $this->assertEquals($r['Root']['Item'][1]['$t'], '#2');
    }
    function test_Element3()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->setIndent(true));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startElement('Item'));
        $this->assertTrue($this->j->text('#1'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->startElement('Item'));
        $this->assertTrue($this->j->text('#2'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->startElement('Item'));
        $this->assertTrue($this->j->text('#3'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertTrue(isset($r['Root']));
        $this->assertEquals($r['Root']['Item'][0]['$t'], '#1');
        $this->assertEquals($r['Root']['Item'][1]['$t'], '#2');
        $this->assertEquals($r['Root']['Item'][2]['$t'], '#3');
    }
    function test_ElementNS()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElementNS('ex', 'Root', 'http://www.example.com'));
        $this->assertTrue($this->j->text('<{.\ô/.}>'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertTrue(isset($r['ex$Root']));
    }
    function test_Comment()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startComment());
        $this->assertTrue($this->j->text('this a comment !'));
        $this->assertTrue($this->j->endComment());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertNotContains('this a comment', serialize($r));
    }
    function test_Attribute()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startAttribute('attr'));
        $this->assertTrue($this->j->text(__METHOD__));
        $this->assertTrue($this->j->endAttribute());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['Root']['attr'], __METHOD__);
    }
    function test_AttributeNS()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startAttributeNS('ex', 'attr', 'http://www.example.com'));
        $this->assertTrue($this->j->text(__METHOD__));
        $this->assertTrue($this->j->endAttribute());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['Root']['ex$attr'], __METHOD__);
    }
    function test_CData()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startCData());
        $this->assertTrue($this->j->text(__METHOD__));
        $this->assertTrue($this->j->endCData());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['Root']['$t'], __METHOD__);
    }
    function test_PI()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startPI('php'));
        $this->assertTrue($this->j->text(__METHOD__));
        $this->assertTrue($this->j->endPI());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['Root']['<?php']['$t'], __METHOD__);
    }
    function test_PI2()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startPI('php'));
        $this->assertTrue($this->j->text(__METHOD__.'#1'));
        $this->assertTrue($this->j->endPI());
        $this->assertTrue($this->j->startPI('php'));
        $this->assertTrue($this->j->text(__METHOD__.'#2'));
        $this->assertTrue($this->j->endPI());
       $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['Root']['<?php'][0]['$t'], __METHOD__.'#1');
        $this->assertEquals($r['Root']['<?php'][1]['$t'], __METHOD__.'#2');
    }
    function test_PI3()
    {
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'UTF-8'));
        $this->assertTrue($this->j->startElement('Root'));
        $this->assertTrue($this->j->startPI('php'));
        $this->assertTrue($this->j->text(__METHOD__.'#1'));
        $this->assertTrue($this->j->endPI());
        $this->assertTrue($this->j->startPI('php'));
        $this->assertTrue($this->j->text(__METHOD__.'#2'));
        $this->assertTrue($this->j->endPI());
        $this->assertTrue($this->j->startPI('php'));
        $this->assertTrue($this->j->text(__METHOD__.'#3'));
        $this->assertTrue($this->j->endPI());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
        $this->assertEquals($r['Root']['<?php'][0]['$t'], __METHOD__.'#1');
        $this->assertEquals($r['Root']['<?php'][1]['$t'], __METHOD__.'#2');
        $this->assertEquals($r['Root']['<?php'][2]['$t'], __METHOD__.'#3');
    }
    function test_full()
    {
        // from http://www.phpbuilder.com/columns/iceomnia_20090116.php3
        $writer = new JsonWriter();
        $this->assertTrue($this->j->setIndent(true));

        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0'));
        $this->assertTrue($this->j->startElement('rss'));
        $this->assertTrue($this->j->writeAttribute('version', '2.0'));
        $this->assertTrue($this->j->startElement('channel'));
        $this->assertTrue($this->j->writeElement('title', 'Latest Products'));
        $this->assertTrue($this->j->writeElement('description', 'This is the latest products from our website.'));
        $this->assertTrue($this->j->writeElement('link', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->writeElement('pubDate', date("D, d M Y H:i:s e")));
        $this->assertTrue($this->j->startElement('image'));
        $this->assertTrue($this->j->writeElement('title', 'Latest Products'));
        $this->assertTrue($this->j->writeElement('link', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->writeElement('url', 'http://www.iab.net/media/image/120x60.gif'));
        $this->assertTrue($this->j->writeElement('width', '120'));
        $this->assertTrue($this->j->writeElement('height', '60'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->startElement('item'));
        $this->assertTrue($this->j->writeElement('title', 'New Product 8'));
        $this->assertTrue($this->j->writeElement('link', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->writeElement('description', 'Description 8 Yeah!'));
        $this->assertTrue($this->j->writeElement('guid', 'http://www.domain.com/link.htm?tiem=1234'));
        $this->assertTrue($this->j->writeElement('pubDate', date("D, d M Y H:i:s e")));
        $this->assertTrue($this->j->startElement('category'));
        $this->assertTrue($this->j->writeAttribute('domain', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->text('May 2008'));
        $this->assertTrue($this->j->endElement());  // category
        $this->assertTrue($this->j->endElement());  // item
        $this->assertTrue($this->j->startElement('item'));
         $this->assertTrue($this->j->writeElement('title', 'New Product 7'));
        $this->assertTrue($this->j->writeElement('link', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->writeElement('description', 'Description  Yeah!'));
        $this->assertTrue($this->j->writeElement('guid', 'http://www.domain.com/link.htm?tiem=1234'));
        $this->assertTrue($this->j->writeElement('pubDate', date("D, d M Y H:i:s e")));
        $this->assertTrue($this->j->startElement('category'));
        $this->assertTrue($this->j->writeAttribute('domain', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->text('May 2008'));
        $this->assertTrue($this->j->endElement());  // category
        $this->assertTrue($this->j->endElement());  // item
        $this->assertTrue($this->j->startElement('item'));
        $this->assertTrue($this->j->writeElement('title', 'New Product 6'));
        $this->assertTrue($this->j->writeElement('link', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->writeElement('description', 'Description 8 Yeah!'));
        $this->assertTrue($this->j->writeElement('guid', 'http://www.domain.com/link.htm?tiem=1234'));
        $this->assertTrue($this->j->writeElement('pubDate', date("D, d M Y H:i:s e")));
        $this->assertTrue($this->j->startElement('category'));
        $this->assertTrue($this->j->writeAttribute('domain', 'http://www.domain.com/link.htm'));
        $this->assertTrue($this->j->text('May 2008'));
        $this->assertTrue($this->j->endElement());  // category
        $this->assertTrue($this->j->endElement());  // item
        $this->assertTrue($this->j->endElement());  // channel
        $this->assertTrue($this->j->endElement());  // rss
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
    }

    function test_fullbis() {

        $writer = new JsonWriter();
        $this->assertTrue($this->j->setIndent(true));
        $this->assertTrue($this->j->openUri($this->uri));
        $this->assertTrue($this->j->startDocument('1.0', 'utf-8', true));
        $this->assertTrue($this->j->writePI('xml-stylesheet', 'type="text/xsl" media="screen" href="test.xsl"'));
        for($i=1,$x=''; $i < 512; $i++) $x .= ' ';
        $this->assertTrue($this->j->writeComment($x));
        $this->assertTrue($this->j->startElementNS('rdf', 'RDF', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#'));
        $this->assertTrue($this->j->writeAttributeNS('xmlns', 'skos',  null, 'http://www.w3.org/2004/02/skos/core#'));
        $this->assertTrue($this->j->startElement('skos:Concept'));
        $this->assertTrue($this->j->writeAttribute('rdf:about', 'truc#'));
        $this->assertTrue($this->j->startElement('skos:prefLabel'));
        $this->assertTrue($this->j->writeAttribute('xml:lang', 'fr'));
        $this->assertTrue($this->j->text('truc'));
        $this->assertTrue($this->j->endElement()); 
        $this->assertTrue($this->j->endElement()); 
        $this->assertTrue($this->j->flush());
        $this->assertTrue($this->j->startElement('skos:Concept'));
        $this->assertTrue($this->j->writeAttribute('rdf:about', 'bidule#'));
        $this->assertTrue($this->j->startElement('skos:prefLabel'));
        $this->assertTrue($this->j->writeAttribute('xml:lang', 'fr'));
        $this->assertTrue($this->j->text('bidule'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->flush());
        $this->assertTrue($this->j->startElement('skos:Concept'));
        $this->assertTrue($this->j->writeAttribute('rdf:about', 'chouette#'));
        $this->assertTrue($this->j->startElement('skos:prefLabel'));
        $this->assertTrue($this->j->writeAttribute('xml:lang', 'fr'));
        $this->assertTrue($this->j->text('chouette'));
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->flush());
        $this->assertTrue($this->j->endElement());
        $this->assertTrue($this->j->endDocument());
        $r = $this->flush_and_get();
    }
     */

    function test_Memory()
    {
        $this->assertTrue($this->j->setIndent(true));
        $this->assertTrue($this->j->setIndentString('.'));
        $this->assertTrue($this->j->openMemory());
        $this->assertTrue($this->j->startDocument());
        $this->assertTrue($this->j->startElement('x'));
        $this->assertTrue($this->j->startAttribute('x'));
        $this->assertTrue($this->j->text('x'));
        $this->assertTrue($this->j->endAttribute());    
        $this->assertTrue($this->j->startElement('y')); // Doesn't support tag and attr with the same name
        $this->assertTrue($this->j->text('x'));
        $this->assertTrue($this->j->endElement());    
        $this->assertTrue($this->j->endElement()); 
        $this->assertTrue($this->j->endDocument());
        $this->assertTrue($this->j->flush() !== false);
        $r1 = $this->j->outputMemory(false);
        $r2 = $this->j->outputMemory();
        $r3 = $this->j->outputMemory();
        $this->assertTrue($r1 !== false);
        $this->assertTrue($r2 !== false);
        $this->assertTrue($r3 !== false);
        $this->assertEquals($r1, $r2);
        $this->assertEquals($r3, '');
        $this->assertEquals("{\n.\"version\": \"1.0\",\n.\"encoding\": \"utf-8\",\n.\"x\": {\n..\"x\": \"x\",\n..\"y\": {\n...\"\$t\": \"x\"\n..}\n.}\n}", $r1);
    }

    /* */
    private function flush_and_get()
    {
        $r = $this->j->flush();
        $this->assertTrue($r !== false, 'flush failed !');
        $r = file_get_contents($this->uri);
        $this->assertTrue($r !== false, 'file_get_contents failed !');
        $r = json_decode($r, true);
        $this->assertNotNull($r, 'json_decode failed !');
        return $r;
    }





}

<?php declare(strict_types=1);

namespace Consatan\XML;

use StdClass;
use SimpleXMLElement;

/**
 * Class XMLLoaderTest
 *
 * @author Chopin Ngo <consatan@gmail.com>
 */
class XMLLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEncodingFail()
    {
        $this->assertEquals('UTF-8', XMLLoader::getEncoding(''));
        $this->assertEquals('UTF-8', XMLLoader::getEncoding('<root></root>'));
        $this->assertEquals('GB2312', XMLLoader::getEncoding('<root></root>', 'GB2312'));
    }

    public function testEmptyStringNode()
    {
        $xml = '<xml><root><text><![CDATA[]]></text><other><![CDATA[123]]></other></root></xml>';
        $simplexml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        $loader = new XMLLoader($xml);
        $this->assertEquals(['root' => ['text' => '', 'other' => 123]], $loader->toArray());
        $this->assertEquals($simplexml, $loader->getXML());
        // 空字符串节点被解析为空数组
        $this->assertEquals(new SimpleXMLElement('<xml></xml>'), $simplexml->root->text[0]);
    }

    /**
     * @expectedException Consatan\XML\XMLException
     *
     */
    public function testException()
    {
        $xml = new XMLLoader('<xml><text>abc</xml>');
    }
}

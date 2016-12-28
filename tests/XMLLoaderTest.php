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
}

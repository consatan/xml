<?php declare(strict_types=1);

namespace Consatan\XML;

use StdClass;
use SimpleXMLElement;

/**
 * Interface XMLLoader
 *
 * @author Chopin Ngo <consatan@gmail.com>
 */
interface XMLLoaderInterface
{
    /**
     * 获取 XML 文档头部信息获取字符集
     *
     * @param   string  $xml      XML 字符串，可以是字符串或文件路径
     * @param   string  $charset  ('UTF-8') 如果 XML 未定义字符集，使用指定字符集
     * @return  string            XML的字符集（全大写），如果找不到 XML 头部信息中
     *                            encoding属性或者提供的不是 xml 字符串，返回指定字符集 $charset
     */
    public static function getEncoding(string $xml, string $charset = 'UTF-8'): string;

    /**
     * 获取 SimpleXMLElement 实例
     *
     * @return \SimpleXMLElement
     */
    public function getXML(): SimpleXMLElement;

    /**
     * 转换 SimpleXMLElement 为 PHP 数组
     *
     * @return array
     * @throws \Consatan\XML\XMLException
     */
    public function toArray(): array;

    /**
     * 转换 SimpleXMLElement 为 StdClass
     *
     * @return StdClass
     * @throws \Consatan\XML\XMLException
     */
    public function toObject(): StdClass;
}

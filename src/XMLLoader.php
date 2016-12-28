<?php declare(strict_types=1);

namespace Consatan\XML;

use StdClass;
use SimpleXMLElement;

/**
 * Class XMLLoader
 *
 * @author Chopin Ngo <consatan@gmail.com>
 */
class XMLLoader implements XMLLoaderInterface
{
    /**
     * xml
     *
     * @var \SimpleXMLElement
     */
    protected $xml = null;

    /**
     * 初始化 XML 对象
     *
     * @param   mixed   $xml        XML字符串或实例化后的 SimpleXMLElement 对象
     * @param   string  $charset    ('UTF-8') XML字符串的字符集，设置此
     *                              参数将自动将字符串转换成 UTF-8 字符集，如果设置为空
     *                              ('' 或 null 或 false)，则根据 self::getEncoding 自动判断字符集
     * @param   int     $options    (LIBXML_NOCDATA) Libxml 的可选参数
     *                              具体参数定义参看 php 文档 simplexml_load_string
     * @throws  XMLException        实例化失败抛出 XMLException 异常
     */
    public function __construct($xml, string $charset = 'UTF-8', int $options = LIBXML_NOCDATA)
    {
        if (is_string($xml)) {
            $this->xml = $xml;
            $charset = trim($charset);
            // 如果有提供字符集参数，则转换为 UTF-8
            if ($charset !== 'UTF-8') {
                $charset = $charset === '' ? self::getEncoding($xml) : $charset;

                $this->xml = @iconv($charset, 'UTF-8', $xml);
                if ($this->xml === false) {
                    throw new XMLException("转换 XML 字符集($charset => UTF-8)失败。");
                }
            }

            // 开启 libxml 错误捕获
            libxml_use_internal_errors(true);
            // 清除 libxml 错误信息，避免和其他程序的错误信息混淆
            libxml_clear_errors();

            $this->xml = simplexml_load_string($this->xml, null, $options);

            if ($this->xml === false) {
                $error = libxml_get_errors();
                // 清除 libxml 错误信息，避免错误信息影响到其他程序
                libxml_clear_errors();
                // 关闭 libxml 错误捕获，避免影响到其他程序
                libxml_use_internal_errors(false);

                throw new XMLException('无效的 XML。' . var_export($error, true));
            }
            libxml_use_internal_errors(false);
        } elseif ($xml instanceof SimpleXMLElement) {
            $this->xml = $xml;
        } else {
            throw new XMLException('无效的参数 $xml(' . gettype($xml)
              . '), 期望类型为 string 或 SimpleXMLElement。');
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getEncoding(string $xml, string $charset = 'UTF-8'): string
    {
        $xml = trim($xml);
        if ('' !== $xml) {
            // 如果参数是文件路径
            if ($xml[0] !== '<') {
                // 只需要获取头部信息，不需要载入整个文档
                $xml = @file_get_contents($xml, null, null, 0, 64);
                if ($xml === false) {
                    // 此处获取文件信息失败返回指定字符集虽然怪怪的
                    // 但本身调用者就应该保证参数的正确性，所以坚持不 throw 或返回 false
                    return $charset;
                }
            }

            if (preg_match('/^<\?xml[^>]+encoding\s*=\s*["\']([^"\']*)/i', $xml, $match) === 1) {
                return strtoupper($match[1]);
            }
        }

        return $charset;
    }

    /**
     * {@inheritdoc}
     */
    public function getXML(): SimpleXMLElement
    {
        return $this->xml;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->to();
    }

    /**
     * {@inheritdoc}
     */
    public function toObject(): StdClass
    {
        return $this->to(false);
    }

    /**
     * 转换 SimpleXMLElement 为 PHP 数组或 StdClass
     *
     * @param  bool  $assoc   (true) `true` 返回数组，`false` 返回 StdClass
     * @return array|\StdClass
     * @throws \Consatan\XML\XMLException
     */
    protected function to(bool $assoc = true)
    {
        if (false === ($to = @json_encode($this->xml)) || false === ($to = @json_decode($to, $assoc))) {
            throw new XMLException('SimpleXMLElement 转换为 ' . ($assoc ? 'Array' : 'Object')
                .  ' 时失败：' . json_last_error_msg() . '。');
        }

        return $to;
    }
}

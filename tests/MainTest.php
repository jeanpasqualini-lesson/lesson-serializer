<?php
namespace Test;

use Encoder\YamlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var Serializer */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer()],
            [
                new XmlEncoder(),
                new JsonEncoder(),
                new YamlEncoder()
            ]);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf(Serializer::class, $this->serializer);
    }

    public function provideEncode()
    {
        yield ['json', '{"name":"john"}'];
        yield ['xml',
            '<?xml version="1.0"?>'.PHP_EOL.
            '<response><name>john</name></response>'.PHP_EOL
        ];
        yield ['yaml', 'name: john'.PHP_EOL];
    }

    /**
     * @dataProvider provideEncode
     * @param $format
     * @param $expected
     */
    public function testEncode($format, $expected)
    {
        $dataNormalized = ['name' => 'john'];

        $this->assertEquals($expected, $this->serializer->encode($dataNormalized, $format));
    }

    public function provideDecode()
    {
        yield ['json', '{"name":"john"}'];
        yield ['xml',
            '<?xml version="1.0"?>'.PHP_EOL.
            '<document><name>john</name></document>'.PHP_EOL
        ];
        yield ['yaml', 'name: john'];
    }

    /**
     * @dataProvider provideDecode
     * @param $format
     * @param $dataEncoded
     */
    public function testDecode($format, $dataEncoded)
    {
        $this->assertEquals(['name' => 'john'], $this->serializer->decode($dataEncoded, $format));
    }
}
<?php
namespace Test;

use Encoder\YamlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /** @var Serializer */
    protected $serializer;

    public function setUp()
    {
        $this->serializer = new Serializer(
            [new GetSetMethodNormalizer(
                $classMetadataFactory = null,
                $nameConverter = new CamelCaseToSnakeCaseNameConverter(),
                $propertyTypeExtractor = null
            )],
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

    private function factoryObject($color = 'red', $size = 'xl', $subject = 't-shirt')
    {
        return new Class($color, $size, $subject) {
            private $color;
            private $size;
            private $subject;

            public function __construct($color = 'red', $size = 'xl', $subject = 't-shirt')
            {
                $this->color = $color;
                $this->size = $size;
                $this->subject = $subject;
            }

            public function getHelloWorld()
            {
                return "the $this->subject is $this->color and size is $this->size";
            }

            public function setHelloWorld($hello)
            {
                preg_match('/the ([a-z-]+) is ([a-z-]+) and size is ([a-z-]+)/i', $hello, $matches);
                list($this->subject, $this->color, $this->size) = array_splice($matches, 1);
            }
        };
    }

    public function testNormalize()
    {
        $object = $this->factoryObject();

        $this->assertEquals([
            'hello_world' => 'the t-shirt is red and size is xl',
        ], $this->serializer->normalize($object));
    }

    public function testDenormalize()
    {
        $object = $this->factoryObject('blue');

        $data = [
            'hello_world' => 'the t-shirt is blue and size is xl',
        ];

        $this->assertEquals($object, $this->serializer->denormalize($data, get_class($object)));
    }
}
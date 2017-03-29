<?php
namespace Test;

use Encoder\YamlEncoder;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Serializer;

class EncoderTest extends \PHPUnit_Framework_TestCase
{
    public function testJsonEncoder()
    {
        $serializer = new Serializer([], [new JsonEncoder()]);
        $this->assertEquals('{"name":"john"}', $serializer->encode(['name' => 'john'], 'json'));
    }

    public function testXmlEncoder()
    {
        $serializer = new Serializer([], [new XmlEncoder()]);
        $this->assertEquals(
            '<?xml version="1.0"?>'.PHP_EOL.
            '<response><name>john</name></response>'.PHP_EOL,
            $serializer->encode(['name' => 'john'], 'xml')
        );
    }

    /**
     * @group 3.1
     */
    public function testYamlEncoder()
    {
        $serializer = new Serializer([], [new YamlEncoder()]);
        $this->assertEquals('name: john'.PHP_EOL, $serializer->encode(['name' => 'john'], 'yaml'));
    }

    /**
     * @group 3.1
     */
    public function testCsvEncoder()
    {
        $serializer = new Serializer([], [new CsvEncoder()]);
        $this->assertEquals(
            'name'.PHP_EOL.
            'john'.PHP_EOL,
            $serializer->encode(['name' => 'john'], 'csv')
        );
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 29/03/17
 * Time: 18:54
 */

namespace Test;


use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\JsonSerializableNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group 3.1
     */
    public function testDataUriNormalizer()
    {
        $object = new \SplFileInfo(__DIR__ . '/../autoload.php');

        $serializer = new Serializer([
            new DataUriNormalizer()
        ], []);

        $this->assertEquals(
            'data:application/octet-stream;base64,' . base64_encode(file_get_contents(__DIR__ . '/../autoload.php')),
            $serializer->normalize($object)
        );
    }

    /**
     * @group 3.1
     */
    public function testDateTimeNormalizer()
    {
        $object = new \DateTime('2016-01-01');

        $serializer = new Serializer([
            new DateTimeNormalizer()
        ], []);

        $this->assertEquals(
            '2016-01-01T00:00:00+01:00',
            $serializer->normalize($object)
        );
    }

    public function testPropertyNormalizer()
    {
        $object = new Class {
            private $color = 'red';
        };

        $serializer = new Serializer([
            new PropertyNormalizer()
        ], []);

        $this->assertEquals(['color' => 'red'], $serializer->normalize($object));
    }

    /**
     * @group 3.1
     */
    public function testJsonSerializableNormalizer()
    {
        $object = new Class implements \JsonSerializable {
            function jsonSerialize()
            {
                return ['color' => 'red'];
            }
        };

        $serializer = new Serializer([
            new JsonSerializableNormalizer()
        ], []);

        $this->assertEquals(['color' => 'red'], $serializer->normalize($object));
    }
}
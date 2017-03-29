<?php

namespace Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DataUriNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Annotation;

class AnnotationSerializerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Serializer */
    protected $serializer;

    /** @var ObjectNormalizer */
    private $normalizer;

    public function setUp()
    {
        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $nameConveter = new CamelCaseToSnakeCaseNameConverter();
        $normalizer = $this->normalizer = new ObjectNormalizer($classMetadataFactory, $nameConveter);
        $encoder = new JsonEncoder();

        $this->serializer = new Serializer([$normalizer], [$encoder]);
    }

    private function factoryObject()
    {
        return new Class {
            /** @Annotation\Groups({"color"}) */
            public $color = 'red';
            /** @Annotation\Groups({"size"}) */
            public $size = 'xl';
        };
    }

    public function testGroup()
    {
        $object = $this->factoryObject();

        $this->assertEquals(['color' => 'red'],
            $this->serializer->normalize(
                $object,
                $format = null,
                ['groups' => ['color']]
            )
        );
    }

    public function testIgnoreAttribute()
    {
        $object = $this->factoryObject();

        $this->normalizer->setIgnoredAttributes(['size']);

        $this->assertEquals(['color' => 'red'],
            $this->serializer->normalize($object)
        );
    }

    public function testOrgPrefixNameConverter()
    {
        $object = $this->factoryObject();

        $orgPrefixNameConverter = new Class implements NameConverterInterface {
            public function normalize($propertyName)
            {
                 return 'org_'.$propertyName;
            }

            public function denormalize($propertyName)
            {
                return 'org_' === substr($propertyName, 0, 4)
                    ? substr($propertyName, 4)
                    : $propertyName;
            }
        };
        $serializer = new Serializer([new ObjectNormalizer(null, $orgPrefixNameConverter)], []);

        $this->assertEquals([
            'org_color' => 'red',
            'org_size' => 'xl'
        ], $serializer->normalize($object));
    }

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
}
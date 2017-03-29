<?php

namespace Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
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

    public function testMaxDepth()
    {
        $factoryObject = function() {
            return new Class {
                public $color = 'red';
                /** @Annotation\MaxDepth(2) */
                public $children;
            };
        };

        $object = $factoryObject();
        $object->children = $factoryObject();
        $object->children->children = $factoryObject();
        $object->children->children->children = $factoryObject();

        $this->assertEquals([
            'color' => 'red',
            'children' => [
                'color' => 'red',
                'children' => [
                    'color' => 'red',
                ]
            ]
        ], $this->serializer->normalize($object, null, array('enable_max_depth' => true)));
    }

    public function testHandlingArray()
    {
        $data = [
            'first' => $this->factoryObject(),
            'second' => $this->factoryObject(),
        ];

        $this->assertEquals([
            'first' => [
                'color' => 'red',
                'size' => 'xl'
            ],
            'second' => [
                'color' => 'red',
                'size' => 'xl'
            ],
        ], $this->serializer->normalize($data));
    }
}
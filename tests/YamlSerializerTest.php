<?php

namespace Test;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\ClassMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\YamlFileLoader;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Annotation;
use Symfony\Component\Serializer\Mapping\ClassMetadata;

class YamlSerializerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Serializer */
    protected $serializer;

    /** @var ObjectNormalizer */
    private $normalizer;

    public function setUp()
    {
        $classMetadataFactory = new ClassMetadataFactory(
            $this->factoryYamlFileLoader(__DIR__ . '/fixtures/yaml_serializer.yml')
        );
        $nameConveter = new CamelCaseToSnakeCaseNameConverter();
        $normalizer = $this->normalizer = new ObjectNormalizer($classMetadataFactory, $nameConveter);
        $encoder = new JsonEncoder();

        $this->serializer = new Serializer([$normalizer], [$encoder]);
    }

    private function factoryYamlFileLoader($config)
    {
        $yamlFileLoader = new Class($config) extends YamlFileLoader {
            private $object;

            public function setObject($object)
            {
                $this->object = $object;
            }

            public function loadClassMetadata(ClassMetadataInterface $classMetadata)
            {
                /** @var $classMetadata ClassMetadata */
                $classMetadata->name = 'anonymous';
                $reflClassMetatadata = new \ReflectionClass($classMetadata);
                $reflClassMetataRefl = $reflClassMetatadata->getProperty('reflClass');
                $reflClassMetataRefl->setAccessible(true);
                $reflClassMetataRefl->setValue($classMetadata, new \ReflectionClass($this->object));
                return parent::loadClassMetadata($classMetadata);
            }
        };
        $yamlFileLoader->setObject($this->factoryObject());

        return $yamlFileLoader;
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
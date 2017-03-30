<?php
namespace Test;

use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TypeExtractorTest extends \PHPUnit_Framework_TestCase
{
    protected $object;
    protected $objectNormalized;

    public function setUp()
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

        $this->object = $object;
        $this->objectNormalized = [
            'color' => 'red',
            'children' => [
                'color' => 'red',
                'children' => [
                    'color' => 'red',
                    'children' => null
                ]
            ]
        ];
    }

    public function testWithoutExtractor()
    {
        $serializer = new Serializer(
            $normalizers = [
                new ObjectNormalizer(
                    null,
                    null,
                    null,
                    null
                )
            ],
            $encoders = []
        );

        $objectPartialyDenormalized = clone $this->object;
        $objectPartialyDenormalized->children = $this->objectNormalized['children'];

        $this->assertEquals(
            $objectPartialyDenormalized,
            $serializer->denormalize(
                $this->objectNormalized,
                    get_class($this->object)
            )
        );
    }

    public function testWithExtractor()
    {
        $propertyTypeExtractor = new Class implements PropertyTypeExtractorInterface {
            public function getTypes($class, $property, array $context = array())
            {
                if('children' === $property) {
                    return [new Type(
                        Type::BUILTIN_TYPE_OBJECT,
                        null,
                        $class
                    )];
                }

                return null;
            }
        };

        $serializer = new Serializer(
            $normalizers = [
                new ObjectNormalizer(
                    null,
                    null,
                    null,
                    $propertyTypeExtractor
                )
            ],
            $encoders = []
        );

        $this->assertEquals(
            $this->object,
            $serializer->denormalize(
                $this->objectNormalized,
                get_class($this->object)
            )
        );
    }
}
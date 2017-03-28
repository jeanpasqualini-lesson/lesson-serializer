<?php
namespace Test\NameConverter;

use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;

class CamelCaseToSnakeConverterTest extends \PHPUnit_Framework_TestCase
{
    /** @var CamelCaseToSnakeCaseNameConverter */
    private $nameConverter;

    public function setUp()
    {
        $this->nameConverter = new CamelCaseToSnakeCaseNameConverter($attributes = null, $lowerCamelCase = true);
    }

    public function testNormalize()
    {
        $this->assertEquals('hello_world', $this->nameConverter->normalize('helloWorld'));
    }

    public function testDenormalize()
    {
        $this->nameConverter = new CamelCaseToSnakeCaseNameConverter($attributes = null, $lowerCamelCase = true);
        $this->assertEquals('helloWorld', $this->nameConverter->denormalize('hello_world'));

        $this->nameConverter = new CamelCaseToSnakeCaseNameConverter($attributes = null, $lowerCamelCase = false);
        $this->assertEquals('HelloWorld', $this->nameConverter->denormalize('hello_world'));
    }
}
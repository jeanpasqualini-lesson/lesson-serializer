<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/10/15
 * Time: 6:41 AM
 */

namespace Test;


use Data\Person;
use Encoder\ClassHintEncoder;
use Encoder\YamlEncoder;
use Interfaces\TestInterface;
use Normalizer\ClassHintNormalisation;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class MainTest implements TestInterface {
    public function runTest()
    {
        // TODO: Implement runTest() method.

        $encoders = array(
            new XmlEncoder(),
            new JsonEncoder(),
            new YamlEncoder()
        );

        $getSetNormalizer = new GetSetMethodNormalizer();

        $getSetNormalizer->setIgnoredAttributes(array("firstName"));

        $normalizers = array(
            new ClassHintNormalisation(array(
                $getSetNormalizer
            ))
        );

        $serializer = new Serializer($normalizers, $encoders);


        $json = $serializer->serialize(new Person("john", "gates"), "json");

        $yaml = $serializer->serialize(new Person("john", "gates"), "yaml");

        echo "yaml : ".PHP_EOL.$yaml.PHP_EOL;

        echo "json : ".$json.PHP_EOL;

        $data = $serializer->decode($json, "json");

        echo "data : ".print_r($data, true).PHP_EOL;

        echo "unserialize data : ".$serializer->denormalize($data, Person::getClass(), "json")->__toString().PHP_EOL;

        echo "unserialize json : ".((string) $serializer->deserialize($json, Person::getClass(), "json")).PHP_EOL;

        echo "unserialize yaml : ".((string) $serializer->deserialize($json, Person::getClass(), "yaml")).PHP_EOL;
    }
}
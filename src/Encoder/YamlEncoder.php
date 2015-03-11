<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/10/15
 * Time: 7:40 AM
 */

namespace Encoder;


use Symfony\Component\Serializer\Encoder\DecoderInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;
use Symfony\Component\Serializer\Encoder\scalar;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Yaml\Dumper;
use Symfony\Component\Yaml\Parser;

class YamlEncoder implements EncoderInterface, DecoderInterface {
    /**
     * Encodes data into the given format.
     *
     * @param mixed $data Data to encode
     * @param string $format Format name
     * @param array $context options that normalizers/encoders have access to.
     *
     * @return scalar
     *
     * @throws UnexpectedValueException
     */
    public function encode($data, $format, array $context = array())
    {
        $yaml = new Dumper();

        return $yaml->dump($data, 2);

        // TODO: Implement encode() method.
    }

    /**
     * Decodes a string into PHP data.
     *
     * @param string $data Data to decode
     * @param string $format Format name
     * @param array $context options that decoders have access to.
     *
     * The format parameter specifies which format the data is in; valid values
     * depend on the specific implementation. Authors implementing this interface
     * are encouraged to document which formats they support in a non-inherited
     * phpdoc comment.
     *
     * @return mixed
     *
     * @throws UnexpectedValueException
     */
    public function decode($data, $format, array $context = array())
    {
        $yaml = new Parser();

        return $yaml->parse($data);
    }

    /**
     * Checks whether the serializer can encode to given format.
     *
     * @param string $format format name
     *
     * @return bool
     */
    public function supportsEncoding($format)
    {
        return "yaml" === $format;

        // TODO: Implement supportsEncoding() method.
    }

    /**
     * Checks whether the deserializer can decode from given format.
     *
     * @param string $format format name
     *
     * @return bool
     */
    public function supportsDecoding($format)
    {
        return "yaml" === $format;

        // TODO: Implement supportsDecoding() method.
    }


}
<?php
/**
 * Created by PhpStorm.
 * User: darkilliant
 * Date: 3/10/15
 * Time: 8:13 AM
 */
namespace Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ClassHintNormalisation implements NormalizerInterface, DenormalizerInterface {

    private $normalizers = array();

    private $currentNormalizer;

    public function __construct(array $normalizers = array())
    {
        $this->normalizers = $normalizers;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed $data data to restore
     * @param string $class the expected class to instantiate
     * @param string $format format the given data was extracted from
     * @param array $context options available to the denormalizer
     *
     * @return object
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        return $this->currentNormalizer->denormalize($data, $class, $format, $context);

        // TODO: Implement denormalize() method.
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed $data Data to denormalize from.
     * @param string $type The class to which the data should be denormalized.
     * @param string $format The format being deserialized from.
     *
     * @return bool
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        foreach($this->normalizers as $normalizer)
        {
            /** @var $normalizer DenormalizerInterface */
            if($result = $normalizer->supportsDenormalization($data, $type, $format))
            {
                $this->currentNormalizer = $normalizer;

                return $result;
            }
        }
        // TODO: Implement supportsDenormalization() method.
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object object to normalize
     * @param string $format format the normalization result will be encoded as
     * @param array $context Context options for the normalizer
     *
     * @return array|string|bool|int|float|null
     */
    public function normalize($object, $format = null, array $context = array())
    {
        return $this->currentNormalizer->normalize($object, $format, $context);

        // TODO: Implement normalize() method.
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed $data Data to normalize.
     * @param string $format The format being (de-)serialized from or into.
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        foreach($this->normalizers as $normalizer)
        {
            /** @var $normalizer NormalizerInterface */
            if($result = $normalizer->supportsNormalization($data, $format))
            {
                $this->currentNormalizer = $normalizer;

                return $result;
            }
        }

        // TODO: Implement supportsNormalization() method.
    }

}
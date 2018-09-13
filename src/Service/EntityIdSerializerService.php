<?php

namespace App\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Helper pra lidar com serialização de EntityIds.
 *
 * @author Carlos Eduardo Pauluk
 *
 */
class EntityIdSerializerService
{

    public function serializeIgnoring($objs, $ignoredAttrs = null)
    {
        $normalizer = new ObjectNormalizer();
        $normalizer->setIgnoredAttributes($ignoredAttrs);
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array(
            $normalizer
        ), array(
            $encoder
        ));

        $data = $serializer->normalize($objs, 'json');

        return $data;
    }

    public function serializeIncluding($objs, $includedAttrs = null)
    {
        $normalizer = new ObjectNormalizer();
        $encoder = new JsonEncoder();

        $serializer = new Serializer(array(
            $normalizer
        ), array(
            $encoder
        ));

        $data = $serializer->normalize($objs, 'json', array(
            'attributes' => $includedAttrs
        ));
        $json = $serializer->serialize($data, 'json');
        return $json;
    }
}
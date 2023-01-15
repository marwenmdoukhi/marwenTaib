<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @package App\DataTransformer
 */
class IssueToNumberTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $q
     * @return mixed|string
     */
    public function transform($q)
    {
        if (null === $q) {
            return  "";
        }
        return $q;
    }

    /**
     * @param mixed $string
     * @return mixed|string|null
     */
    public function reverseTransform($string)
    {
        if (null === $string) {
            return null;
        }

        return $string;
    }
}

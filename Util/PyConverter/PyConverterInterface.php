<?php

namespace Truelab\KottiSecurityBundle\Util\PyConverter;

/**
 * Interface PyConverterInterface
 * @package Truelab\KottiSecurityBundle\Util\PyConverter
 */
interface PyConverterInterface
{
    /**
     * Returns a specific char ("python like") for an ascii code
     *
     * @param int $ascii - the ascii code
     * @param bool $printable - if true return a printable version, default: false
     *
     * @return string - could contains binary data
     *
     * @return string
     */
    public function chr($ascii, $printable = false);
}

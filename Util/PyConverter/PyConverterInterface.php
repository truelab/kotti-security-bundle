<?php

namespace Truelab\KottiSecurityBundle\Util\PyConverter;

/**
 * Interface PyConverterInterface
 * @package Truelab\KottiSecurityBundle\Util\PyConverter
 */
interface PyConverterInterface
{
    /**
     * @param int $ascii
     * @param bool $printable
     *
     * @return
     */
    public function chr($ascii, $printable = false);
}

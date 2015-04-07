<?php

namespace Truelab\KottiSecurityBundle\Util\PyConverter;

use Truelab\KottiSecurityBundle\Util\PyConverter\Exception\ChrValueErrorException;

/**
 * Class PyConverter
 * @package Truelab\KottiSecurityBundle\Util
 */
class PyConverter implements PyConverterInterface
{
    /**
     * @var array - python.chr "special" ascii chars
     */
    protected $chrSpecialChars = [
        9 => '\t',
        10 => '\n',
        13 => '\r'
    ];

    /**
     * Returns a specific char ("python like") for an ascii code
     *
     * @param int $ascii - the ascii code
     * @param bool $printable - if true return a printable version, default: false
     *
     * @return string - could contains binary data when printable flag is false (default behavior)
     *
     * @throws ChrValueErrorException - when ascii code is out of range (0-256)
     */
    public function chr($ascii, $printable = false)
    {
        if($ascii < 0 || $ascii > 255) {
            throw new ChrValueErrorException('ValueError: chr() arg not in range(256)');
        }

        // it's a "special" char
        if(isset($this->chrSpecialChars[$ascii])) {
            return $this->chrSpecialChars[$ascii];
        }

        // it's printable ascii char and not equal to ?
        if(ctype_print(chr($ascii)) && !( $ascii >= 160 && $ascii <= 255) ) {

            return chr($ascii);

        }else{

            // when printable adds same padding and a \x like python
            if($printable) {
                $hex = dechex($ascii);
                $pyHex = ((strlen($hex) === 1) ? '0' . $hex : $hex);
                return '\x' . $pyHex;
            }

            // not printable binary version
            return chr($ascii);
        }

    }
}

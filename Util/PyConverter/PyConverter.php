<?php

namespace Truelab\KottiSecurityBundle\Util\PyConverter;

use Truelab\KottiSecurityBundle\Util\PyConverter\Exception\ChrValueErrorException;

/**
 * Class PyConverter
 * @package Truelab\KottiSecurityBundle\Util
 */
class PyConverter implements PyConverterInterface
{
    protected $chrSpecialCases = [
        9 => '\t',
        10 => '\n',
        13 => '\r'
    ];

    public function chr($ascii, $printable = false)
    {
        if($ascii < 0 || $ascii > 255) {
            throw new ChrValueErrorException('ValueError: chr() arg not in range(256)');
        }

        if(ctype_print(chr($ascii)) && !( $ascii >= 160 && $ascii <= 255) ) {

            return chr($ascii);

        }else{

            if(in_array($ascii, array_keys($this->chrSpecialCases))) {
                return $this->chrSpecialCases[$ascii];
            }

            $hex = dechex($ascii);

            $pyHex = ((strlen($hex) === 1) ? '0' . $hex : $hex);

            if($printable) {
                return '\x' . $pyHex;
            }

            return pack('H*', $pyHex); // binary version
        }
    }
}

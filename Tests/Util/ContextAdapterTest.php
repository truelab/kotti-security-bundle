<?php

namespace Truelab\KottiSecurityBundle\Tests\Util;

use Truelab\KottiSecurityBundle\Util\ContextAdapter;

/**
 * Class ContextAdapterTest
 * @package Truelab\KottiSecurityBundle\Tests\Util
 */
class ContextAdapterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ContextAdapter
     */
    private $adapted;

    public function setUp()
    {

        $context = $this->getMock('Truelab\KottiModelBundle\Model\ContentInterface');

        $context->expects($this->any())
            ->method('getPath')
            ->willReturn('/it/');

        $context->expects($this->any())
            ->method('getType')
            ->willReturn('language_root');

        $context->expects($this->any())
            ->method('getState')
            ->willReturn('public');

        $this->adapted = ContextAdapter::wrap($context);
    }

    public function testGetTypeTitle()
    {
        $expected = 'LanguageRoot';
        $actual = $this->adapted->getTypeTitle();

        $this->assertEquals($expected, $actual);
    }
}

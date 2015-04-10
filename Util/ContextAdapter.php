<?php

namespace Truelab\KottiSecurityBundle\Util;
use Truelab\KottiModelBundle\Model\ContentInterface;


/**
 * Class ContextAdapter
 * @package Truelab\KottiSecurityBundle\Util
 */
class ContextAdapter
{
    private $context;

    private function __construct(ContentInterface $context)
    {
        $this->context = $context;
    }

    public function getPath()
    {
        return $this->context->getPath();
    }

    public function getState()
    {
        return $this->context->getState();
    }

    public function getTypeTitle()
    {
        $str = join('', array_map(function ($word) {
            return ucfirst($word);
        }, explode('_', $this->context->getType())));

        return $str;
    }

    /**
     * @param $context
     *
     * @return array|ContextAdapter
     */
    public static function wrap($context)
    {
        if($context instanceof ContentInterface) {
            return new ContextAdapter($context);
        }

        return [
            'path' => '',
            'state' => 'public',
            'typeTitle' => 'WrongContextType'
        ];
    }
}

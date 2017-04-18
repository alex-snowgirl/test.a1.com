<?php

/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 4:48 PM
 */
namespace CORE;

/**
 * Simple Data holder class
 *
 * Class DataHolder
 * @package CORE
 */
class DataHolder extends \stdClass
{
    public function __construct(array $data = array())
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
    }
}
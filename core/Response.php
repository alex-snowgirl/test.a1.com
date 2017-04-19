<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:26 PM
 */
namespace CORE;

/**
 * Interface Response
 * @package CORE
 */
interface Response
{
    public function send($die = false);
}
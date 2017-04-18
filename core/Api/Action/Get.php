<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 12:18 AM
 */
namespace CORE\Api\Action;

use CORE\Api\Action;
use CORE\DataHolder;

/**
 * Class Get
 * @package CORE\Api\Action
 */
class Get extends Action
{
    public function run()
    {
        return new DataHolder(array(
            'code' => 200,
            'body' => $this->entity->read()
        ));
    }
}
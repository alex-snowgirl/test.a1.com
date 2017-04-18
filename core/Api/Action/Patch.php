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
 * Class Patch
 * @package CORE\Api\Action
 */
class Patch extends Action
{
    public function run()
    {
        $this->entity->update();

        return new DataHolder(array(
            'code' => 200,
            'body' => $this->entity->read()
        ));
    }
}
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
 * Class Delete
 * @package CORE\Api\Action
 */
class Delete extends Action
{
    public function run()
    {
        $this->entity->delete();

        return new DataHolder(array(
            'code' => 204,
            'body' => array()
        ));
    }
}
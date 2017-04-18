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
use CORE\Response\Exception as ResponseException;

/**
 * Not in use!!
 * @todo test
 * @todo improve
 *
 * Class Put
 * @package CORE\Api\Action
 */
class Put extends Action
{
    public function run()
    {
        if (!$this->entity->getId()) {
            new ResponseException('Invalid id', 400);
        }

        $this->entity->update();

        return new DataHolder(array(
            'code' => 200,
            'body' => $this->entity->read()
        ));
    }
}
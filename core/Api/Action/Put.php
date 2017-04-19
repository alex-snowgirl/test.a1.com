<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 12:18 AM
 */
namespace CORE\Api\Action;

use CORE\Api\Action;
use CORE\Api\Response;

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
    public function run(Response $response = null)
    {
        if (!$this->entity->getId()) {
            if ($response) {
                $response->setCode(400)->setBody('Invalid id');
            }

            return false;
        }

        $this->entity->update();

        $output = $this->entity->read();

        if ($response) {
            $response->setCode(200)->setBody($output);
        }

        return $output;
    }
}
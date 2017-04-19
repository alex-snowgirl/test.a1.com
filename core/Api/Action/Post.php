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
 * Class Post
 * @package CORE\Api\Action
 */
class Post extends Action
{
    public function run(Response $response = null)
    {
        $id = $this->entity->create();
        $output = $this->entity->setId($id)->read();

        if ($response) {
            $locationHeader = 'Location: /' . $this->entity->getRawEntity() . '/' . $id;

            $response->setCode(201)->setBody($output)
                ->addHeader($locationHeader);
        }

        return $output;
    }
}
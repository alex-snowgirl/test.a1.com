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
 * Class Post
 * @package CORE\Api\Action
 */
class Post extends Action
{
    public function run()
    {
        $id = $this->entity->create();

        $header = 'Location: /' . $this->entity->getRawEntity() . '/' . $id;
        header($header);

        return new DataHolder(array(
            'code' => 201,
            'body' => $this->entity->setId($id)->read()
        ));
    }
}
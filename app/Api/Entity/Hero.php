<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 3:15 AM
 */
namespace APP\Api\Entity;

use CORE\Api\Entity;
use CORE\Response\Exception as ResponseException;

/**
 * Class Hero
 * @package APP\Api\Entity
 */
class Hero extends Entity
{
    protected function getAcceptedData()
    {
        return array(
//            'id' => null,
            'name' => null,
            'attack' => null,
            'defence' => null
        );
    }

    public function create()
    {
        throw new ResponseException('Yuo do not have appropriate rights', 403);
    }

    /**
     * @todo load into storage and retrieve items with normal workflow
     *
     * @return array
     * @throws ResponseException
     */
    public function read()
    {
        $items = array(
            1 => array(
                'id' => 1,
                'name' => 'Assassin',
                'attack' => 12,
                'defence' => 6
            ),
            2 => array(
                'id' => 2,
                'name' => 'Protector',
                'attack' => 6,
                'defence' => 12
            ),
            3 => array(
                'id' => 3,
                'name' => 'Universal',
                'attack' => 9,
                'defence' => 9
            )
        );

        if ($this->getId()) {
            if (isset($items[$this->getId()])) {
                return $items[$this->getId()];
            }

            throw new ResponseException('No hero was found with such id', 404);
        }

        $items = array_values($items);

        return $items;
    }

    public function update()
    {
        throw new ResponseException('Yuo do not have appropriate rights', 403);
    }

    public function delete()
    {
        throw new ResponseException('Yuo do not have appropriate rights', 403);
    }
}
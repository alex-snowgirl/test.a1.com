<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 11:19 PM
 */
namespace APP\Api\Entity;

use CORE\Api\Action\Get;
use CORE\Api\Entity;

/**
 * Class User
 * @package APP\Api\Entity
 */
class User extends Entity
{
    protected function getAcceptedData()
    {
        return array(
//            'id' => null,
            'name' => null,
            'hero_id' => null,
            'level' => 0
        );
    }

    /**
     * Parent behaviour + Append Heroes objects
     *
     * @return array|mixed
     */
    public function read()
    {
        $items = parent::read();

        if ($this->getId()) {
            $items = array($items);
        }

        foreach ($items as $k => $item) {
            if ($item['hero_id']) {
                $heroGetCommand = new Get(new Hero($this->storage, $item['hero_id']));
                $items[$k]['hero'] = $heroGetCommand->apply()->body;
            }
        }

        if ($this->getId()) {
            return array_values($items)[0];
        }

        return $items;
    }
}
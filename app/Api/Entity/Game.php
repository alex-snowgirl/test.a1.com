<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 3:15 AM
 */
namespace APP\Api\Entity;

use CORE\Api\Action\Get;
use CORE\Api\Entity;
use CORE\Response\Exception as ResponseException;

/**
 * For saved games only
 *
 * Class Game
 * @package APP\Api\Entity
 */
class Game extends Entity
{
    protected function getAcceptedData()
    {
        return array(
//            'id' => null,
            'user_id' => null,
            'candidate' => null,
            'log' => array(),
//            'max_rounds' => 0,
            'is_saved' => 0
        );
    }

    /**
     * @todo improve check
     * @throws ResponseException
     */
    public function create()
    {
        if (!array_key_exists('user_id', $this->getData())) {
            throw new ResponseException('Empty owner', 404);
        }

        if (!array_key_exists('candidate', $this->getData())) {
            throw new ResponseException('Empty candidate', 404);
        }

        return parent::create();
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
            $heroGetCommand = new Get(new Hero($this->storage, $item['candidate']['hero_id']));
            $items[$k]['candidate']['hero'] = $heroGetCommand->apply()->body;
        }

        if ($this->getId()) {
            return current($items);
        }

        return $items;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 12:06 AM
 */
namespace CORE\Api;

use CORE\DataHolder;
use CORE\Observable;

/**
 * Class Action
 * Realize Command pattern
 *
 * @package CORE\Api
 */
abstract class Action extends Observable
{
    const EVENT_BEFORE_RUN = 0;
    const EVENT_AFTER_RUN = 1;

    /**
     * @var Entity
     */
    protected $entity;

    public function __construct(Entity $entity = null)
    {
        $this->entity = $entity;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Command executor
     * @todo specify return type when PHP7
     *
     * @return DataHolder
     */
    abstract public function run();

    /**
     * Command executor
     * @todo specify return type when PHP7
     *
     * @return DataHolder
     */
    public function apply()
    {
        $this->trigger(self::EVENT_BEFORE_RUN);
        $data = $this->run();
        $this->trigger(self::EVENT_AFTER_RUN, $data);
        return $data;
    }
}
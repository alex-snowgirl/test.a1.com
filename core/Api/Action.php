<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 12:06 AM
 */
namespace CORE\Api;

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

    public function __construct(Entity $entity)
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
     * @param Response|null $response
     * @return mixed
     */
    abstract public function run(Response $response = null);

    /**
     * Command executor
     * @todo specify return type when PHP7
     *
     * @param Response|null $response
     * @return mixed
     */
    public function apply(Response $response  = null)
    {
        $this->trigger(self::EVENT_BEFORE_RUN);
        $output = $this->run($response);
        $this->trigger(self::EVENT_AFTER_RUN, $output);
        return $output;
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 5:43 PM
 */
namespace CORE\Storage;

use CORE\Storage;

/**
 * Simple Session storage
 * @todo improve
 *
 * Class Session
 * @package CORE\Storage
 */
class Session implements Storage
{
    /**
     * @var \CORE\Session
     */
    protected $session;

    public function __construct()
    {
        $this->session = new \CORE\Session();
    }

    public function insert($entity, $data)
    {
        $entities = $this->getEntitiesByType($entity);
        $key = $this->genKey();
        $data['id'] = $key;
        $entities[$key] = $data;
        $this->setEntitiesByType($entity, $entities);
        return $key;
    }

    public function get($entity, $key)
    {
        if ($key) {
            return $this->getEntitiesByTypeAndKey($entity, $key);
        }

        return $this->getEntitiesByType($entity);
    }

    public function getByFilter($entity, array $filter = array())
    {
        $entities = $this->getEntitiesByType($entity);

        return array_filter($entities, function ($entity) use ($filter) {
            foreach ($filter as $k => $v) {
                if ($entity[$k] != $v) {
                    return false;
                }
            }

            return true;
        });
    }

    public function update($entity, $key, $data)
    {
        $entities = $this->getEntitiesByType($entity);
        $entities[$key] = array_merge($entities[$key], $data);
        $this->setEntitiesByType($entity, $entities);
        return true;
    }

    public function delete($entity, $key)
    {
        $entities = $this->getEntitiesByType($entity);

        if (isset($entities[$key])) {
            unset($entities[$key]);
        }

        $this->setEntitiesByType($entity, $entities);
        return true;
    }

    const ENTITIES_NAMESPACE = 'entities';

    protected function getAllEntities()
    {
        return $this->session->get(self::ENTITIES_NAMESPACE, array());
    }

    protected function getEntitiesByType($type)
    {
        $entities = $this->getAllEntities();
        return isset($entities[$type]) ? $entities[$type] : array();
    }

    protected function getEntitiesByTypeAndKey($type, $key)
    {
        $entities = $this->getEntitiesByType($type);
        return isset($entities[$key]) ? $entities[$key] : null;
    }

    protected function setEntitiesByType($type, $entities)
    {
        $tmp = $this->getAllEntities();
        $tmp[$type] = $entities;
        $this->session->set(self::ENTITIES_NAMESPACE, $tmp);
        return $this;
    }

    protected function genKey()
    {
        return md5(time() . 'asd' . mt_rand(0, 9999));
    }
}
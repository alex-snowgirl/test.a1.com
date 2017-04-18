<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 12:08 AM
 */
namespace CORE\Api;

use CORE\Storage;

/**
 * Class Entity
 * @package CORE\Api
 */
abstract class Entity/* implements \ArrayAccess*/
{
    /**
     * @var null
     */
    protected $id;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Storage passed here as Strategy of data management
     *
     * @param Storage $storage
     * @param null $id
     * @param array $data
     */
    public function __construct(Storage $storage, $id = null, array $data = array())
    {
        $this->storage = $storage;
        $this->setId($id);
        $this->setData($data);
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    public function create()
    {
        return $this->storage->insert($this->getRawEntity(), array_merge($this->getAcceptedData(), $this->getData()));
    }

    public function read()
    {
        if ($this->getId()) {
            return $this->storage->get($this->getRawEntity(), $this->getId());
        }

        return $this->storage->getByFilter($this->getRawEntity(), $this->getData());
    }

    public function update()
    {
        return $this->storage->update($this->getRawEntity(), $this->getId(), $this->getData());
    }

    public function delete()
    {
        return $this->storage->delete($this->getRawEntity(), $this->getId());
    }

    public function getRawEntity()
    {
        $tmp = explode('\\', get_called_class());
        $tmp = end($tmp);
        $tmp = strtolower($tmp);
        return $tmp;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    abstract protected function getAcceptedData();

    protected function setData(array $data = array())
    {
        $accepted = array_keys($this->getAcceptedData());
        $this->data = array_intersect_key($data, array_flip($accepted));
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * Abstract Factory method
     *
     * @param Storage $storage
     * @param null $id
     * @return static
     */
    public static function factory(Storage $storage, $id = null)
    {
        return new static($storage, $id);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:24 PM
 */
namespace CORE;

use Composer\Autoload\ClassLoader;

/**
 * Class App
 * @package CORE
 */
abstract class App
{
    /**
     * @var ClassLoader
     */
    protected $loader;
    /**
     * @var Storage
     */
    protected $storage;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;

    public function __construct(ClassLoader $loader, Storage $storage, View $view)
    {
        $this->loader = $loader;
        $this->storage = $storage;
        $this->request = $this->iniRequest();
        $this->response = $this->iniResponse($view);

        $this->request->parse();
        $this->run();
        $this->response->send();
    }

    /**
     * Abstract factory method
     * @todo specify return type when PHP7
     *
     * @return Request
     */
    abstract protected function iniRequest();

    /**
     * Abstract factory method
     * @todo specify return type when PHP7
     *
     * @param View $view
     * @return mixed
     */
    abstract protected function iniResponse(View $view);

    /**
     * Magic goes here
     * Facade method for itself
     * @todo specify return type when PHP7
     *
     * @return App
     */
    abstract protected function run();
}
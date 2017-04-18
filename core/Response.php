<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:26 PM
 */
namespace CORE;

/**
 * Class Response
 * @package CORE
 */
abstract class Response extends DataHolder
{
    /**
     * @var View
     */
    protected $view;

    /**
     * Constructor
     * View object - is a strategy of rendering
     *
     * @param View $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        parent::__construct();
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function send()
    {
        $this->view->output($this);
    }
}
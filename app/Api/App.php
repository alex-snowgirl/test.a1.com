<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/18/17
 * Time: 2:11 AM
 */
namespace APP\Api;

use APP\Api\Entity\User;
use CORE\Api\Action;

use CORE\Api\Action\Get;
use CORE\Api\Action\Post;
use CORE\Api\Action\Patch;
use CORE\Api\Action\Put;
use CORE\Api\Action\Delete;
use CORE\Session;

/**
 * Class App
 * @package APP\Api
 */
class App extends \CORE\Api\App
{
    /**
     * @var Session
     */
    protected $session;

    protected function bindHandlers(Action $action)
    {
        /**
         * Simple security
         * @todo auth
         */

        //@todo lazy getter
        $this->session = new Session();

        $action->on(Action::EVENT_BEFORE_RUN, function (Action $action) {
            $this->saveClientId()
                ->checkRequestQuantity()
                ->checkClientIdBetweenRequests()
                ->checkUserId($action);
        });

        $action->on(Action::EVENT_AFTER_RUN, function (Action $action, $output) {
            $this->saveClientUserId($action, $output);
        });
    }

    /**
     * Saves Request timestamp
     * Checks if Client sends too many requests
     *
     * @return App
     */
    protected function checkRequestQuantity()
    {
        $now = time();

        if (isset($this->session->time)) {
            if ($now - $this->session->time < 1) {
                $this->response->setCode(429)->setBody('Too many requests')
                    ->addHeader('Retry-After: 1')->send(true);
            }
        } else {
            $this->session->time = $now;
        }

        return $this;
    }

    /**
     * Saves Client id
     *
     * @return App
     */
    protected function saveClientId()
    {
        if (!isset($this->session->client_id)) {
            $this->session->client_id = $this->request->getClientId();
        }

        return $this;
    }

    /**
     * Saves Client User id
     *
     * @param Action $action
     * @param mixed $output
     * @return $this
     */
    protected function saveClientUserId(Action $action, $output)
    {
        if (Post::class == get_class($action) && User::class == get_class($action->getEntity())) {
            $tmp = $this->session->get('user_id', array());
            $tmp[] = $output['id'];
            $this->session->set('user_id', $tmp);
        }

        return $this;
    }

    /**
     * Saves Client id
     * Checks if stored and received Client ids are different
     *
     * @return App
     */
    protected function checkClientIdBetweenRequests()
    {
        if (isset($this->session->client_id)) {
            if ($this->session->client_id != $this->request->getClientId()) {
                $this->response->setCode(403)->setBody('Invalid client id')->send(true);
            }
        } else {
            $this->session->client_id = $this->request->getClientId();
        }

        return $this;
    }

    /**
     * Checks if given request User id belongs to the Client
     *
     * @param Action $action
     * @return $this
     */
    protected function checkUserId(Action $action)
    {
        $clientUserIds = $this->session->get('user_id', array());

        if (in_array(get_class($action), array(Get::class, Put::class, Patch::class, Delete::class))
            && User::class == get_class($action->getEntity()) && $action->getEntity()->getId()
            && !in_array($action->getEntity()->getId(), $clientUserIds)
        ) {
            $this->response->setCode(403)->setBody('User id is not belongs to you')->send(true);
        }

        $entityData = $action->getEntity()->read();

        if (isset($entityData['user_id']) && !in_array($entityData['user_id'], $clientUserIds)) {
            $this->response->setCode(403)->setBody('Entity is not belongs to you')->send(true);
        }

        return $this;
    }
}
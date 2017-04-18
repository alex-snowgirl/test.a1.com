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

use CORE\DataHolder;
use CORE\Response\Exception as ResponseException;

/**
 * Class App
 * @package APP\Api
 */
class App extends \CORE\Api\App
{
    protected function bindHandlers(Action $action)
    {
        /**
         * Simple security
         * @todo auth
         */

        $action->on(Action::EVENT_BEFORE_RUN, function (Action $action) {
            $this->saveClientId()
                ->checkRequestQuantity()
                ->checkClientIdBetweenRequests()
                ->checkUserId($action);
        });

        $action->on(Action::EVENT_AFTER_RUN, function (Action $action, DataHolder $data) {
            $this->saveClientUserId($action, $data);
        });
    }

    protected function startSession()
    {
        if (!session_id()) {
            session_start();
        }

        return $this;
    }

    /**
     * Saves Request timestamp
     * Checks if Client sends too many requests
     *
     * @return App
     * @throws ResponseException
     */
    protected function checkRequestQuantity()
    {
        $this->startSession();

        $now = time();

        if (isset($_SESSION['time'])) {
            if ($now - $_SESSION['time'] < 1) {
                header('Retry-After: 1');

                throw new ResponseException('Too many requests', 429);
            }
        } else {
            $_SESSION['time'] = $now;
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
        $this->startSession();

        if (!isset($_SESSION['client_id'])) {
            $_SESSION['client_id'] = $this->request->getClientId();
        }

        return $this;
    }

    /**
     * Saves Client User id
     *
     * @param Action $action
     * @param DataHolder $data
     * @return $this
     */
    protected function saveClientUserId(Action $action, DataHolder $data)
    {
        if (Post::class == get_class($action) && User::class == get_class($action->getEntity())) {
            $this->startSession();

            if (!isset($_SESSION['user_id'])) {
                $_SESSION['user_id'] = array();
            }

            $_SESSION['user_id'][] = $data->body['id'];
        }

        return $this;
    }

    protected function getClientUserIds()
    {
        $this->startSession();

        if (isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }

        return array();
    }

    /**
     * Saves Client id
     * Checks if stored and received Client ids are different
     *
     * @return App
     * @throws ResponseException
     */
    protected function checkClientIdBetweenRequests()
    {
        if (!session_id()) {
            session_start();
        }

        if (isset($_SESSION['client_id'])) {
            if ($_SESSION['client_id'] != $this->request->getClientId()) {
                throw new ResponseException('Invalid client id', 403);
            }
        } else {
            $_SESSION['client_id'] = $this->request->getClientId();
        }

        return $this;
    }

    /**
     * Checks if given request User id belongs to the Client
     *
     * @param Action $action
     * @return $this
     * @throws ResponseException
     */
    protected function checkUserId(Action $action)
    {
        if (in_array(get_class($action), array(Get::class, Put::class, Patch::class, Delete::class))
            && User::class == get_class($action->getEntity()) && $action->getEntity()->getId()
            && !in_array($action->getEntity()->getId(), $this->getClientUserIds())
        ) {
            throw new ResponseException('User id is not belongs to you', 403);
        }

        $entityData = $action->getEntity()->read();

        if (isset($entityData['user_id']) && !in_array($entityData['user_id'], $this->getClientUserIds())) {
            throw new ResponseException('Entity is not belongs to you', 403);
        }

        return $this;
    }
}
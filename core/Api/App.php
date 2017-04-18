<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:22 PM
 */
namespace CORE\Api;

use CORE\Api\Action;
use CORE\DataHolder;
use CORE\Response;
use CORE\Api\Request as ApiRequest;
use CORE\Api\Response as ApiResponse;
use CORE\Exception as CoreException;
use CORE\Response\Exception as ResponseException;
use CORE\Storage;
use CORE\View;

/**
 * Very simple REST API Application class
 *
 * Do not support async requests
 * Do not support transactions
 * Do not support caching
 *
 * Class Api
 * @package CORE\App
 * @property ApiRequest $request
 * @property ApiResponse $response
 */
class App extends \CORE\App
{
    /**
     * @return App
     */
    protected function run()
    {
        /** @var ApiRequest $request */
        /** @var ApiResponse $response */

        /**
         * Instead of implementing well-known controller-action pattern
         * I want to test Command pattern over here
         */

        try {
            $entity = $this->getEntity();
            $action = $this->getAction($entity);
            $this->bindHandlers($action);
            $output = $this->apply($action);

            /**
             * We could pass response object as Mediator to the action object also
             * In this case we do not need lines below
             *
             * But I decided to make it more visually, so...
             */
            $this->response->setCode($output->code)
                ->setBody($output->body);

//            sleep(1);
        } catch (ResponseException $ex) {
            $this->response->setCode($ex->getCode())
                ->setBody($ex->getMessage());
        } catch (CoreException $ex) {
            $this->response->setCode(500)
                ->setBody('Our API-Server is gone away! Sorry!');
        } catch (\Exception $ex) {
            $this->response->setCode(500)
                ->setBody('Ooops! Sorry!');
        }

        return $this;
    }

    protected function iniRequest()
    {
        return new ApiRequest();
    }

    protected function iniResponse(View $view)
    {
        return new ApiResponse($view);
    }

    /**
     * @param Entity $entity
     * @return mixed
     * @throws ResponseException
     */
    protected function getAction(Entity $entity)
    {
        $actionName = join('\\', array(
            'CORE',
            'Api',
            'Action',
            ucfirst(strtolower($this->request->getMethod()))
        ));

        if (!$this->loader->findFile($actionName)) {
            throw new ResponseException('Invalid method', 405);
        }

        $actionObject = new $actionName($entity);
        return $actionObject;
    }

    /**
     * Factory method
     * Command receiver
     *
     * @return Entity
     * @throws CoreException
     */
    protected function getEntity()
    {
        if (!$params = $this->request->getParams()) {
            throw new ResponseException('Invalid params set', 400);
        }

        $rawEntityName = array_keys($params)[0];

        $entityName = join('\\', array(
            'APP',
            'Api',
            'Entity',
            ucfirst($rawEntityName)
        ));

        if (!$this->loader->findFile($entityName)) {
            throw new ResponseException('Entity not found', 404);
        }

        $entityObject = new $entityName($this->storage, $params[$rawEntityName], $this->request->getParams());
        return $entityObject;
    }

    protected function bindHandlers(Action $action)
    {
    }

    /**
     * Proxy for Action apply method
     * Command invoker
     *
     * @param Action $action
     * @return DataHolder
     */
    protected function apply(Action $action)
    {
        return $action->apply();
    }
}
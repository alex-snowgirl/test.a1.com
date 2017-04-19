<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/18/17
 * Time: 5:20 AM
 */
use PHPUnit\Framework\TestCase;
use CORE\Api\Action\Post;
use APP\Api\Entity\User;
use CORE\Storage\Session;

require_once '../vendor/autoload.php';

/**
 * @todo...
 * Class CoreTest
 */
class CoreTest extends TestCase
{
    /**
     * POST API method test
     *
     * @dataProvider postProvider
     *
     * @param $entity
     * @param $dataHolder
     */
    public function testPost($entity, $dataHolder)
    {
        $action = new Post($entity);
        $result = $action->apply();

        //check generated id
        $this->assertTrue(isset($result['body']) && isset($result['body']['id']));

        //delete generated id, coz we do not know it before command execution
        unset($result['body']['id']);

        $this->assertEquals($dataHolder, $result);
    }

    public function postProvider()
    {
        return [
            [
                new User(new Session(), null, array('name' => 'Snowgirl')),
                array('code' => 201, 'body' => array('name' => 'Snowgirl', 'hero_id' => null, 'level' => 0))
            ]
        ];
    }
}
<?php
/**
 * 回调事件测试
 * User: Siam
 * Date: 2019/12/10
 * Time: 9:42
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\ORM\Tests\models\TestUserEventModel;
use PHPUnit\Framework\TestCase;

/**
 * Class ErrorTest
 * @package EasySwoole\ORM\Tests
 */
class EventTest extends TestCase
{
    /**
     * @var $connection Connection
     */
    protected $connection;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $config = new Config(MYSQL_CONFIG);
        $this->connection = new Connection($config);
        DbManager::getInstance()->addConnection($this->connection);
        $connection = DbManager::getInstance()->getConnection();
        $this->assertTrue($connection === $this->connection);
    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function testAdd()
    {
        $testUserModel = new TestUserEventModel();
        $testUserModel->state = 1;
        $testUserModel->name = 'Siam';
        $testUserModel->age = 100;
        $testUserModel->addTime = date('Y-m-d H:i:s');
        $data = $testUserModel->save();
        $this->assertFalse($data);

        // 更改为正常插入
        TestUserEventModel::$insert = true;

        $testUserModel = new TestUserEventModel();
        $testUserModel->state = 1;
        $testUserModel->name = 'Siam';
        $testUserModel->age = 100;
        $testUserModel->addTime = date('Y-m-d H:i:s');
        $data = $testUserModel->save();
        $this->assertIsInt($data);
    }

    /**
     * @throws Exception
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \Throwable
     */
    public function testUpdate()
    {
        $model = TestUserEventModel::create()->get([
            'name' => 'Siam',
            'age' => 100
        ]);

        $model->age = 333;
        $res = $model->update();

        $this->assertFalse($res);

        TestUserEventModel::$update = true;

        $model = TestUserEventModel::create()->get([
            'name' => 'Siam',
            'age' => 100
        ]);

        $model->age = 102;
        $res = $model->update();
        $this->assertTrue($res);

    }

    /**
     * @throws Exception
     * @throws \Throwable
     */
    public function testDeleteAll()
    {
        $res = TestUserEventModel::create()->destroy(null, true);
        $this->assertFalse($res);

        TestUserEventModel::$delete = true;
        $res = TestUserEventModel::create()->destroy(null, true);
        $this->assertIsInt($res);
    }
}

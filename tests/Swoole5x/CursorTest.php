<?php
/**
 * 游标模式
 * User: haoxu
 * Date: 2020-01-15
 * Time: 14:42
 */

namespace EasySwoole\ORM\Tests\Swoole5x;

use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Tests\models\TestUserModel;
use PHPUnit\Framework\TestCase;


class CursorTest extends TestCase
{
    /**
     * @var $connection Connection
     */
    protected $connection;

    protected $tableName = 'user_test_list';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $config = new Config(MYSQL_CONFIG);
        $config->setUseMysqli(false);
        $config->setFetchMode(true);
        $this->connection = new Connection($config);
        DbManager::getInstance()->addConnection($this->connection);
        $connection = DbManager::getInstance()->getConnection();
        $this->assertTrue($connection === $this->connection);
    }

    public function testAdd()
    {
//        if (version_compare(swoole_version(), '5.0.0', '>=')) {
//            $this->assertSame(1, 1);
//            return;
//        }

        $testUserModel = new TestUserModel();
        $testUserModel->state = 1;
        $testUserModel->name = 'Siam';
        $testUserModel->age = 18;
        $testUserModel->addTime = date('Y-m-d H:i:s');
        $data = $testUserModel->save();
        $this->assertIsInt($data);
    }

    public function testQuery()
    {
//        if (version_compare(swoole_version(), '5.0.0', '>=')) {
//            $this->assertSame(1, 1);
//            return;
//        }

        $result = TestUserModel::create()->all();
        $this->assertIsArray($result);
    }
}
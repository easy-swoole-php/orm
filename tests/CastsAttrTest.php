<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2020/6/4
 * Time: 16:20
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Tests\models\TestCastsModel;
use PHPUnit\Framework\TestCase;

class CastsAttrTest extends TestCase
{
    /**
     * @var $connection Connection
     */
    protected $connection;

    protected function setUp(): void
    {
        parent::setUp();
        $config = new Config(MYSQL_CONFIG);
        $this->connection = new Connection($config);
        DbManager::getInstance()->addConnection($this->connection);
        $connection = DbManager::getInstance()->getConnection();
        $this->assertTrue($connection === $this->connection);
    }

    public function testAdd()
    {
        $testUserModel = new TestCastsModel();
        $testUserModel->state = 0;
        $testUserModel->name = 'Siam';
        $testUserModel->age = 100;
        $testUserModel->addTime = date('Y-m-d H:i:s');
        $data = $testUserModel->save();
        $this->assertIsInt($data);

        $testUserModel = new TestCastsModel();
        $testUserModel->state = 1;
        $testUserModel->name = 'Siam222';
        $testUserModel->age = 100;
        $testUserModel->addTime = date('Y-m-d H:i:s');
        $data = $testUserModel->save();
        $this->assertIsInt($data);
    }

    public function testFloat()
    {
        $test = TestCastsModel::create()->get([
            'state' => 0
        ]);
        $this->assertIsFloat($test->id);
    }

    public function testInt()
    {
        $test = TestCastsModel::create()->get([
            'state' => 0
        ]);
        $this->assertIsInt($test->age);
    }

    public function testTimestamp()
    {
        $test = TestCastsModel::create()->get([
            'state' => 0
        ]);
        $this->assertIsInt($test->addTime);
    }

    public function testBool()
    {
        $test = TestCastsModel::create()->get([
            'state' => 0
        ]);
        $this->assertIsBool($test->state);
    }

    public function testString()
    {
        $test = TestCastsModel::create();
        $test->setAttr('test_string', 1);
        $this->assertIsString($test->test_string);
    }

    public function testJson()
    {
        $test = TestCastsModel::create();
        $test->setAttr('test_json', [
            'name' => 'siam'
        ]);

        $this->assertInstanceOf(\stdClass::class, $test->test_json);
    }

    public function testArray()
    {
        $test = TestCastsModel::create();
        $test->setAttr('test_array', [
            'name' => 'siam'
        ]);

        $this->assertIsArray($test->test_array);
    }

    public function testDate()
    {
        $test = TestCastsModel::create();
        $test->setAttr('test_date', time());
        $this->assertEquals(date("Y-m-d"), $test->test_date);
    }

    public function testDateTime()
    {
        $test = TestCastsModel::create();
        $test->setAttr('test_datetime', time());
        $this->assertEquals(date("Y-m-d H:i:s"), $test->test_datetime);
    }

    public function testDelete()
    {
        $res = TestCastsModel::create()->destroy(null, true);
        $this->assertIsInt($res);
    }
}

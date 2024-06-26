<?php

namespace EasySwoole\ORM\Tests;

use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Tests\models\ReuniteTableModel;
use PHPUnit\Framework\TestCase;

class ReuniteTableTest extends TestCase
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

        ReuniteTableModel::create();
    }

    /**
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    function testAdd()
    {
        $model = ReuniteTableModel::create([
            'pk_1' => 1,
            'pk_2' => 2
        ])->save();
        $this->assertIsInt($model);

        $model = ReuniteTableModel::create([
            'pk_1' => 1,
            'pk_2' => 1
        ])->save();
        $this->assertIsInt($model);
    }

    /**
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    function testGet()
    {
        $model = ReuniteTableModel::create()->get();
        $this->assertInstanceOf(ReuniteTableModel::class, $model);

        $model = ReuniteTableModel::create()->get([
            'pk_1' => 1,
            'pk_2' => 1
        ]);
        $this->assertInstanceOf(ReuniteTableModel::class, $model);
        $deleteOne = $model->destroy();
        $this->assertEquals(1, $deleteOne);
    }

    function testUpdate()
    {
        $model = ReuniteTableModel::create()->get([
            'pk_1' => 1,
            'pk_2' => 2
        ]);
        $this->assertInstanceOf(ReuniteTableModel::class, $model);
        $model->pk_2 = 'new';
        $updateRes = $model->update();
        $this->assertEquals(true, $updateRes);
        $this->assertEquals(1, $model->lastQueryResult()->getAffectedRows());
    }


    /**
     * @throws \EasySwoole\ORM\Exception\Exception
     * @throws \Throwable
     */
    function testDeleteAll()
    {
        $int = ReuniteTableModel::create()->destroy(null, true);
        $this->assertIsInt($int);
    }
}

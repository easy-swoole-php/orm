<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2020/7/26 0026
 * Time: 8:12
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Exception\Exception;
use EasySwoole\ORM\Tests\models\TestRelationModel;
use EasySwoole\ORM\Tests\models\TestTimeStampModel;
use EasySwoole\ORM\Utility\Schema\Table;
use PHPUnit\Framework\TestCase;

class ModelCloneCreateTest extends TestCase
{
    /**
     * @var $connection Connection
     */
    protected $connection;

    protected $tableName = 'test_user_model';

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $config = new Config(MYSQL_CONFIG);
        $this->connection = new Connection($config);

        DbManager::getInstance()->addConnection($this->connection);
        $connection = DbManager::getInstance()->getConnection();
        $this->assertTrue($connection === $this->connection);
        $this->createtTiamstampTestTable();
    }

    public function createtTiamstampTestTable()
    {
        $tableDDL = new Table($this->tableName);
        $tableDDL->colInt('id', 11)->setIsPrimaryKey()->setIsAutoIncrement();
        $tableDDL->colVarChar('name', 255);
        $tableDDL->colTinyInt('age', 1);
        $tableDDL->colDateTime('create_time')->setIsNotNull(false);
        $tableDDL->colDateTime('update_time')->setIsNotNull(false);
        $tableDDL->colInt('create_at', 10)->setIsNotNull(false);
        $tableDDL->colInt('update_at', 10)->setIsNotNull(false);
        $tableDDL->setIfNotExists();
        $sql = $tableDDL->__createDDL();
        $query = new QueryBuilder();
        $query->raw($sql);
        DbManager::getInstance()->query($query);
    }

    /**
     * @throws Exception
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \Throwable
     */
    public function testGet()
    {
        TestTimeStampModel::create([
            'name' => 'siam_test_clone',
            'age' => 22,
        ])->save();
        $model = new TestRelationModel();
        $cloneModel = $model->tableName("tiamstamp_test")->get();
        $this->assertEquals($cloneModel->getTableName(), 'tiamstamp_test');
    }

    /**
     * @throws Exception
     * @throws \EasySwoole\Mysqli\Exception\Exception
     * @throws \Throwable
     */
    public function testSaveAll()
    {
        $model = new TestRelationModel();
        $model->tableName("tiamstamp_test")->saveAll([
            [
                'name' => 'siam_test_clone_1',
                'age' => 22,
            ],
            [
                'name' => 'siam_test_clone_2',
                'age' => 22,
            ]
        ]);
        $com = TestTimeStampModel::create()->get([
            'name' => 'siam_test_clone_2'
        ]);
        $this->assertEquals($com->name, "siam_test_clone_2");
    }

    public function testAfter()
    {
        TestTimeStampModel::create()->destroy(null, true);
    }
}

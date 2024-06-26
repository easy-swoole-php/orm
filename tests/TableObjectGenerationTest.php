<?php
/**
 * 生成表
 * User: Tioncico
 * Date: 2019/10/22 0022
 * Time: 14:10
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Utility\Schema\Table;
use EasySwoole\ORM\Utility\TableObjectGeneration;
use PHPUnit\Framework\TestCase;

class TableObjectGenerationTest extends TestCase
{
    /**
     * @var $generation TableObjectGeneration
     */
    protected $generation;
    /**
     * @var $connection Connection
     */
    protected $connection;

    protected $tableName = 'test';


    protected function setUp(): void
    {
        parent::setUp();
        $config = new Config(MYSQL_CONFIG);
        $connection = new Connection($config);
        $this->connection = $connection;
        $this->generation = new TableObjectGeneration($connection, $this->tableName);
        $this->createTestTable();
    }

    function createTestTable()
    {
        $sql = "DROP TABLE  if exists {$this->tableName};";
        $query = new QueryBuilder();
        $query->raw($sql);
        $data = $this->connection->defer()->query($query);
        $this->assertTrue($data->getResult());

        $tableDDL = new Table($this->tableName);
        $tableDDL->colInt('id', 11)->setIsPrimaryKey()->setIsAutoIncrement();
        $tableDDL->colVarChar('name', 255);
        $tableDDL->colTinyInt('age', 1);
        $tableDDL->colDateTime('addTime');
        $tableDDL->colTinyInt('state', 1);
        $tableDDL->setIfNotExists();
        $sql = $tableDDL->__createDDL();
        $query->raw($sql);
        $data = $this->connection->defer()->query($query);
        $this->assertTrue($data->getResult());
    }

    function testGetTableInfo()
    {
        $data = $this->generation->getTableColumnsInfo();
        $this->assertIsArray($data);
    }

}

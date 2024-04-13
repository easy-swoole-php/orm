<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/10/22 0022
 * Time: 10:19
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\Db\Result;
use PHPUnit\Framework\TestCase;

class ConnectionTest extends TestCase
{
    /**
     * @var $connection Connection
     */
    protected $connection;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $config = new Config(MYSQL_CONFIG);
        $this->connection = new Connection($config);
    }

    public function testQuery()
    {
        $queryBuild = new QueryBuilder();
        $queryBuild->raw("show tables");
        $data = $this->connection->defer()->query($queryBuild);
        $this->assertTrue($data instanceof Result);
    }

    // getPool变更为受保护 不再测试
//    function testGetPool(){
//        /**
//         * @var $data MysqlPool
//         */
//        $data = $this->connection->getPool();
//        $this->assertTrue($data instanceof MysqlPool);
//        /**
//         * @var $mysqli Client
//         */
//        $mysqli = $data->getObj();
//        $queryBuild = new QueryBuilder();
//        $queryBuild->raw("show tables");
//
//        $data = $mysqli->rawQuery('show tables');
//        $this->assertIsArray($data);
//    }
}
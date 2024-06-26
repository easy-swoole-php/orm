<?php
/**
 * Created by PhpStorm.
 * User: Siam
 * Date: 2020/2/27
 * Time: 9:55
 */

namespace EasySwoole\ORM\Tests;

use EasySwoole\Mysqli\QueryBuilder;
use EasySwoole\ORM\AbstractModel;
use EasySwoole\ORM\Db\Config;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\ORM\Tests\models\Roles;
use EasySwoole\ORM\Tests\models\UserInfo;
use EasySwoole\ORM\Tests\models\UserRole;
use EasySwoole\ORM\Tests\models\UserRoleDifferentField;
use EasySwoole\ORM\Tests\models\Users;
use PHPUnit\Framework\TestCase;

class BelongsToManyTest extends TestCase
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

        UserInfo::create();
        UserRole::create();
        UserRoleDifferentField::create();
        Users::create();
        Roles::create();
    }

    public function testInsert()
    {
        ### 插入第1个用户
        $user = Users::create([
            'name' => 'SiamBelongsToManySimpleRole'
        ])->save();
        $this->assertIsInt($user);

        $userRole = UserRole::create([
            'user_id' => $user,
            'role_id' => 1
        ])->save();
        $this->assertIsInt($userRole);

        ### 插入第2个用户
        $userManyRole = Users::create([
            'name' => 'SiamBelongsToManyManyRole'
        ])->save();
        $this->assertIsInt($userManyRole);
    }

    private function truncateTable(string $table)
    {
        $sql = "truncate {$table}";
        $builder = new QueryBuilder();
        $builder->raw($sql);
        DbManager::getInstance()->query($builder, true);
    }

    private function mockInsert()
    {
        // 默认角色列表插入
        Roles::create([
            'role_name' => '管理员'
        ])->save();
        Roles::create([
            'role_name' => 'VIP用户'
        ])->save();
        Roles::create([
            'role_name' => '普通用户'
        ])->save();

        ### 插入第1个用户
        $user = Users::create([
            'name' => 'SiamBelongsToManySimpleRole'
        ])->save();

        UserRole::create([
            'user_id' => $user,
            'role_id' => 1
        ])->save();

        UserRoleDifferentField::create([
            'u_id' => $user,
            'r_id' => 1
        ])->save();

        // 添加user_info信息
        UserInfo::create([
            'user_id'    => $user,
            'user_email' => '1@qq.com'
        ])->save();


        ### 插入第2个用户
        $userManyRole = Users::create([
            'name' => 'SiamBelongsToManyManyRole'
        ])->save();

        UserRole::create([
            'user_id' => $userManyRole,
            'role_id' => 1
        ])->save();
        UserRoleDifferentField::create([
            'u_id' => $userManyRole,
            'r_id' => 1
        ])->save();

        UserRole::create([
            'user_id' => $userManyRole,
            'role_id' => 3
        ])->save();
        UserRoleDifferentField::create([
            'u_id' => $userManyRole,
            'r_id' => 3
        ])->save();

        // 添加user_info信息
        UserInfo::create([
            'user_id'    => $userManyRole,
            'user_email' => '3@qq.com'
        ])->save();
    }

    private function truncateAllTables()
    {
        $this->truncateTable('users');
        $this->truncateTable('user_info');
        $this->truncateTable('roles');
        $this->truncateTable('user_role');
        $this->truncateTable('user_role_different_field');
    }

    // 关联查询
    public function testGet()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        $user = Users::create()->where('name', 'SiamBelongsToManySimpleRole')->get();
        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals(count($user->roles()), 1);
        $this->assertInstanceOf(Roles::class, $user->roles()[0]);
        $this->assertEquals('管理员', $user->roles()[0]->role_name);

        $userMany = Users::create()->where('name', 'SiamBelongsToManyManyRole')->get();
        $this->assertEquals(count($userMany->roles()), 2);
        $this->assertInstanceOf(Roles::class, $userMany->roles()[0]);
        $this->assertInstanceOf(Roles::class, $userMany->roles()[1]);
        $this->assertEquals('管理员', $userMany->roles()[0]->role_name);
        $this->assertEquals('普通用户', $userMany->roles()[1]->role_name);

        // 自定义键名
        $user = Users::create()->where('name', 'SiamBelongsToManySimpleRole')->get();
        $this->assertInstanceOf(Users::class, $user);

        $this->assertEquals(count($user->roles_different_field()), 1);
        $this->assertInstanceOf(Roles::class, $user->roles_different_field()[0]);
        $this->assertEquals('管理员', $user->roles_different_field()[0]->role_name);

        $userMany = Users::create()->where('name', 'SiamBelongsToManyManyRole')->get();
        $this->assertEquals(count($userMany->roles_different_field()), 2);
        $this->assertInstanceOf(Roles::class, $userMany->roles_different_field()[0]);
        $this->assertInstanceOf(Roles::class, $userMany->roles_different_field()[1]);
        $this->assertEquals('管理员', $userMany->roles_different_field()[0]->role_name);
        $this->assertEquals('普通用户', $userMany->roles_different_field()[1]->role_name);
    }

    public function testToArray()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        $user = Users::create()->where('name', 'SiamBelongsToManySimpleRole')->get();
        $user->roles();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles'][0]['role_name'], '管理员');

        $user = Users::create()->where('name', 'SiamBelongsToManyManyRole')->get();
        $user->roles();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles'][0]['role_name'], '管理员');
        $this->assertEquals($user->toArray(false, false)['roles'][1]['role_name'], '普通用户');

        // 自定义键名
        $user = Users::create()->where('name', 'SiamBelongsToManySimpleRole')->get();
        $user->roles_different_field();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');

        $user = Users::create()->where('name', 'SiamBelongsToManyManyRole')->get();
        $user->roles_different_field();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][1]['role_name'], '普通用户');
    }

    public function testWithGet()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        $user = Users::create()->where('name', 'SiamBelongsToManySimpleRole')->with(['roles'])->get();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles'][0]['role_name'], '管理员');

        $user = Users::create()->where('name', 'SiamBelongsToManyManyRole')->with(['roles'])->get();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles'][0]['role_name'], '管理员');
        $this->assertEquals($user->toArray(false, false)['roles'][1]['role_name'], '普通用户');
        // 自定义键名
        $user = Users::create()->where('name', 'SiamBelongsToManySimpleRole')->with(['roles_different_field'])->get();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');

        $user = Users::create()->where('name', 'SiamBelongsToManyManyRole')->with(['roles_different_field'])->get();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][1]['role_name'], '普通用户');
    }

    public function testWithAll()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        $user = Users::create()->with(['roles'])->all();
        $this->assertInstanceOf(Users::class, $user[0]);
        $this->assertInstanceOf(Users::class, $user[1]);

        $this->assertEquals($user[0]->toArray(false, false)['roles'][0]['role_name'], '管理员');
        $this->assertEquals($user[1]->toArray(false, false)['roles'][0]['role_name'], '管理员');
        $this->assertEquals($user[1]->toArray(false, false)['roles'][1]['role_name'], '普通用户');

        $user = Users::create()->with(['roles_different_field'])->all();
        $this->assertInstanceOf(Users::class, $user[0]);
        $this->assertInstanceOf(Users::class, $user[1]);

        $this->assertEquals($user[0]->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');
        $this->assertEquals($user[1]->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');
        $this->assertEquals($user[1]->toArray(false, false)['roles_different_field'][1]['role_name'], '普通用户');
    }

    public function testCallCondition()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        $user = Users::create()->with(['roles_different_field_call'])->all();
        $this->assertInstanceOf(Users::class, $user[0]);
        $this->assertInstanceOf(Users::class, $user[1]);
        // 在模型中设置了field  没有role_name
        $this->assertTrue(!isset($user[0]->toArray(false, false)['roles_different_field_call'][0]['role_name']));
        $this->assertTrue(!isset($user[1]->toArray(false, false)['roles_different_field_call'][0]['role_name']));
        $this->assertTrue(!isset($user[1]->toArray(false, false)['roles_different_field_call'][1]['role_name']));
    }

    // 关联查询同时使用 join
    public function testWithJoinAll()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        $user = Users::create()
            ->alias('users')
            ->where('users.name', 'SiamBelongsToManySimpleRole')
            ->with(['roles'])
            ->join('user_info user_info', 'users.user_id = user_info.user_id', 'INNER')
            ->get();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles'][0]['role_name'], '管理员');
        $this->assertEquals($user->toArray(false, false)['user_email'], '1@qq.com');

        // 自定义键名
        $user = Users::create()
            ->alias('users')
            ->where('users.name', 'SiamBelongsToManySimpleRole')
            ->with(['roles_different_field'])
            ->join('user_info user_info', 'users.user_id = user_info.user_id', 'INNER')
            ->get();
        $this->assertInstanceOf(Users::class, $user);
        $this->assertEquals($user->toArray(false, false)['roles_different_field'][0]['role_name'], '管理员');
        $this->assertEquals($user->toArray(false, false)['user_email'], '1@qq.com');

        /** @var AbstractModel[] $user */
        $user = Users::create()
            ->alias('users')
            ->field('user_info.user_email')
            ->with(['roles_join'])
            ->join('user_info as user_info', 'users.user_id = user_info.user_id', 'INNER')
            ->all();
        $this->assertIsArray($user);
        $this->assertTrue(count($user) > 0);
        foreach ($user as $k => $v) {
            $this->assertStringContainsString('@qq.com', $v->toArray(false, false)['user_email']);
        }
    }

    public function testDelete()
    {
        $this->truncateAllTables();
        $this->mockInsert();

        Users::create()->destroy(null, true);
        UserRole::create()->destroy(null, true);
        UserRoleDifferentField::create()->destroy(null, true);

        $this->assertIsInt(1);
    }
}

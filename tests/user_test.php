<?php
/**
 * Created by PhpStorm.
 * User: bojan
 * Date: 2022-10-13
 * Time: 00:28
 */

namespace mod_myplugin;

/**
 * Class test_user
 * @package mod_myplugin
 */
class user_test extends \advanced_testcase {



    private $payload = [
        'name' => 'Test',
        'family_name' => 'User',
        'email' => 'test@test.com'
    ];

    /**
     * @throws \dml_exception
     */
    public function test_token() {
        $mock = $this->getMockBuilder('\auth_cognito\model\User')
            ->setMethods(array('getDataFromToken'))
            ->getMock();

        $mock->expects($this->once())
            ->method('getDataFromToken')
            ->will($this->returnValue($this->payload));

        $this->assertEquals(json_encode($this->payload), json_encode($mock->getDataFromToken('','')));
    }

    /**
     * @throws \dml_exception
     */
    public function test_create_user() {
        global $DB;

        $mock = $this->getMockBuilder('\auth_cognito\model\User')
            ->setMethods(array('createUser'))
            ->getMock();

        $mock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue(1));


        $this->assertSame(1, $mock->createUser($this->payload));

    }
}
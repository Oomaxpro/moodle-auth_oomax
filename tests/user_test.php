<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file is part of the Oomax Pro Authentication package.
 *
 * @package     auth_cognito
 * @author      Bojan Bazdar / Dustin Brisebois
 * @license     GPL
 * @copyright   Oomax
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Oomax;

/**
 * Class test_user
 * @package mod_myplugin
 */
class user_test extends \advanced_testcase {
    private $payload = [
        'name' => 'Test',
        'family_name' => 'User',
        'email' => 'test@test.com',
    ];

    /**
     * @throws \dml_exception
     */
    public function test_token() {
        $mock = $this->getMockBuilder('\Oomax\Model\User')
            ->setMethods(['getDataFromToken'])
            ->getMock();

        $mock->expects($this->once())
            ->method('getDataFromToken')
            ->will($this->returnValue($this->payload));

        $this->assertEquals(json_encode($this->payload), json_encode($mock->getDataFromToken('', '')));
    }

    /**
     * @throws \dml_exception
     */
    public function test_create_user() {
        global $DB;

        $mock = $this->getMockBuilder('\Oomax\Model\User')
            ->setMethods(['createUser'])
            ->getMock();

        $mock->expects($this->once())
            ->method('createUser')
            ->will($this->returnValue(1));

        $this->assertSame(1, $mock->createUser($this->payload));

    }
}

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
 * @license     https://opensource.org/license/mit MIT
 * @copyright   Oomax
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Oomax;

/**
 * Class test_user
 * @package oomax
 */
class settings_test extends \advanced_testcase {

    /**
     * @throws \dml_exception
     */
    public function test_settings_login_url() {

        $loginurl = get_config('auth_cognito', 'oomax_serverless_login_url');
        $this->assertIsString($loginurl, 'Settings: Login url is missing');
    }
}

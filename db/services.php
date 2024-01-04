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
 * auth_cognito block settings
 *
 * @package    auth_cognito
 * @copyright  2022
 * @author     Bojan Bazdar
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$lambdaprocessupload = Array(
        'classname' => 'cognito_external',
        'methodname' => 'process_upload',
        'classpath' => 'auth/cognito/externallib.php',
        'description' => 'Upload Processable Files.',
        'type' => 'write',
        'capabilities' => 'moodle/user:create'
      );

$lambdacreatessouser = Array(
        'classname' => 'cognito_external',
        'methodname' => 'create_users',
        'classpath' => 'auth/cognito/externallib.php',
        'description' => 'Create cognito users.',
        'type' => 'write',
        'capabilities' => 'moodle/user:create'
      );

// auth/cognito/externallib.php does not exist
//
//$functions = Array(
//  'cognito_create_sso_user' => $lambdacreatessouser
//);
//
//if (isset($CFG->SSO_EXPERIMENTAL) && $CFG->SSO_EXPERIMENTAL) {
//    $functions['cognito_process_upload'] = $lambdaprocessupload;
//}

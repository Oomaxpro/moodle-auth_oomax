<?php
/** 
 * This file is part of Moodle - http://moodle.org/
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 * php version 8.1.1

 * @category Settings
 * auth_oomax auth settings
 
 * @package   Auth_Oomax
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @copyright 2022 OOMAX PRO SOFTWARE INC.
 
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @link    http://www.gnu.org/copyleft/gpl.html
 */

defined('MOODLE_INTERNAL') || die;

$lambdaprocessupload = [
  'classname' => 'cognito_external',
  'methodname' => 'process_upload',
  'classpath' => 'auth/cognito/externallib.php',
  'description' => 'Upload Processable Files.',
  'type' => 'write',
  'capabilities' => 'moodle/user:create',
];

$lambdacreatessouser = [
  'classname' => 'cognito_external',
  'methodname' => 'create_users',
  'classpath' => 'auth/cognito/externallib.php',
  'description' => 'Create cognito users.',
  'type' => 'write',
  'capabilities' => 'moodle/user:create',
];

$functions = [
  'cognito_create_sso_user' => $lambdacreatessouser,
];

if (isset($CFG->SSO_EXPERIMENTAL) && $CFG->SSO_EXPERIMENTAL) {
    $functions['cognito_process_upload'] = $lambdaprocessupload;
}

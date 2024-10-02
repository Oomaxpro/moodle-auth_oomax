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
 * @package   auth_cognito
 * @copyright 2024 OOMAX PRO SOFTWARE INC
 * @author    Dustin Brisebois <dustin@oomaxpro.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/** global $CFG; - Variable "$CFG" is not expected in a lang file */

$string['auth_cognito_description'] = "An Authentication Plugin by Oomax Pro";
$string['cognito_settings'] = 'Oomax Pro Settings';
$string['auth_cognito_title'] = "Oomax Pro Authentication Plugin";
$string['pluginname'] = 'Oomax Pro Authentication';
$string['auth_cognito_settings'] = "Oomax Pro Authentication Settings";
$string['cognito_serverless_login_url'] = 'Oomax Pro serverless login url';
$string['public_key_settings'] = 'Oomax Pro public key';
$string['config_lock_email'] = 'Lock email address';
$string['config_lock_email_desc'] = 'When checked users cannot update their email address';
$string['oomax_uri'] = 'Oomax SSO URI';
$string['sso_bypass'] = 'Enable SSO By-Pass';
$string['sso_bypass_desc'] = 'This enables SSO forwarding to the OOMAX URL and allows bypassing forwarding, bypass requires <i>sso=1</i> included in paths';
$string['cachedef_oomax_cache'] = 'Oomax Cache Store';
$string['oomax_uri_desc'] = 'Oomax Pro Server URI Address';
$string['audience_fail_enrol'] = 'Failed to add user to audience {$a->cohortid} : {$a->message}';
$string['invalid_token'] = 'User token is invalid.';
$string['course_not_exist'] = 'Course {$a->courseid} does not exist';
$string['course_failed_enrol'] = 'Failed to enrol user in course {$a->courseid}';
$string['course_user_enrolled'] = 'User is already enrolled in course {$a->courseid}';
$string['course_failed_enrol_msg'] = 'Failed to enrol user in course {$a->courseid} : {$a->message}';
$string['groups_failed_user_group'] = 'Failed to add user to group {$a->groupid}';
$string['groups_failed_user_group_msg'] = 'Failed to add user to group {$a->groupid} : {$a->message}';

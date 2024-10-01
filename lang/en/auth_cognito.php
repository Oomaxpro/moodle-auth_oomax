<?php
 /**
  * This file is part of the Oomax Pro Authentication package.
  *
  * @package     auth_oomax
  * @author      Bojan Bazdar
  * @license     MIT
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  *
  */

global $CFG;

// require_once($CFG->dirroot.'/auth/oomax/vendor/autoload.php');

$string['pluginname'] = 'Oomax Pro Authentication';
$string['auth_cognito_title'] = "Oomax Pro Authentication Plugin";
$string['auth_cognito_settings'] = "Oomax Pro Authentication Settings";
$string['auth_cognito_description'] = "An Authentication Plugin by Oomax Pro";
$string['cognito_settings'] = 'Oomax Pro Settings';
$string['public_key_settings'] = 'Oomax Pro public key';
$string['cognito_serverless_login_url'] = 'Oomax Pro serverless login url';
$string['config_lock_email'] = 'Lock email address';
$string['config_lock_email_desc'] = 'When checked users cannot update their email address';

$string['sso_bypass'] = 'Enable SSO By-Pass';
$string['sso_bypass_desc'] = 'This enables SSO forwarding to the OOMAX URL and allows bypassing forwarding, bypass requires <i>sso=1</i> included in paths';

$string['oomax_uri'] = 'Oomax SSO URI';
$string['oomax_uri_desc'] = 'Oomax Pro Server URI Address';

$string['cachedef_oomax_cache'] = 'Oomax Cache Store';

$string['invalid_token'] = 'User token is invalid.';

$string['audience_fail_enrol'] = "Failed to add user to audience {$a->cohortid} : {$a->message}";
$string['course_not_exist'] = "Course {$a->courseid} does not exist";
$string['course_user_enrolled'] = "User is already enrolled in course {$a->courseid}";
$string['course_failed_enrol'] = "Failed to enrol user in course {$a->courseid}";
$string['course_failed_enrol_msg'] = "Failed to enrol user in course {$a->courseid} : {$a->message}";
$string['groups_failed_user_group'] = "Failed to add user to group {$a->groupid}";
$string['groups_failed_user_group_msg'] = "Failed to add user to group {$a->groupid} : {$a->message}";


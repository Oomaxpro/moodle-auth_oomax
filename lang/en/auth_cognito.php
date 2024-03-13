<?php
 /**
  * This file is part of the Oomax Pro Authentication package.
  *
  * @package     auth_cognito
  * @author      Bojan Bazdar
  * @license     MIT
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  *
  */

global $CFG;

require_once($CFG->dirroot.'/auth/cognito/vendor/autoload.php');

$string['pluginname'] = 'Oomax Pro Authentication';
$string['auth_cognitotitle'] = "Oomax Pro Authentication Plugin";
$string['auth_cognito_description'] = "An Authentication Plugin by Oomax Pro";
$string['auth_cognito_settings'] = 'Oomax Pro Settings';
$string['public_key_settings'] = 'Oomax Pro public key';
$string['oomax_serverless_login_url'] = 'Oomax Pro serverless login url';
$string['config_lock_email'] = 'Lock email address';
$string['config_lock_email_desc'] = 'When checked users cannot update their email address';

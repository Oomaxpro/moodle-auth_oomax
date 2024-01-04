<?php
 /**
  * This file is part of the Lambda Solutions Lambda Cognito Authentication package.
  *
  * Copyright (c) 2022 Lambda Solutions
  *
  * @package     auth_cognito
  * @author      Bojan Bazdar
  * @copyright   2022 Lambda Solutions
  * @license     MIT
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  *
  */

global $CFG;

require_once($CFG->dirroot.'/auth/cognito/vendor/autoload.php');

$string['pluginname'] = 'Lambda Cognito Authentication';
$string['auth_cognitotitle'] = "Lambda Cognito Authentication Plugin";
$string['auth_cognito_description'] = "An Authentication Plugin by Lambda Solutions";
$string['auth_cognito_settings'] = 'Lambda Cognito Settings';
$string['public_key_settings'] = 'Cognito public key';
$string['lambda_serverless_login_url'] = 'Lambda serverless login url';
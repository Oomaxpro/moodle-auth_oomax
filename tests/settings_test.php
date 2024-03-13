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
class settings_test extends \advanced_testcase {

    /**
     * @throws \dml_exception
     */
    public function test_settings_login_url() {

        $loginUrl = get_config('auth_cognito', 'oomax_serverless_login_url');
        $this->assertIsString($loginUrl, 'Settings: Login url is missing');
    }

    /**
     * @throws \dml_exception
     */
    public function test_settings_public_key() {
        $publickey = get_config('auth_cognito', 'public_key');
        $this->assertIsString($publickey, 'Settings: Public key is missing');
    }
}
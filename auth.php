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
 * @author    Bojan Bazdar / Dustin Brisebois <dustin@oomaxpro.com>
 * @license   https://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2024 OOMAX PRO SOFTWARE INC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');

/**
 * Class auth_plugin_oomax
 * 
 * @category Engine
 * @package  Auth_Cognito
 * @author   Bojan Bazdar / Dustin Brisebois <dustin@oomaxpro.com>
 * 
 */
class auth_plugin_cognito extends auth_plugin_base {

    /**
     * Control auth requests from OOMAX
     * 
     * @var $logouturl string
     * @var $plugin string
     */
    private $logouturl = '';
    private $plugin = '';

    /**
     * Constructor. No parameters given.
     * As non-static, create the AuthManage connect and get the mode
     * @author Bojan Bazdar / Dustin Brisebois <dustin@oomaxpro.com>
     */
    public function __construct() {
        global $CFG, $SESSION;

        $plugin = 'cognito';
        $this->plugin = "auth_{$plugin}";
        $this->authtype = $plugin;
        if (!is_null($SESSION) && property_exists($SESSION, 'logout')) {
            $this->logouturl = $SESSION->logout;
        }
        $this->config = get_config("auth_{$plugin}");
    }

    /**
     * Postlogout_Hook for Redirecting User on Logout
     * 
     * @param $user stdClass 
     * 
     * @throws moodle_exception
     * 
     * @return boolean
     */
    public function postlogout_hook($user) {
        if ($this->logouturl) {
            redirect($this->logouturl);
            exit;
        }
    }

    /**
     * Can change password?
     * @return bool if false
     */
    public function can_change_password(): bool {
        return false;
    }

    /**
     * Can edit profile?
     * 
     * @return bool
     */
    public function can_edit_profile(): bool {
        return false;
    }

    /**
     * Can reset password?
     * @return bool
     */
    public function can_reset_password(): bool {
        return false;
    }

    /**
     * Is plugin internal?
     * 
     * @return bool
     */
    public function is_internal(): bool {
        return true;
    }

    /**
     * Encrypted Cookie manager for wantsurl
     * 
     * @return void
     */
    private function calculate_wantsurl() {
        if (isset($_COOKIE['oomaxHome'])) {
            global $CFG;

            $options = 0;
            $ciphering = "AES-256-CBC";
            $decryptioniv = substr(bin2hex($CFG->wwwroot), -16);
            $decryptionkey = parse_url($CFG->wwwroot)['host'];
            $decryption = openssl_decrypt($_COOKIE['oomaxHome'], $ciphering,  $decryptionkey, $options, $decryptioniv);
            redirect("https://{$decryption}");
        }
    }

    /**
     * OAuth smart handler for UI mapping
     * 
     * @param $issuer \core\outh2\issuer 
     * 
     * @return bool
     */
    private function is_ready_for_login_page(\core\oauth2\issuer $issuer) {
        return $issuer->get('enabled') && $issuer->is_configured() && empty($issuer->get('showonloginpage'));
    }

    /**
     * Login Idp List handler for UI artifacts
     * 
     * @param $wantsurl string
     * @param $details  bool  = false
     * 
     * @return Array
     */
    public function loginpage_idp_list($wantsurl, Bool $details = false) {
        $result = [];
        $providers = \core\oauth2\api::get_all_issuers();
        if (empty($wantsurl)) {
            $wantsurl = '/';
        }
        foreach ($providers as $idp) {
            if ($this->is_ready_for_login_page($idp)) {
                $params = ['id' => $idp->get('id'), 'wantsurl' => $wantsurl, 'sesskey' => sesskey()];
                $url = new moodle_url('/login/index.php', $params);
                $icon = $idp->get('image');
                $result[] = ['url' => $url, 'iconurl' => $icon, 'name' => $idp->get('name'), 'authtype' => 'oauth2'];
            }
        }
        return $result;
    }
}


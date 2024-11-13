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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright 2024 OOMAX PRO SOFTWARE INC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/vendor/autoload.php');
require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');

use auth_cognito\local\token;
use auth_cognito\local\user;
use auth_cognito\local\courses;
use auth_cognito\local\groups;
use auth_cognito\local\audiences;
use auth_cognito\local\messages;
use core\oauth2;

/**
 * Class auth_plugin_oomax
 * @package  Auth_Cognito
 * @author   Bojan Bazdar / Dustin Brisebois <dustin@oomaxpro.com>
 */
class auth_plugin_cognito extends \auth_plugin_base {


    /** @var string The passed logout URL for the origin brand */
    private $logouturl = '';

    /** @var string The plugin making the request */
    private $plugin = '';

    /** @var string The wantsurl on non-authorized users */
    private $wantsurl;

    /**
     * Constructor. No parameters given.
     * As non-static, create the AuthManage connect and get the mode
     * @var $plugin string
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
     * @param User $user
     * @throws moodle_exception
     * @return boolean
     */
    public function postlogout_hook($user) {
        if ($this->logouturl) {
            redirect($this->logouturl);
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
     * @return bool
     */
    public function is_internal(): bool {
        return true;
    }

    /**
     * Encrypted Cookie manager for wantsurl
     * @return void
     */
    private function calculate_wantsurl() {

        $bypass = optional_param('oomax', null, PARAM_RAW);
        if (isset($_COOKIE['oomaxhome']) && is_null($bypass) && $bypass == 'stop') {

            global $CFG;

            $options = 0;
            $ciphering = "AES-256-CBC";
            $decryptioniv = substr(bin2hex($CFG->wwwroot), -16);
            $decryptionkey = parse_url($CFG->wwwroot)['host'];
            $decryption = openssl_decrypt($_COOKIE['oomaxhome'], $ciphering,  $decryptionkey, $options, $decryptioniv);
            redirect("https://{$decryption}");
        }
    }

    /**
     * is_ready_for_login_page
     *
     * @param oauth2\issuer $issuer
     * @return void
     */
    private function is_ready_for_login_page(oauth2\issuer $issuer) {
        return $issuer->get('enabled') && $issuer->is_configured() && empty($issuer->get('showonloginpage'));
    }

    /**
     * loginpage_idp_list
     *
     * @param String $wantsurl
     * @param Bool $details
     * @return Array
     */
    public function loginpage_idp_list($wantsurl, Bool $details = false) {
        if (!$details) {
            return [];
        }

        $result = [];
        $providers = oauth2\api::get_all_issuers();
        if (empty($wantsurl)) {
            $wantsurl = '/';
        }
        foreach ($providers as $idp) {
            if ($this->is_ready_for_login_page($idp)) {
                $params = ['id' => $idp->get('id'), 'wantsurl' => $wantsurl, 'sesskey' => sesskey()];
                $url = new \moodle_url('/login/index.php', $params);
                $icon = $idp->get('image');
                $result[] = ['url' => $url, 'iconurl' => $icon, 'name' => $idp->get('name'), 'authtype' => 'oauth2'];
            }
        }
        return $result;
    }

    /**
     * auth prelogin hook
     *
     * @return void
     * @return Exception
     */
    public function loginpage_hook() {
        global $CFG, $SESSION;

        $token = optional_param('token', null, PARAM_RAW);
        $logout = optional_param('logout', null, PARAM_RAW);
        $courses = optional_param('courses', null, PARAM_SEQUENCE);
        $groups = optional_param('groups', null, PARAM_SEQUENCE);
        $audiences = optional_param('audiences', null, PARAM_SEQUENCE);
        $SESSION->logout = $logout;

        if (empty($token)) {
            $this->calculate_wantsurl();
        }

        if (!empty($token)) {
            $oomaxtoken = new \auth_cognito\local\token($token);
            $oomaxtoken->get_data_from_token();

            list($oomaxuser, $this->wantsurl) = $this->login_user($oomaxtoken);

            if (!empty($SESSION->wantsurl)) {
                $this->wantsurl = $SESSION->wantsurl;
            }

            // If payload exist process user.
            if ($oomaxtoken->is_authorized()) {
                $oomaxtoken->get_payload();
                $oomaxuser = new user($oomaxtoken);

                $oomaxuser->process_user_locale();
                $oomaxuser->user_login();
                $oomaxuser->generate_oomax_cookie();

                $this->process_gca($courses, $groups, $audiences, $oomaxtoken, $oomaxuser);
                if (empty($this->wantsurl)) {
                    $this->wantsurl = new \moodle_url(optional_param('wantsurl', $CFG->wwwroot, PARAM_URL));
                }

                redirect($this->wantsurl);
            } else {
                throw new \moodle_exception('oomaxtoken', '', '', null, get_string('invalid_token', 'auth_cognito'));
            }

        }
    }

    /**
     * Login User
     * @param \auth_cognito\local\token $token
     * @return Array
     */
    private function login_user(token $oomaxtoken): Array {
        $wantsurl = null;
        if (isset($SESSION->wantsurl)) {
            $wantsurl = $SESSION->wantsurl;
        }

        if ($oomaxtoken->is_authorized()) {
            $oomaxtoken->get_payload();
            $oomaxuser = new user($oomaxtoken);

            $oomaxuser->process_user_locale();
            $oomaxuser->user_login($oomaxuser);
            $oomaxuser->generate_oomax_cookie();
        }
        $wantsurl = null;
        if (isset($SESSION->wantsurl)) {
            $wantsurl = $SESSION->wantsurl;
        }
        return [$oomaxuser, $wantsurl];
    }

    /**
     * Process G (groups) C (courses) A (audiences)
     * @param Array $courses
     * @param Array $groups
     * @param Array $audiences
     * @param \oomax\local\Token $oomaxtoken
     * @param \oomax\local\User $oomaxuser
     * @return void
     */
    private function process_gca(
        String $courses, String $groups, String $audiences, token $oomaxtoken, user $oomaxuser) {
        if (!is_null($courses)) {
            $oomaxcourses = new courses($oomaxtoken, $courses);
            $oomaxcourses->process_courses($oomaxuser);
        }

        if (!is_null($groups)) {
            $oomaxgroups = new groups($oomaxtoken, $groups);
            $oomaxgroups->process_groups($oomaxuser);
        }

        if (!is_null($audiences)) {
            $oomaxaudiences = new audiences($oomaxtoken, $audiences);
            $oomaxaudiences->process_audiences($oomaxuser);
        }
    }
}

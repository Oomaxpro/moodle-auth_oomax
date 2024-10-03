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

use Oomax\Model;

/**
 * Class auth_plugin_oomax
 * @package  Auth_Cognito
 * @author   Bojan Bazdar / Dustin Brisebois <dustin@oomaxpro.com>
 */
class auth_plugin_cognito extends auth_plugin_base {


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

        if (!isloggedin()) {
            $this->wantsurl = $CFG->wwwroot.$_SERVER['REQUEST_URI'];
        }
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
        if (isset($_COOKIE['oomaxhome'])) {
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
     * @param \core\oauth2\issuer $issuer
     * @return void
     */
    private function is_ready_for_login_page(\core\oauth2\issuer $issuer) {
        return $issuer->get('enabled') && $issuer->is_configured() && empty($issuer->get('showonloginpage'));
    }

    /**
     * loginpage_idp_list
     *
     * @param String $wantsurl
     * @param Bool $details
     * @return void
     */
    public function loginpage_idp_list($wantsurl, Bool $details = false) {
        if (!$details) {
            return [];
        }

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

        $oomaxtoken = new Model\Token($token);
        $oomaxtoken->getdatafromtoken();

        list($oomaxuser, $this->wantsurl) = $this->loginuser($oomaxtoken);

        // If payload exist process user.
        if ($oomaxtoken->isauthorized()) {
            $oomaxtoken->getpayload();
            $oomaxuser = new Model\User($oomaxtoken);

            $oomaxuser->processuserlocale();
            $oomaxuser->userlogin($oomaxuser);
            $oomaxuser->generateoomaxcookie();

            $this->processgca($courses, $groups, $audiences, $oomaxtoken, $oomaxuser);
            if (is_null($this->wantsurl)) {
                $this->wantsurl = new moodle_url(optional_param('wantsurl', $CFG->wwwroot, PARAM_URL));
            }

            redirect($this->wantsurl);
        } else {
            throw new moodle_exception('oomaxtoken', '', '', null, get_string('invalid_token', 'auth_cognito'));
        }
    }

    /**
     * Login User
     * @param \Oomax\Model\Token $token
     * @return Array
     */
    private function loginuser(\Oomax\Model\Token $oomaxtoken): Array {
        $wantsurl = null;
        if (isset($SESSION->wantsurl)) {
            $wantsurl = $SESSION->wantsurl;
        }

        if ($oomaxtoken->isauthorized()) {
            $oomaxtoken->getpayload();
            $oomaxuser = new Model\User($oomaxtoken);

            $oomaxuser->processuserlocale();
            $oomaxuser->Userlogin($oomaxuser);
            $oomaxuser->generateoomaxcookie();
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
     * @param \Oomax\Model\Token $oomaxtoken
     * @param \Oomax\Model\User $oomaxuser
     * @return void
     */
    private function processgca(
        String $courses, String $groups, String $audiences,
        \Oomax\Model\Token $oomaxtoken, \Oomax\Model\User $oomaxuser) {
        if (!is_null($courses)) {
            $oomaxcourses = new Model\Courses($oomaxtoken, $courses);
            $oomaxcourses->processcourses($oomaxuser);
        }

        if (!is_null($groups)) {
            $oomaxgroups = new Model\Groups($oomaxtoken, $groups);
            $oomaxgroups->processgroups($oomaxuser);
        }

        if (!is_null($audiences)) {
            $oomaxaudiences = new Model\Audiences($oomaxtoken, $audiences);
            $oomaxaudiences->processaudiences($oomaxuser);
        }
    }
}

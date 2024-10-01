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

 * @package     auth_cognito
<<<<<<< HEAD
 * @copyright   Oomax

 * @author      Bojan Bazdar
 * @license     MIT
=======
 * @author      Bojan Bazdar / Dustin Brisebois
 * @license     GPL
 * @copyright   Oomax
>>>>>>> CLDOPS-525v5
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');
require_once($CFG->dirroot.'/user/lib.php');

/**
 * Class auth_plugin_oomax
 */
class auth_plugin_cognito extends auth_plugin_base {

    /**
     * @var string
     */
    private $logouturl = '';
    private $plugin = '';

    /**
     * @var string
     */
    private $plugin = '';

    /**
     * Constructor. No parameters given.
     * As non-static, create the AuthManage connect and get the mode
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
     * @param stdClass $user
     * @throws moodle_exception
     */
    public function postlogout_hook($user) {
        if ($this->logouturl) {
            redirect($this->logouturl);
            exit;
        }
    }

    /**
<<<<<<< HEAD
     * @return bool if false
     */

=======
     * Can change password?
     * @return bool if false
     */
>>>>>>> CLDOPS-525v5
    public function can_change_password(): bool {
        return false;
    }

    /**
<<<<<<< HEAD
=======
     * Can edit profile?
>>>>>>> CLDOPS-525v5
     * @return bool
     */
    public function can_edit_profile(): bool {
        return false;
    }

    /**
<<<<<<< HEAD
=======
     * Can reset password?
>>>>>>> CLDOPS-525v5
     * @return bool
     */
    public function can_reset_password(): bool {
        return false;
    }

    /**
<<<<<<< HEAD
=======
     * Is plugin internal?
>>>>>>> CLDOPS-525v5
     * @return bool
     */
    public function is_internal(): bool {
        return true;
    }

<<<<<<< HEAD
    private function calculate_wantsurl() {
        if (isset($_COOKIE['oomaxHome']))
        {
=======
    /**
     * Encrypted Cookie manager for wantsurl
     * @return void
     */
    private function calculate_wantsurl() {
        if (isset($_COOKIE['oomaxHome'])) {
>>>>>>> CLDOPS-525v5
            global $CFG;

            $options = 0;
            $ciphering = "AES-256-CBC";
            $decryptioniv = substr(bin2hex($CFG->wwwroot), -16);
            $decryptionkey = parse_url($CFG->wwwroot)['host'];
            $decryption = openssl_decrypt ($_COOKIE['oomaxHome'], $ciphering,  $decryptionkey, $options, $decryptioniv);
<<<<<<< HEAD

=======
>>>>>>> CLDOPS-525v5
            redirect("https://{$decryption}");
        }
    }

<<<<<<< HEAD
    public function loginpage_hook() {

        global $CFG, $USER;

        if (CLI_SCRIPT || AJAX_SCRIPT) {
            return;
        }

        $this->calculate_wantsurl();

        $token = optional_param('token', '', PARAM_RAW);
        $logout = optional_param('logout', '', PARAM_RAW);

        if ($CFG->forcelogin == true) {
            // force login!
        } else if ($USER->id == 0) {
            // not logged in
        } else if ($CFG->autologinguests == false || $CFG->guestloginbutton == false) {

            // no guest
        }
    }


    /**
     * @param \core\outh2\issuer issuer
     * @return bool
     */

=======
    /**
     * OAuth smart handler for UI mapping
     * @param \core\outh2\issuer issuer
     * @return bool
     */
>>>>>>> CLDOPS-525v5
    private function is_ready_for_login_page(\core\oauth2\issuer $issuer) {
        return $issuer->get('enabled') && $issuer->is_configured() && empty($issuer->get('showonloginpage'));
    }

<<<<<<< HEAD
    public function loginpage_idp_list($wantsurl, Bool $details = false) {

=======
    /**
     * Login Idp List handler for UI artifacts
     * @param $wantsurl
     * @param bool $details = false
     * @return Array
     */
    public function loginpage_idp_list($wantsurl, Bool $details = false) {
>>>>>>> CLDOPS-525v5
        $result = [];
        $providers = \core\oauth2\api::get_all_issuers();
        if (empty($wantsurl)) {
            $wantsurl = '/';
        }
        foreach ($providers as $idp) {
<<<<<<< HEAD

            if ($this->is_ready_for_login_page($idp)) {

=======
            if ($this->is_ready_for_login_page($idp)) {
>>>>>>> CLDOPS-525v5
                $params = ['id' => $idp->get('id'), 'wantsurl' => $wantsurl, 'sesskey' => sesskey()];
                $url = new moodle_url('/login/index.php', $params);
                $icon = $idp->get('image');
                $result[] = ['url' => $url, 'iconurl' => $icon, 'name' => $idp->get('name'), 'authtype' => 'oauth2'];
            }
        }
        return $result;
    }
<<<<<<< HEAD


    public function pre_user_login_hook(&$user) {

        // magic
        echo "<pre>";
        echo var_dump($user);
        echo "</pre>";
        die();
    }

    public function user_exists($username) {
        echo "User Exists: ". $username .'<br>';
        die();
    }
=======
>>>>>>> CLDOPS-525v5
}


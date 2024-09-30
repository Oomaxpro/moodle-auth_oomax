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
 * @package     auth_oomax
 * @author      Bojan Bazdar
 * @license     MIT
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
     * @return bool if false
     */
    public function can_change_password(): bool
    {
        return false;        
    }
    
    /**
     * 
     */
    public function can_edit_profile(): bool
    {
        return false;        
    }

    /**
     * 
     */
    public function can_reset_password(): bool
    {
        return false;        
    }

    /**
     * 
     */
    public function is_internal(): bool
    {
        return true;        
    }


    private function calculate_wantsurl()
    {
        if (isset($_COOKIE['oomaxHome'])) 
        {
            global $CFG;
            
            $options = 0;
            $ciphering = "AES-256-CBC";
            $decryption_iv = substr(bin2hex($CFG->wwwroot), -16);
            $decryption_key = parse_url($CFG->wwwroot)['host'];
            $decryption = openssl_decrypt ($_COOKIE['oomaxHome'], $ciphering,  $decryption_key, $options, $decryption_iv);
            redirect("https://{$decryption}");
        }
    }

    /**
     * 
     */
    public function loginpage_hook()
    {
        global $CFG, $USER;

        if (CLI_SCRIPT || AJAX_SCRIPT) {
            return;
        }
        
        $this->calculate_wantsurl();

        $token = optional_param('token', '', PARAM_RAW);
        $logout = optional_param('logout', '', PARAM_RAW);

        if ($CFG->forcelogin == True) {
            // force login!
        } elseif ($USER->id == 0) {
            // not logged in
        } elseif ($CFG->autologinguests == False || $CFG->guestloginbutton == False) {
            // no guest
        }
    }

    private function is_ready_for_login_page(\core\oauth2\issuer $issuer) {
        return $issuer->get('enabled') && $issuer->is_configured() && empty($issuer->get('showonloginpage'));
    }

    public function loginpage_idp_list($wantsurl, Bool $details = false)
    {
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

    public function pre_user_login_hook(&$user)
    {
        // magic
        echo "<pre>";
        echo var_dump($user);
        echo "</pre>";
        die();
    }

    public function user_exists($username)
    {
        echo "User Exists: ". $username .'<br>';
        die();
    }
}
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

namespace auth_cognito\local;

use auth_cognito\local\token;

/**
 * Class User
 * @package auth_oomax\model
 */
class user {
    /**
     * @var token
     */
    protected token $token;

    /**
     * @var stdClass
     */
    public \stdClass $user;

    /**
     * @var string
     */
    public string $locale;


    /**
     * __construct
     *
     * @param  \auth_cognito\model\token $token
     * @return void
     */
    public function __construct(token $token) {
        $this->token = $token;
        $this->user = $this->token->get_payload();
        $this->locale = 'en';
    }


    /**
     * Generates the User for Oomax
     * @return int
     * @throws \moodle_exception
     */
    public function create_user(): int {
        global $CFG;

        $firstname = '';
        $lastname = '';
        if (isset($this->user->name) && $this->user->name) {
            $firstname = $this->user->name;
        }
        if (isset($this->user->family_name) && $this->user->family_name) {
            $lastname = $this->user->family_name;
        }
        $user = new \stdClass();
        $user->auth = $this->token->auth;
        $user->username = preg_replace('/\+/', '_', $this->user->email);
        $user->firstname = trim($firstname);
        $user->lastname = trim($lastname);
        $user->email = $this->user->email;
        if (isset($this->user->locale)) {
            $user->lang = $this->user->locale;
        }

        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->confirmed = 1;
        $user->suspended = 0;
        $user->lastlogin = 0;

        return user_create_user($user, false, true);
    }

    /**
     * Process Locale Handling at the Token Level
     * @return void
     */
    public function process_user_locale(): void {
        if ($this->token->get_lang()) {
            // Convert language code from oomax format (e.g. fr-CA) to Moodle format (e.g. fr_ca).
            $lang = strtolower(str_replace('-', '_', $this->token->get_lang()));
            $sm = get_string_manager();
            // Find appropriate installed language, or use default system language.
            if (!$sm->translation_exists($lang)) {
                // Try base language.
                $lang = explode('_', $lang)[0];
                if (!$sm->translation_exists($lang)) {
                    // Use default language.
                    $lang = \core_user::get_property_default('lang');
                }
            }
            $this->locale = $lang;
        }
    }

    /**
     * Log User in; if user doesn't exist create user first
     * @return stdClass
     */
    public function user_login(): \stdClass {
        global $DB;

        // Get user by email.
        $userexists = $DB->get_record_select('user', 'LOWER(email) = ?', [strtolower($this->user->email)]);

        if ($userexists) {
            $this->user = $userexists;
            // If user exist perform login and redirect.

            $this->user->lang = $this->locale;
            if (isset($this->user->locale) && $this->user->locale != $this->user->lang) {
                $this->user->lang = $this->user->locale;
                $this->user->auth = $this->token->auth;
                user_update_user($this->user, false, false);
            }
        } else {
            // If user doesn't exist create user and perform login and redirect.
            $userid = $this->create_user($this->user);
            $this->user = $DB->get_record("user", ["id" => $userid]);
        }

        return complete_user_login($this->user);
    }

    /**
     * Generates Oomax Cookie
     * @return void
     */
    public function generate_oomax_cookie(): void {
        global $CFG;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $oomaxhome = parse_url($_SERVER['HTTP_REFERER']);
            $oomaxgroups = $this->token->get_groups();
            $oomaxgroupindex = $oomaxgroups[array_search($oomaxhome['host'], $oomaxgroups)];
            $homepath = parse_url($CFG->wwwroot);

            $homepath['path'] = !isset($homepath['path']) ? '/' : $homepath['path'];

            $options = 0;
            $ciphering = "AES-256-CBC";

            $encryptioniv = substr(bin2hex($CFG->wwwroot), -16);
            $encryptionkey = $homepath['host'];
            $encryption = openssl_encrypt($oomaxgroupindex, $ciphering, $encryptionkey, $options, $encryptioniv);
            $expiry = time() + 60 * 60 * 24 * 30;
            setcookie('oomaxhome', $encryption, $expiry, $homepath['path'], $homepath['host'], true, true);

        }
    }

    /**
     * Return User ID
     * @return int
     */
    public function user_id(): int {
        return $this->user->id;
    }
}

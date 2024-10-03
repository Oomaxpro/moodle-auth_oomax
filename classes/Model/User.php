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

namespace oomax\model;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\SignatureInvalidException;

/**
 * Class User
 * @package auth_oomax\model
 */
class User {
    /**
     * @var token
     */
    protected \Oomax\Model\Token $token;

    /**
     * @var stdClass
     */
    public \stdClass $user;

    /**
     * __construct
     *
     * @param  \Oomax\Model\Token $token
     * @return void
     */
    public function __construct(\Oomax\Model\Token $token) {
        $this->token = $token;
        $this->user = $this->token->getpayload();
    }


    /**
     * Generates the User for Oomax
     * @return int
     * @throws \moodle_exception
     */
    public function createuser(): int {
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
    public function processuserlocale(): void {
        if (isset($this->puserayload->locale)) {
            // Convert language code from oomax format (e.g. fr-CA) to Moodle format (e.g. fr_ca).
            $lang = strtolower(str_replace('-', '_', $this->user->locale));
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
            $this->user->locale = $lang;
        }
    }

    /**
     * Log User in; if user doesn't exist create user first
     * @return stdClass
     */
    public function userlogin(): \stdClass {
        global $DB;

        // Get user by email.
        $this->user = $DB->get_record_select('user', 'LOWER(email) = ?', [strtolower($this->user->email)]);

        if ($this->user) {
            // If user exist perform login and redirect.
            if (isset($this->user->locale) && $this->user->locale != $this->user->lang) {
                $this->user->lang = $this->user->locale;
                $this->user->auth = $this->token->auth;
                user_update_user($this->user, false, false);
            }
        } else {
            // If user doesn't exist create user and perform login and redirect.
            $userid = $this->createuser($this->user);
            $this->user = $DB->get_record("user", ["id" => $userid]);
        }

        return complete_user_login($this->user);
    }

    /**
     * Generates Oomax Cookie
     * @return void
     */
    public function generateoomaxcookie(): void {
        global $CFG;

        if (isset($_SERVER['HTTP_REFERER'])) {
            $oomaxhome = parse_url($_SERVER['HTTP_REFERER']);
            $oomaxgroups = $this->token->getgroups();
            $oomaxgroupindex = $oomaxgroups[array_search($oomaxhome['host'], $oomaxgroups)];
            $homepath['path'] = !isset($homepath['path']) ? '/' : $homepath['path'];

            $options = 0;
            $ciphering = "AES-256-CBC";

            $encryptioniv = substr(bin2hex($CFG->wwwroot), -16);
            $encryptionkey = $homepath['host'];
            $encryption = openssl_encrypt($oomaxgroupindex, $ciphering, $encryptionkey, $options, $encryptioniv);

            setcookie('oomaxhome', $encryption, time() + 60 * 60 * 24 * 30, $homepath['path'] ?? '/', $homepath['host'], true, true);
        }
    }

    /**
     * Return User ID
     * @return int
     */
    public function userid(): int {
        return $this->user->id;
    }
}

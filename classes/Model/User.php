<?php
/**
 * Created by PhpStorm.
 * User: bojan
 * Date: 2022-10-13
 * Time: 09:39
 */
namespace Oomax\Model;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\SignatureInvalidException;

/**
 * Class User
 * @package auth_oomax\model
 */
class User
{
    protected \Oomax\Model\Token $token;
    public \stdClass $user;

    public function __construct(\Oomax\Model\Token $token) 
    {
        $this->token = $token;
        $this->user = $this->token->getPayload();
    }


    /**
     * @param $this->user->
     * @return int
     * @throws \moodle_exception
     */
    public function createUser(): int
    {
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
        if(isset($this->user->locale)){
            $user->lang = $this->user->locale;
        }

        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->confirmed = 1;
        $user->suspended = 0;
        $user->lastlogin = 0;

        $userId = user_create_user($user, false, true);

        return $userId;
    }

    /**
     * Process Locale Handling at the Token Level
     */
    public function processUserLocale(): void
    {
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
     */
    public function UserLogin(): \stdClass
    {
        global $DB;

        // Get user by email
        $user = $DB->get_record_select('user', 'LOWER(email) = ?', [strtolower($this->user->email)]);

        if ($user) {
            // If user exist perform login and redirect
            if (isset($this->user->locale) && $this->user->locale != $user->lang) {
                $user->lang = $this->user->locale;
                $user->auth = $this->token->auth;
                user_update_user($user, false, false);
            }
        } else {
            // If user doesn't exist create user and perform login and redirect.
            $userId = $user->createUser($this->user);
            $user = $DB->get_record("user", ["id" => $userId]);
        }

        return complete_user_login($user);
    }

    public function userId(): int
    {
        return $this->user->id;
    }
}
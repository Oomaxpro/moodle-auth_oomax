<?php
/**
 * Created by PhpStorm.
 * User: bojan
 * Date: 2022-10-13
 * Time: 09:39
 */
namespace auth_cognito\model;

/**
 * Class User
 * @package auth_cognito\model
 */
class User
{

    /**
     * @param $payload
     * @return int
     * @throws \moodle_exception
     */
    public function createUser($payload){
        global $CFG;

        $firstnamefield = get_config('auth_cognito', 'firstname_field');
        $lastnamefield = get_config('auth_cognito', 'lastname_field');

        $firstname = '';
        $lastname = '';
        if (!empty($firstnamefield) && !empty($payload[$firstnamefield])) {
            $firstname = $payload[$firstnamefield];
        }
        if (!empty($lastnamefield) && !empty($payload[$lastnamefield])) {
            $lastname = $payload[$lastnamefield];
        }

        $user = new \stdClass();
        $user->auth = 'cognito';
        $user->username = $payload['email'];
        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user->email = $payload['email'];
        if(isset($payload['locale'])){
            $user->lang = $payload['locale'];
        }

        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->confirmed = 1;
        $user->suspended = 0;
        $user->lastlogin = 0;
        $userId = user_create_user($user, false, true);

        return $userId;
    }

    /**
     * @param string $token
     * @return object|string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function getDataFromToken($token = ''){
        $data = '';
        $publicKey = $this->get_public_key();

        // Decode token
        foreach ($publicKey['keys'] as $key) {
            try {

                $pk = \Firebase\JWT\JWK::parseKey($key);

                $data = \Firebase\JWT\JWT::decode($token, $pk);
            }
            catch (\Exception $e) {
//                print_r($e);
            }
        }

        return $data;
    }

    /**
     * Get public key file
     * @return mixed
     * @throws \coding_exception
     * @throws \dml_exception
     */
    function get_public_key() {

        $context = \context_system::instance();
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'auth_cognito', 'public_key');

        $file = end($files);

        return json_decode($file->get_content(), true);

    }
}
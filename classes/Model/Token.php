<?php
/**
 * Created by PhpStorm.
 * User: bojan
 * Date: 2022-10-13
 * Time: 09:39
 */
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
 *
 */

namespace Oomax\Model;

use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Firebase\JWT\SignatureInvalidException;

/**
 * Class Token
 * @package Oomax\Model
 */
class Token {
    /**
     * @var int
     */
    private Int $retry;

    /**
     * @var string
     */
    public String $auth = '';

    /**
     * @var string
     */
    private String $plugin = '';

    /**
     * @var array
     */
    private $keys;

    /**
     * @var cache
     */
    private \cache $cache;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $keyuri;

    /**
     * @var stdClass
     */
    private $payload;

    /**
     * Oomax Token Constructor
     * @param string $token
     */
    public function __construct(String $token = null) {
        $this->retry = 1;
        $this->auth = 'cognito';
        $this->plugin = "auth_{$this->auth}";
        $this->cache = \cache::make($this->plugin, 'oomax_cache');
        $this->token = $token;
        $this->keyuri = "https://cognito-idp.ca-central-1.amazonaws.com/ca-central-1_SiaYTCMC1/.well-known/jwks.json";
        $this->cachekeys();
    }

    /**
     * Returns the plugin information
     * @return string
     */
    public function getplugin(): string {
        return $this->plugin;
    }

    /**
     * Deciphers the groups payload
     */
    public function getgroups() {
        $groups = 'cognito:groups';
        if (!is_null($this->token)) {
            return $this->payload->$groups;
        }
        return null;
    }

    /**
     * Returns the Data from the Token
     * @return bool
     */
    public function getdatafromtoken(): bool {
        while ($this->retry > 0) {
            $result = $this->decipherToken();
            if ($result) {
                return $result;
            }
            $this->cache->delete('keys');
            $this->retry--;
        }
        return false;
    }

    /**
     * Deciphers the Token for Data
     * @return bool
     * @throws \SignatureInvalidException
     * @throws \Exception
     */
    private function deciphertoken(): bool {
        $data = '';
        if (is_null($this->keys)) {
            return false;
        }
        foreach ($this->keys['keys'] as $key) {
            try {
                $pk = JWK::parseKey($key);
                $this->payload = JWT::decode($this->token, $pk);
                return true;
            } catch (SignatureInvalidException $e) {
                continue;
            } catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Checks if JWT has been decoded
     * @return bool
     */
    public function isauthorized(): bool {
        return !is_null($this->payload);
    }

    /**
     * Get the JWT payload
     * @return \stdClass
     * @return bool
     */
    public function getpayload() {
        return $this->payload;
    }

    /**
     * Get public key file
     * @return void
     */
    private function getpublickey(): void {
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL => $this->keyuri,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        $this->keys = json_decode($response, true);
    }

    /**
     * Cache the Keys locally
     * @return void
     */
    private function cachekeys(): void {
        $this->keys = json_decode($this->cache->get('keys'), true);
        if (is_null($this->keys)) {
            $this->getpublickey();
            $this->cache->set('keys', json_encode($this->keys));
        }
    }
}

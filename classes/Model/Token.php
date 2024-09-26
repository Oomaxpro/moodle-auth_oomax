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
 * Class Token
 * @package Oomax\Model
 */
class Token
{
    private Int $retry;
    public String $auth = '';
    private String $plugin = '';
    private Array | Null $keys;
    private \cache $cache;
    private String | Null $token;
    private String | Null $key_uri;
    private \stdClass | Null $payload;

    /**
     * @param string $token
     */
    public function __construct(String $token = Null)
    {
        $this->retry = 1;
        $this->auth = 'cognito';
        $this->plugin = "auth_{$this->auth}";
        $this->cache = \cache::make($this->plugin, 'oomax_cache');
        $this->token = $token;
        $this->key_uri = "https://cognito-idp.ca-central-1.amazonaws.com/ca-central-1_SiaYTCMC1/.well-known/jwks.json";
        $this->cache_keys();
    }

    /**
     * @return string
     */
    public function getPlugin(): string
    {
        return $this->plugin;
    }

    /**
     * @return bool
     */
    public function getDataFromToken(): bool {
        while ($this->retry > 0)
        {
            $result = $this->decipherToken();
            if ($result) return $result;
            $this->cache->delete('keys');
            $this->retry--;
        }
        return false;
    }

    /**
     * @return bool
     * @throws \SignatureInvalidException
     * @throws \Exception
     */
    private function decipherToken(): bool {
        $data = '';
        if (is_null($this->keys)) return false;
        foreach ($this->keys['keys'] as $key) {
            try {
                $pk = JWK::parseKey($key);
                $this->payload = JWT::decode($this->token, $pk);
                return true;
            }
            catch (SignatureInvalidException $e) {
                continue;
            } 
            catch (\Exception $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Checks if JWT has been decoded
     * @return bool
     */
    public function isAuthorized(): bool 
    {
        return !is_null($this->payload);
    }

    /**
     * Get the JWT payload
     * @return \stdClass
     * @return bool
     */
    public function getPayload(): \stdClass | Null
    {
        return $this->payload;
    }

    /**
     * Get public key file
     * @return void
     */
    private function get_public_key(): void {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $this->key_uri,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
        ));
        
        $response = curl_exec($curl);
       
        curl_close($curl);
        $this->keys = json_decode($response, true);
    }

    /**
     * Cache the Keys locally
     * @return void
     */
    private function cache_keys(): void
    {
        $this->keys = json_decode($this->cache->get('keys'), true);
        if (is_null($this->keys)) {
            $this->get_public_key();
            $this->cache->set('keys', json_encode($this->keys));
        } 
    }
}
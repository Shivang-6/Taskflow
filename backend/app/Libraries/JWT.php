<?php
class JWT {
    private $secret_key = "taskflow_secret_key_2024_change_this";
    private $algorithm = "HS256";
    
    public function encode($payload) {
        $header = json_encode(["typ" => "JWT", "alg" => $this->algorithm]);
        $payload = json_encode(array_merge($payload, ["iat" => time(), "exp" => time() + 86400]));
        
        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);
        
        $signature = hash_hmac("sha256", $base64UrlHeader . "." . $base64UrlPayload, $this->secret_key, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);
        
        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }
    
    public function decode($jwt) {
        $parts = explode(".", $jwt);
        if (count($parts) != 3) return false;
        
        list($header, $payload, $signature) = $parts;
        
        $valid_signature = hash_hmac("sha256", $header . "." . $payload, $this->secret_key, true);
        $valid_signature = $this->base64UrlEncode($valid_signature);
        
        if (!hash_equals($signature, $valid_signature)) return false;
        
        $decoded_payload = json_decode($this->base64UrlDecode($payload), true);
        
        // Check expiration
        if (isset($decoded_payload["exp"]) && $decoded_payload["exp"] < time()) {
            return false;
        }
        
        return $decoded_payload;
    }
    
    private function base64UrlEncode($data) {
        return str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($data));
    }
    
    private function base64UrlDecode($data) {
        return base64_decode(str_replace(["-", "_"], ["+", "/"], $data));
    }
}
?>

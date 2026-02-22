<?php
/**
 * JWT - Classe para codificar/decodificar tokens JWT
 * Algoritmo: HS256
 */

class JWT {
    
    /**
     * Codificar JWT Token
     */
    public static function encode($payload) {
        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT'
        ];
        
        $headerEncoded = self::base64UrlEncode(json_encode($header));
        $payloadEncoded = self::base64UrlEncode(json_encode($payload));
        
        // Criar assinatura
        $signature = hash_hmac(
            'sha256',
            "$headerEncoded.$payloadEncoded",
            getenv('JWT_SECRET') ?: 'SAW_JWT_SECRET_2025',
            true
        );
        
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return "$headerEncoded.$payloadEncoded.$signatureEncoded";
    }
    
    /**
     * Decodificar JWT Token
     */
    public static function decode($token) {
        try {
            $parts = explode('.', $token);
            
            if (count($parts) !== 3) {
                return false;
            }
            
            list($headerEncoded, $payloadEncoded, $signatureEncoded) = $parts;
            
            // Verificar assinatura
            $signature = hash_hmac(
                'sha256',
                "$headerEncoded.$payloadEncoded",
                getenv('JWT_SECRET') ?: 'SAW_JWT_SECRET_2025',
                true
            );
            
            $expectedSignature = self::base64UrlDecode($signatureEncoded);
            
            if (!hash_equals($signature, $expectedSignature)) {
                return false;
            }
            
            // Decodificar payload
            $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
            
            // Verificar expiração
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return false;
            }
            
            return $payload;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Base64 URL Encode
     */
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    /**
     * Base64 URL Decode
     */
    private static function base64UrlDecode($data) {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
?>

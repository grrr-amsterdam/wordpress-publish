<?php namespace Grrr\WordpressPublish;

use WP_Error;

class JWT
{
    const TRANSIENT_NAME_TOKEN = "grrr-wordpress-publish-jwt-token";

    public function __construct(
        private string $privateKey,
        private string $applicationId
    ) {
    }

    public function createToken(): string
    {
        /** @var string|false $token */
        $token = get_transient(self::TRANSIENT_NAME_TOKEN);

        if ($token !== false) {
            return $token;
        }

        $expiration = 10 * 60;

        $header = [
            "alg" => "RS256",
            "typ" => "JWT",
        ];

        $payload = [
            "iat" => time() - 60,
            "exp" => time() + $expiration,
            "iss" => $this->applicationId,
        ];

        $header = json_encode($header);
        $payload = json_encode($payload);
        if ($header === false || $payload === false) {
            throw new \Exception("Failed to encode JWT header or payload.");
        }
        $header = $this->base64url_encode($header);
        $payload = $this->base64url_encode($payload);

        $privateKey = openssl_pkey_get_private($this->privateKey);
        if ($privateKey === false) {
            throw Exception::invalidPrivateKey();
        }

        $data = "$header.$payload";
        $success = openssl_sign(
            $data,
            $signature,
            $privateKey,
            "sha256WithRSAEncryption"
        );
        if ($success === false) {
            throw Exception::failedToSignData();
        }

        $signature = $this->base64url_encode($signature);

        $token = "$header.$payload.$signature";

        set_transient(self::TRANSIENT_NAME_TOKEN, $token, $expiration);

        return $token;
    }

    private function base64url_encode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }
}

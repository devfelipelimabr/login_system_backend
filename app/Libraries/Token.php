<?php

namespace App\Libraries;

/**
 * Token Class
 *
 * This class represents a unique token, typically used for authentication or password recovery purposes.
 * It generates a random token on instantiation and provides methods to retrieve the token and its hash.
 */
class Token
{

    /**
     * @var string $token The token value.
     */
    private $token;


    /**
     * Constructor
     *
     * Initializes a new Token object with a randomly generated token.
     *
     * @param string $token (Optional) The token value. If not provided, a random 16-byte token is generated.
     */
    public function __construct(string $token = null)
    {
        $this->token = $token ?? bin2hex(random_bytes(16));
    }

    /**
     * Get Token
     *
     * Returns the token value.
     *
     * @return string The token value.
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Get Hash
     *
     * Returns the SHA256 HMAC hash of the token using the 'PASSWORD_RECOVERY_KEY' environment variable as the secret key.
     *
     * @return string The hashed token value.
     */
    public function getHash(): string
    {
        return hash_hmac("sha256", $this->token, getenv('PASSWORD_RECOVERY_KEY'));
    }

    /**
     * Verifies if a given hash matches the computed hash for a token.
     *
     * This function takes a token and a hash as input and calculates the hash
     * for the provided token using the `ACCOUNT_ACTIVATION_SECRET_KEY`
     * environment variable. It then compares the computed hash with the provided hash.
     *
     * @param string $token The token to be verified.
     * @param string $hash The hash to compare with the computed hash.
     *
     * @return bool Returns `true` if the hashes match, `false` otherwise.
     */
    public static function verifyHash(string $token, string $hash): bool
    {
        $computedHash = hash_hmac('sha256', $token, env('ACCOUNT_ACTIVATION_SECRET_KEY'));
        return hash_equals($computedHash, $hash);
    }
}

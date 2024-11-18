<?php

namespace App\Entities;

use App\Libraries\Token;
use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Checks if the given password matches the stored password hash.
     *
     * This method uses `password_verify()` to compare the provided password with the stored password hash.
     *
     * @param string $password The password to be checked.
     *
     * @return bool True if the password matches the stored hash, false otherwise.
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password_hash);
    }

    /**
     * Starts the password reset process by generating a new token and setting associated data.
     *
     * This method creates a new Token object, retrieves the token and hash values from it,
     * and sets the `reset_token`, `reset_hash`, and `reset_expires_in` properties of the entity.
     * The token expires after 2 hours (7200 seconds).
     *
     * @return void
     */
    public function startPasswordReset(): void
    {
        $token = new Token();

        $this->reset_token = $token->getToken();
        $this->reset_hash = $token->getHash();
        $this->reset_expires_in = date('Y-m-d H:i:s', time() + 7200);
    }
}

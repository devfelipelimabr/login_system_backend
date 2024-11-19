<?php

namespace App\Controllers;

use App\Entities\User;
use App\Libraries\Token;
use App\Models\UserModel;
use App\Models\BlacklistedTokenModel;
use CodeIgniter\RESTful\ResourceController;
use DateTimeImmutable;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends ResourceController
{
    protected $userModel;
    protected $blacklistModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->blacklistModel = new BlacklistedTokenModel();
        helper('security');
    }

    // Registro de usuário
    public function register()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$email || !$password) {
            return $this->fail('Email e senha são obrigatórios', 400);
        }

        // Verificar se o usuário já existe
        if ($this->userModel->getUserByEmail($email)) {
            return $this->fail('O e-mail já está registrado', 409);
        }

        // Criar usuário
        $user = new User(
            [
                'email' => $email,
                'password' => $password
            ]
        );
        $this->userModel->save($user);

        return $this->respondCreated(['message' => 'Usuário registrado com sucesso']);
    }

    // Login de usuário
    public function login()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (!$email || !$password) {
            return $this->fail('Email e senha são obrigatórios', 400);
        }

        // Encontrar usuário
        $user = $this->userModel->getUserByEmail($email);
        if (!$user || !$user->checkPassword($password)) {
            return $this->fail('Credenciais inválidas', 401);
        }

        // Gerar JWT
        $key = getenv('JWT_SECRET'); // Certifique-se de definir JWT_SECRET no .env
        $payload = [
            'iss' => getenv('JWT_ISSUER'),
            'aud' => getenv('JWT_AUDIENCE'),
            'iat' => time(),
            'exp' => getenv('JWT_EXPIRATION') ?
                time() + getenv('JWT_EXPIRATION') :
                time() + 3600,
            'uid' => $user->id
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond([
            'token' => $token,
            'expires_in' => $payload['exp']
        ]);
    }

    // Verificação de token
    public function verifyToken()
    {
        // Obtenha o token do corpo da requisição
        $token = $this->request->getPost('token');

        if (!$token) {
            return $this->failUnauthorized('Token não fornecido');
        }

        // Verificar se o token está na blacklist
        if ($this->blacklistModel->where('token', $token)->first()) {
            return $this->failUnauthorized('Token inválido ou revogado.');
        }

        // Verificação normal do token JWT
        try {
            $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));
            return $this->respond(['message' => 'Token válido']);
        } catch (Exception $e) {
            return $this->failUnauthorized('Token inválido: ' . $e->getMessage());
        }
    }

    // Logout de usuário
    public function logout()
    {
        // Obtenha o token do corpo da requisição
        $token = $this->request->getPost('token');

        if (!$token) {
            return $this->failUnauthorized('Token não fornecido');
        }

        // Verificar se o token está na blacklist
        if ($this->blacklistModel->where('token', $token)->first()) {
            return $this->failUnauthorized('Token inválido ou já foi revogado.');
        }

        // Adicionar o token à blacklist
        $this->blacklistModel->insert(['token' => $token]);

        return $this->respond(['message' => 'Logout realizado com sucesso.']);
    }

    public function recovery()
    {
        if ($this->request->getMethod() === 'POST') {
            $post = $this->request->getPost();

            if (empty($post['email'])) {
                return $this->failUnauthorized('E-mail não fornecido');
            }

            $email = $post['email'];

            $user = $this->userModel->getUserByEmail($email);

            if (! $user) {
                return $this->fail('Email inválido', 401);
            }

            $user->startPasswordReset();

            if (! $this->userModel->save($user)) {
                return $this->fail('Falha do servidor', 500);
            }

            if (! $this->sendPasswordRecoveryEmail($user)) {
                return $this->fail('Falha durante envio de e-mail', 500);
            }

            return $this->respond(['success' => 'E-mail enviado com sucesso']);
        }
    }

    public function resetConfirm(string $reset_token = null)
    {
        if (!$reset_token) {
            return $this->failUnauthorized('Token não fornecido');
        }

        $tokenHandler = new Token($reset_token);
        $resetHash = $tokenHandler->getHash(); // Create a hash token based in reset input token

        $user = $this->userModel->getUserByResetHash($resetHash); // Searches the database for a user who has the hash token equal to the one generated in resetHash

        if (!$user || !$this->isValidResetExpires($user->reset_expires_in)) {
            return $this->failUnauthorized('Requisição expirada ou inválida');
        }

        return $this->respond([
            'success' => 'Token válido',
            'reset_token' => $reset_token
        ]);
    }

    public function reset()
    {

        if ($this->request->getMethod() === 'POST') {

            $post = $this->request->getPost();

            if (empty($post['reset_token'])) {
                return $this->failUnauthorized('Token não fornecido');
            }
            if (empty($post['password'])) {
                return $this->failUnauthorized('Senha não fornecida');
            }

            $tokenHandler = new Token($post['reset_token']);
            $resetHash = $tokenHandler->getHash(); // Create a hash token based in reset input token

            $user = $this->userModel->getUserByResetHash($resetHash); // Searches the database for a user who has the hash token equal to the one generated in resetHash

            if (!$user || !$this->isValidResetExpires($user->reset_expires_in)) {
                return $this->failUnauthorized('Requisição expirada ou inválida');
            }

            // Fill in the user entity with password and password_confirm only, and remove reset_hash and reset_expires_in
            $userData = [
                'password' => $post['password'],
                'reset_hash' => null,
                'reset_expires_in' => null,
            ];

            // Populates the entity with the new userData
            $user->fill($userData);

            if ($this->userModel->save($user)) {
                return $this->respond(['message' => 'Nova senha salva']);
            }

            return $this->respond(['errors' => $this->userModel->errors()], 500);
        }
    }

    /**
     * Sends a password recovery email to the specified user.
     *
     * @param object $user The User entity object containing the email and reset token.
     *
     * @return boolean
     */
    private function sendPasswordRecoveryEmail(User $user): bool
    {
        // Render the password recovery email template with user data
        $recoveryMsg = view('pw_recovery_mail', [
            'reset_token' => $user->reset_token,
            'name' => $user->name,
        ]);

        // Get the email service instance
        $email = service('email');

        // Set the email sender and recipient
        $email->setFrom(env('email.SMTPUser'), env('company.name'));
        $email->setTo($user->email);

        // Set the email subject and message
        $email->setSubject('Redefinição de senha'); // Consider using a more descriptive subject
        $email->setMessage($recoveryMsg);

        // Send the email
        if ($email->send()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks if the password reset token is still valid based on the expiration time.
     *
     * @param string $resetExpiresIn The string representation of the reset token expiration time in "Y-m-d H:i:s" format.
     * @return bool True if the token is still valid, false otherwise.
     */
    private function isValidResetExpires(string $resetExpiresIn): bool
    {
        // If the reset expiration time is not provided, the token is invalid.
        if (!$resetExpiresIn) {
            return false;
        }

        // Get the current date and time as a DateTimeImmutable object.
        $now = new DateTimeImmutable();

        // Convert the string reset expiration time into a DateTimeImmutable object.
        $resetExpiresDateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $resetExpiresIn);

        // Compare the expiration time with the current time.
        // The token is valid if the expiration time is greater than the current time.
        return $resetExpiresDateTime > $now;
    }
}

<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BlacklistedTokenModel;
use CodeIgniter\RESTful\ResourceController;
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
        if ($this->userModel->where('email', $email)->first()) {
            return $this->fail('O e-mail já está registrado', 409);
        }

        // Hash da senha
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Criar usuário
        $this->userModel->insert([
            'email' => $email,
            'password_hash' => $passwordHash
        ]);

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
        $user = $this->userModel->where('email', $email)->first();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return $this->fail('Credenciais inválidas', 401);
        }

        // Gerar JWT
        $key = getenv('JWT_SECRET'); // Certifique-se de definir JWT_SECRET no .env
        $payload = [
            'iss' => 'sua_api',
            'aud' => 'seu_frontend',
            'iat' => time(),
            'exp' => time() + 3600, // Expiração de 1 hora
            'uid' => $user['id']
        ];
        $token = JWT::encode($payload, $key, 'HS256');

        return $this->respond(['token' => $token]);
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
        } catch (\Exception $e) {
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
}

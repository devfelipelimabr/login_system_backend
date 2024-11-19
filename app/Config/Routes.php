<?php

use Config\Services;

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('api/v2', function ($routes) {
    // Registro de usuário
    $routes->post('register', 'AuthController::register');

    // Login de usuário
    $routes->post('login', 'AuthController::login');

    // Logout de usuário
    $routes->post('logout', 'AuthController::logout');

    // Verificação de token
    $routes->post('verify', 'AuthController::verifyToken');

    // Recuperação de senha
    $routes->post('recovery', 'AuthController::recovery');

    // Verificação de token dinâmico
    $routes->get('reset-confirm/(:segment)', 'AuthController::resetConfirm/$1');

    // Redefinição de senha
    $routes->post('reset', 'AuthController::reset');
});

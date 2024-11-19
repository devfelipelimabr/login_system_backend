<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var \CodeIgniter\Router\RouteCollection $routes
 */

$routes->group('api/v2', ['filter' => 'cors'], function (RouteCollection $routes): void {
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

    // Adicionando suporte para OPTIONS (Preflight Requests)
    $routes->options('register', static function () {
        return response()->setStatusCode(204); // No Content
    });
    $routes->options('login', static function () {
        return response()->setStatusCode(204);
    });
    $routes->options('logout', static function () {
        return response()->setStatusCode(204);
    });
    $routes->options('verify', static function () {
        return response()->setStatusCode(204);
    });
    $routes->options('recovery', static function () {
        return response()->setStatusCode(204);
    });
    $routes->options('reset-confirm/(:segment)', static function () {
        return response()->setStatusCode(204);
    });
    $routes->options('reset', static function () {
        return response()->setStatusCode(204);
    });
});

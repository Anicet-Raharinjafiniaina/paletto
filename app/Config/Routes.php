<?php

use CodeIgniter\Config\Services;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

$routes->get('/', 'Login::index');
$routes->setAutoRoute(true);

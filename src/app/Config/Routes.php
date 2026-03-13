<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Comments::index');
$routes->get('comments/get', 'Comments::getComments');
$routes->post('comments/create', 'Comments::create');
$routes->delete('comments/delete/(:num)', 'Comments::delete/$1');

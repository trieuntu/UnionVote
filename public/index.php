<?php
/**
 * UnionVote - Entry Point
 * All requests are routed through this file.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Config\App;
use App\Core\Router;
use App\Core\Session;

// Initialize application config
App::init();

// Start session
Session::start();

// Create router
$router = new Router();

// ============================================================
// FRONTEND ROUTES
// ============================================================

$router->get('/', 'Frontend\HomeController@index');
$router->get('/election/{id}', 'Frontend\ElectionController@show');

// Vote flow
$router->post('/vote/request-token', 'Frontend\VoteController@requestToken', ['CsrfMiddleware']);
$router->get('/vote/{election_id}/verify', 'Frontend\VoteController@verifyForm');
$router->post('/vote/verify-token', 'Frontend\VoteController@verifyToken', ['CsrfMiddleware']);
$router->get('/vote/{election_id}/ballot', 'Frontend\VoteController@ballot');
$router->post('/vote/submit', 'Frontend\VoteController@submit', ['CsrfMiddleware']);
$router->get('/vote/{election_id}/success', 'Frontend\VoteController@success');

// Results
$router->get('/results/{election_id}', 'Frontend\ResultController@live');
$router->get('/api/results/{election_id}', 'Frontend\ResultController@apiResults');

// ============================================================
// ADMIN ROUTES
// ============================================================

// Auth (public admin routes)
$router->get('/admin/login', 'Admin\AuthController@loginForm');
$router->post('/admin/login', 'Admin\AuthController@login', ['CsrfMiddleware']);
$router->post('/admin/logout', 'Admin\AuthController@logout', ['CsrfMiddleware', 'AuthMiddleware']);

// Dashboard
$router->get('/admin', 'Admin\DashboardController@index', ['AuthMiddleware']);

// Elections CRUD
$router->get('/admin/elections', 'Admin\ElectionController@index', ['AuthMiddleware']);
$router->get('/admin/elections/create', 'Admin\ElectionController@create', ['AuthMiddleware']);
$router->post('/admin/elections', 'Admin\ElectionController@store', ['CsrfMiddleware', 'AuthMiddleware']);
$router->get('/admin/elections/{id}', 'Admin\ElectionController@show', ['AuthMiddleware']);
$router->get('/admin/elections/{id}/edit', 'Admin\ElectionController@edit', ['AuthMiddleware']);
$router->put('/admin/elections/{id}', 'Admin\ElectionController@update', ['CsrfMiddleware', 'AuthMiddleware']);
$router->delete('/admin/elections/{id}', 'Admin\ElectionController@destroy', ['CsrfMiddleware', 'AuthMiddleware']);
$router->patch('/admin/elections/{id}/toggle-visibility', 'Admin\ElectionController@toggleVisibility', ['CsrfMiddleware', 'AuthMiddleware']);
$router->patch('/admin/elections/{id}/toggle-result', 'Admin\ElectionController@toggleResult', ['CsrfMiddleware', 'AuthMiddleware']);

// Candidates
$router->get('/admin/elections/{id}/candidates', 'Admin\CandidateController@index', ['AuthMiddleware']);
$router->get('/admin/elections/{id}/candidates/create', 'Admin\CandidateController@create', ['AuthMiddleware']);
$router->post('/admin/elections/{id}/candidates', 'Admin\CandidateController@store', ['CsrfMiddleware', 'AuthMiddleware']);
$router->get('/admin/candidates/{id}/edit', 'Admin\CandidateController@edit', ['AuthMiddleware']);
$router->put('/admin/candidates/{id}', 'Admin\CandidateController@update', ['CsrfMiddleware', 'AuthMiddleware']);
$router->post('/admin/elections/{id}/candidates/import', 'Admin\CandidateController@import', ['CsrfMiddleware', 'AuthMiddleware']);
$router->delete('/admin/candidates/{id}', 'Admin\CandidateController@destroy', ['CsrfMiddleware', 'AuthMiddleware']);
$router->delete('/admin/elections/{id}/candidates', 'Admin\CandidateController@destroyAll', ['CsrfMiddleware', 'AuthMiddleware']);
$router->get('/admin/templates/candidates', 'Admin\CandidateController@downloadTemplate', ['AuthMiddleware']);

// Voters
$router->get('/admin/elections/{id}/voters', 'Admin\VoterController@index', ['AuthMiddleware']);
$router->get('/admin/elections/{id}/voters/create', 'Admin\VoterController@create', ['AuthMiddleware']);
$router->post('/admin/elections/{id}/voters', 'Admin\VoterController@store', ['CsrfMiddleware', 'AuthMiddleware']);
$router->get('/admin/voters/{id}/edit', 'Admin\VoterController@edit', ['AuthMiddleware']);
$router->put('/admin/voters/{id}', 'Admin\VoterController@update', ['CsrfMiddleware', 'AuthMiddleware']);
$router->post('/admin/elections/{id}/voters/import', 'Admin\VoterController@import', ['CsrfMiddleware', 'AuthMiddleware']);
$router->delete('/admin/voters/{id}', 'Admin\VoterController@destroy', ['CsrfMiddleware', 'AuthMiddleware']);
$router->delete('/admin/elections/{id}/voters', 'Admin\VoterController@destroyAll', ['CsrfMiddleware', 'AuthMiddleware']);
$router->get('/admin/templates/voters', 'Admin\VoterController@downloadTemplate', ['AuthMiddleware']);

// Results
$router->get('/admin/elections/{id}/results', 'Admin\ResultController@index', ['AuthMiddleware']);
$router->get('/admin/elections/{id}/results/export', 'Admin\ResultController@export', ['AuthMiddleware']);

// Users (Admin only)
$router->get('/admin/users', 'Admin\UserController@index', ['AuthMiddleware', 'RoleMiddleware']);
$router->get('/admin/users/create', 'Admin\UserController@create', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('/admin/users', 'Admin\UserController@store', ['CsrfMiddleware', 'AuthMiddleware', 'RoleMiddleware']);
$router->get('/admin/users/{id}/edit', 'Admin\UserController@edit', ['AuthMiddleware', 'RoleMiddleware']);
$router->put('/admin/users/{id}', 'Admin\UserController@update', ['CsrfMiddleware', 'AuthMiddleware', 'RoleMiddleware']);
$router->delete('/admin/users/{id}', 'Admin\UserController@destroy', ['CsrfMiddleware', 'AuthMiddleware', 'RoleMiddleware']);

// Settings (Admin only)
$router->get('/admin/settings/mail', 'Admin\SettingController@mail', ['AuthMiddleware', 'RoleMiddleware']);
$router->post('/admin/settings/mail', 'Admin\SettingController@updateMail', ['CsrfMiddleware', 'AuthMiddleware', 'RoleMiddleware']);
$router->post('/admin/settings/mail/test', 'Admin\SettingController@testMail', ['CsrfMiddleware', 'AuthMiddleware', 'RoleMiddleware']);

// ============================================================
// DISPATCH
// ============================================================

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$router->dispatch($uri, $method);

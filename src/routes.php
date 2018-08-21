<?php

use Slim\Http\Request;
use Slim\Http\Response;

include (__DIR__ . '/controller.php');
include (__DIR__ . '/ind.php');

// Routes

/**
 * @todo
 * 1. вынести базу в сервис-контейнер (как logger и renderer) done
 * 2. привести в порядок навигацию done
 * 3. обработка ошибок
 */

	$app->get('/', 'controller:Home');

	$app->get('/todolist', 'controller:displayTodo')->add('AuthMiddleware:Check');
	//$app->get('/todolist', 'controller:displayTodo');
	$app->post('/todolist','controller:AddNewTask');

	$app->get('/authorization', 'controller:Auth');
	$app->post('/authorization', 'controller:Login');

	$app->get('/logout', 'controller:LogOut');
  
	$app->get('/register', 'controller:Register');
	$app->post('/register', 'controller:Registration');

	$app->get('/todo/{id}/delete', 'controller:Delete');

	$app->get('/todo/{id}/edit', 'controller:EditForm')->add('AuthMiddleware:Check');
	$app->post('/todo/{id}/edit', 'controller:Edit');
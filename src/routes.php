<?php

use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;
use src\handlers\DatabaseInfoHandler;
use src\handlers\GroupHandler;
use src\handlers\UserHandler;



return function (App $app) {

    $app->get('/',[UserHandler::class, 'index']);
    $app->get('/users', [DatabaseInfoHandler::class, 'getUsers']);
    $app->get('/groups', [DatabaseInfoHandler::class, 'getGroups']);
    $app->get('/messages', [DatabaseInfoHandler::class, 'getMessages']);
    $app->get('/utg', [DatabaseInfoHandler::class, 'getUserGroups']);
    $app->post('/register', [UserHandler::class, 'register']);
    $app->get('/attendants/{group_id}', [GroupHandler::class, 'getGroupAttendants']);

    $app->group('/{token}', function (Group $group) {

        $group->get('', [UserHandler::class, 'getUser']);
        $group->get('/', [UserHandler::class, 'getUser']);
        $group->get('/polling', [UserHandler::class, 'polling']);

        $group->group('/group', function (Group $group) {

            $group->get('/attendedlist', [UserHandler::class, 'attendedGroups']);
            $group->post('/create', [GroupHandler::class, 'createGroup']);
            $group->post('/join/{group_id}', [GroupHandler::class, 'joinGroup']);
            $group->post('/sendmessage', [UserHandler::class, 'sendMessage']);
            $group->post('/{group_id}', [GroupHandler::class, 'getGroupMessages']);

        });
    });   

};

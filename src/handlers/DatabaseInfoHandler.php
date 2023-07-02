<?php

namespace src\handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use src\models\Model;



class DatabaseInfoHandler
{
    private $model;

    public function __construct() {
        $this->model = new Model();
    }

    public function getUsers(Request $request, Response $response, array $args) {
        $users = $this->model->getUsers();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getMessages(Request $request, Response $response, array $args) {
        $messages = $this->model->getAllMessages();
        $response->getBody()->write(json_encode($messages));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getGroups(Request $request, Response $response, array $args) {
        $groups = $this->model->getGroups();
        $response->getBody()->write(json_encode($groups));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUserGroups(Request $request, Response $response, array $args) {
        $utg = $this->model->getUsersGroups();
        $response->getBody()->write(json_encode($utg));
        return $response->withHeader('Content-Type', 'application/json');
    }
 
}

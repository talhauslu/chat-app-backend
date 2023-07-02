<?php

namespace src\handlers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use src\models\User;
use src\models\Message;
use src\models\Group;



class UserHandler
{
    private $userModel;
    private $messageModel;
    private $groupModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->messageModel = new Message();
        $this->groupModel = new Group();
    }

    public function index(Request $request, Response $response, array $args)
    {
        $data = [
            'status' => 200,
            'message' => 'welcome to the chat app!'
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getUser(Request $request, Response $response, array $args)
    {
        $token = $args['token'];
        $id = $this->userModel->getIdByToken($token);
        $user = $this->userModel->getUserById($id);
        $response->getBody()->write(json_encode($user));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function sendMessage(Request $request, Response $response, array $args)
    {
        $id = $this->userModel->getIdByToken($args['token']);
        $query_parameters = $request->getQueryParams();
        if ($this->userModel->checkIfAttendant($id, $query_parameters['to'])) {
            $this->messageModel->sendMessage($query_parameters['to'], $id, $query_parameters['content']);
        } else {
            $data = [
                'status' => 401,
                'message' => 'You are not a member of this group.'
            ];
            $response->getBody()->write(json_encode($data));
            return $response->withHeader('Content-Type', 'application/json');
        }
        $data = [
            'status' => 200,
            'message' => 'Message sent successfully.'
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function polling(Request $request, Response $response, array $args)
    {
        $user_id = $this->userModel->getIdByToken($args['token']);
        $group_ids = $this->userModel->getAttendedGroups($user_id);
        $unseen_messages = [];
        foreach ($group_ids as $group_id) {
            $messages = $this->messageModel->getUnseenMessages($user_id, $group_id['group_id']);
            foreach ($messages as $message) {
                array_push($unseen_messages, $message);
            }
        }
        $data = [
            'total' => count($unseen_messages),
            'messages' => $unseen_messages
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function attendedGroups(Request $request, Response $response, array $args) {
        $user_id = $this->userModel->getIdByToken($args['token']);
        $group_ids = $this->userModel->getAttendedGroups($user_id);
        $data = [];
        foreach ($group_ids as $group_id) {
            $groups = $this->groupModel->getGroupById($group_id['group_id']);
            foreach ($groups as $group) {
                array_push($data, $group);
            }
        }
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function register(Request $request, Response $response, array $args)
    {
        $query_parameters = $request->getQueryParams();
        $this->userModel->createUser($query_parameters['username'], $query_parameters['token']);
        $data = [
            'status' => 200,
            'message' => 'Registered successfully.'
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

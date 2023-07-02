<?php

namespace src\handlers;

use Error;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use src\models\Group;
use src\models\Message;
use src\models\User;



class GroupHandler
{
    private $groupModel;
    private $userModel;
    private $messageModel;

    public function __construct() {
        $this->groupModel = new Group();
        $this->userModel = new User();
        $this->messageModel = new Message();
    }

    public function getGroupMessages(Request $request, Response $response, array $args) {
        $group_id = $args['group_id'];
        $user_id = $this->userModel->getIdByToken($args['token']);
        if ($this->userModel->checkIfAttendant($user_id, $group_id)) {
            $data = $this->groupModel->getMessages($group_id);
            foreach($data as $each) {
                if ($each['sender_id'] != $user_id) { //36th row
                    $seen_by = explode(',', $each['seen_by']);
                    if (!in_array($user_id, $seen_by)) {
                        $this->messageModel->markAsSeen($user_id, $each['id']);
                    }
                }
            }
        } else {
            $data = [
                'status' => 401,
                'message' => 'You are not a member of this group.'
            ];
        }
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function createGroup(Request $request, Response $response, array $args) {
        $query_parameters = $request->getQueryParams();
        try {
            $this->groupModel->newGroup($query_parameters['name'], ($query_parameters['description'] ?? ''));
        } catch (Error $e) {
            //
        }
        $group_id = $this->groupModel->getLastGroupId();
        $attendant_ids = explode(',', $query_parameters['attendants']);
        foreach ($attendant_ids as $attendant_id) {
            $this->groupModel->addUserToGroup($attendant_id,$group_id);
        }
        $user_id = $this->userModel->getIdByToken($args['token']);
        $this->groupModel->addUserToGroup($user_id, $group_id);
        $data = [
            'status' => 200,
            'message' => 'Group was created successfully.'
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function joinGroup(Request $request, Response $response, array $args) {
        $group_id = $args['group_id'];
        $user_id = $this->userModel->getIdByToken($args['token']);
        $this->groupModel->addUserToGroup($user_id, $group_id);
        $data = [
            'status' => 200,
            'message' => 'Joined the group successfully.'
        ];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getGroupAttendants(Request $request, Response $response, array $args){
        $id = $args['group_id'];
        $attendants = $this->groupModel->getAttendants($id);
        $attendants_list = [];
        foreach ($attendants as $attendant) {
            $user = $this->userModel->getUserById($attendant['user_id'])[0];
            array_push($attendants_list, $user);
        }
        $response->getBody()->write(json_encode($attendants_list));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

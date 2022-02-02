<?php
/**

 * Copyright (c) 2003-2019 BrightOutcome Inc.  All rights reserved.
 *
 * This software is the confidential and proprietary information of
 * BrightOutcome Inc. ("Confidential Information").  You shall not
 * disclose such Confidential Information and shall use it only
 * in accordance with the terms of the license agreement you
 * entered into with BrightOutcome.
 *
 * BRIGHTOUTCOME MAKES NO REPRESENTATIONS OR WARRANTIES ABOUT THE
 * SUITABILITY OF THE SOFTWARE, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT
 * NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE, OR NON-INFRINGEMENT. BRIGHTOUTCOME SHALL NOT BE LIABLE
 * FOR ANY DAMAGES SUFFERED BY LICENSEE AS A RESULT OF USING, MODIFYING OR
 * DISTRIBUTING THIS SOFTWARE OR ITS DERIVATIVES.
 */
defined('BASEPATH') or exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Questionnaire extends REST_Controller
{

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Token, Content-Type, X-XSRF-TOKEN");
        parent::__construct();
        $this->load->model('Questionnaire_model', 'questionnaire');
        $this->load->model('User_model', 'user');
        $this->load->model('Educational_model', 'education');
        $this->check_token();
    }

    /**
     * Function to get Dashboard Weekly Questionnaire for each Patients.
     */
    public function dash_weekly_questionnaire_get()
    {
        $chapter_arr = array();

        $is_week_completed = false;
        $arm = $this->input->get('arm');
        $user_id = $this->get_user();
        $user = $this->user->get_detail($user_id);
        if ($user['user_type'] != 3) {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('unauthorised_see_content')), REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }
        //$user_list = $this->questionnaire->get_dashboard_weekly_questionnaire(array('user_id' => $user_id));
        $where = array('content.content_id' => null, 'type' => "'CONTENT'");
        if ($user['arm_alloted']) {
            $where['content.arm'] = "'" . $user['arm_alloted'] . "'";
        }
        $chapters_list = $this->education->get_content(array('where' => $where, 'fields' => 'id,content_name,type,slug,icon_class', 'order_by' => 'content.position', 'order' => 'ASC'));
        $all_week_info = $this->education->get_all_week($user_id);
        $assets_config = $this->config->item('assets_images');
        
        if (!empty($chapters_list)) {
            foreach ($chapters_list as $key => $chapters) {
                $ch_data = array('id' => $chapters['id'], 'week_number' => '', 'content_name' => $chapters['content_name'], 'type' => $chapters['type'], 'slug' => $chapters['slug'], 'image' => base_url($assets_config['path'] . '/' . $chapters['icon_class']));
                if (isset($all_week_info[$key]['week_number']) && $all_week_info[$key]['week_number']) {
                    $ch_data['week_number'] = $all_week_info[$key]['week_number'];
                }
                $chapter_arr[] = $ch_data;
            }
        }
        $last_week_info = $this->questionnaire->get_last_week_info($user_id);

        if(isset($last_week_info) && !empty($last_week_info)){
            $today = date('Y-m-d H:i:s');
            if($today>$last_week_info->week_ends_at){
                $is_week_completed = true;
            }
        }
        $this->response(array('status' => 'success',  'chapters' => $chapter_arr, 'is_week_completed'=>$is_week_completed, 'arm' => $user['arm_alloted']), REST_Controller::HTTP_OK);
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Desc : Get all questions
     */
    public function questionnaire_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $user_id = $this->get_user();
        $request['user_id'] = $user_id;
        $response = $this->questionnaire->get_questionnaire($request);
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Method: POST
     * Header Key: Authorization
     * Desc : save question
     */
    public function save_answer_post()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $user_id = $this->get_user();
        $data['user_id'] = $user_id;
        $response = $this->questionnaire->save_questions_answer($data);
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Desc : Get current week information
     */
    public function week_info_get()
    {
        $user_id = $this->get_user();
        $result = $this->questionnaire->get_week_info($user_id);
        $data = array(
            'status' => 'success',
            'data' => $result,
        );
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method: post
     * Header Key: Authorization
     * Desc : Get question for resource
     */
    public function resourse_question_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $data = array('status' => 'error');
        if ($request['exercise_type']) {
            $resource_id = isset($request['resource_id']) && $request['resource_id'] ? $request['resource_id'] : null;
            $user_id = $this->get_user();
            $result = $this->questionnaire->get_resource_question($request['exercise_type'], $user_id, $resource_id);
            if (isset($result) && $result) {
                $data = array(
                    'status' => 'success',
                    'data' => $result,
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'msg' => "Week not started yet",
                );
            }

        }
        $this->response($data, REST_Controller::HTTP_OK);
    }
    /**
     * Method: post
     * Header Key: Authorization
     * Desc : Get question for resource
     */
    public function resourse_question_responses_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $data = array('status' => 'error');
        if ($request['exercise_type']) {
            $resource_id = isset($request['resource_id']) && $request['resource_id'] ? $request['resource_id'] : null;
            $user_id = $this->get_user();
            $result = $this->questionnaire->get_resource_question_response($request['exercise_type'], $user_id, $resource_id);
            if (isset($result) && $result) {
                $data = array(
                    'status' => 'success',
                    'data' => $result,
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'msg' => "Week not started yet",
                );
            }

        }
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method: post
     * Header Key: Authorization
     * Desc : Get question for resource
     */
    public function resourse_question_response_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $request['user_id'] = $this->get_user();
        $data = $this->questionnaire->save_resource_question_response($request);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Method: post
     * Header Key: Authorization
     * Desc : set chapter as visited
     */
    public function add_to_visited_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $request['user_id'] = $this->get_user();
        if ($request['user_id'] && $request['content_id']) {
            $response = $this->questionnaire->add_chapter_subtopic_visited($request);
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Method: post
     * Header Key: Authorization
     * Desc : set chapter as visited
     */
    public function update_visited_chapter_subtopic_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $request['user_id'] = $this->get_user();
        if ($request['user_id'] && $request['content_id']) {
            $response = $this->questionnaire->update_visited_chapter_subtopic($request);
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

    /**
     * Method: post
     * Header Key: Authorization
     * Desc : set chapter as visited
     */
    public function add_visited_resource_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $request['user_id'] = $this->get_user();
        if ($request['user_id'] && $request['content_id'] && $request['resource_id']) {
            $response = $this->questionnaire->add_resource_to_visited($request);
        }
        $this->response($response, REST_Controller::HTTP_OK);
    }

}

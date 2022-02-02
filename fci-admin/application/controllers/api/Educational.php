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
class Educational extends REST_Controller
{
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Token, Content-Type, X-XSRF-TOKEN");
        parent::__construct();
        $this->load->model('Educational_model', 'education');
        $this->load->model('Resource_model', 'resource');
        $this->load->model('Settings_model', 'settings');
        $this->load->model('User_model', 'user');
        $this->check_token();
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Desc : Get all Resources for a user
     */

    public function resources_get()
    {
        $user_id = $this->get_user();
        if (!$user_id) {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('not_authorized')), REST_Controller::HTTP_OK);
            exit;
        }

		$user = $this->user->get_detail($user_id);
        if ($user['user_type'] == 3) {
            $response = $this->resource->get_user_resources($user['arm_alloted'], $user_id);
            $this->response(array('status' => 'success', 'data' => $response), REST_Controller::HTTP_OK);
        }
        $this->response(array('status' => 'error', 'msg' => $this->lang->line('not_authorized')), REST_Controller::HTTP_OK);
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Desc : Get all week reflection data
     */

    public function reflection_get()
    {
        $user_id = $this->get_user();
        if (!$user_id) {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('not_authorized')), REST_Controller::HTTP_OK);
            exit;
        }

        $user = $this->user->get_detail($user_id);
        if ($user['user_type'] == 3) {
            $response = $this->resource->get_user_resources($user['arm_alloted'], $user_id);
            $this->response(array('status' => 'success', 'data' => $response), REST_Controller::HTTP_OK);
        }
        $this->response(array('status' => 'error', 'msg' => $this->lang->line('not_authorized')), REST_Controller::HTTP_OK);
    }

    /**
     * Method: GET
     * Header Key: Authorization
     * Desc : Get all Chapters list
     */

    public function chapters_get()
    {
        $status = 'error';
        $msg = $this->lang->line('chapter_not_available');
        $response = array();

        $chapters_list = $this->education->get_content(array('table' => 'content', 'where' => array('content.content_id' => null), 'fields' => 'id,content_name,type,slug,icon_class', 'order_by' => 'content.position', 'order' => 'ASC'));

        $assets_config = $this->config->item('assets_images');

        $chapter_arr = array();
        if (!empty($chapters_list)) {
            foreach ($chapters_list as $chapters) {
                $chapter_arr[] = array('id' => $chapters['id'], 'content_name' => $chapters['content_name'], 'type' => $chapters['type'], 'slug' => $chapters['slug'], 'image' => base_url($assets_config['path'] . '/' . $chapters['icon_class']));
            }
        }

        if (!empty($chapter_arr)) {
            $status = 'success';
            $msg = $this->lang->line('chapter_list');
            $response = $chapter_arr;
        }
        $data = array(
            'status' => $status,
            'msg' => $msg,
            'data' => $response,
        );
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Getting details of each chapter and sub-topic
     */
    public function chapter_details_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $users_id = $this->get_user();
        $user = $this->user->get_detail($users_id);
        if ($user['user_type'] != 3) {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('unauthorised_see_content')), REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }
        $type = isset($request['type']) ? $request['type'] : '';
        $param = isset($request['value']) ? $request['value'] : '';
        $arm = $user['arm_alloted'] ? $user['arm_alloted'] : '';
        $field = $request['type'] == 'slug' ? 'slug' : 'id';
        $is_sub_topic = isset($request['is_sub_topic']) && $request['is_sub_topic'] == true;
        $is_added_favorite = false;

        if (!$param) {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('invalid_data')), REST_Controller::HTTP_OK);
            exit;
        }
        if (empty($type)) {
            $data = array('status' => 'error', 'msg' => $this->lang->line('select_slug_id'), 'data' => array());
            $this->response($data, REST_Controller::HTTP_OK);
            exit;
        }

        $content = $this->education->get_content_details(array('field' => $field, 'param' => $param, 'is_sub_topic' => $is_sub_topic, 'arm' => $arm, 'users_id' => $users_id, 'is_app'=>true));

        if ($is_sub_topic && isset($content['id'])) {
            $is_added_favorite = $this->education->check_content_added_in_favorite($content['id'], $this->get_user());
        }

        $data = array('status' => 'success', 'data' => $content, 'is_added_favorite' => $is_added_favorite);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    /**
     * Add/Remove favorite for a user
     */
    // public function update_favorite_post()
    // {
    //     $request = json_decode(file_get_contents('php://input'), true);
    //     $user_id = $this->get_user();
    //     $content_id = isset($request['content_id']) && $request['content_id'] ? $request['content_id'] : '';
    //     $request['user_id'] = $user_id;
    //     if (!$content_id) {
    //         $this->response(array('status' => 'error', 'msg' => $this->lang->line('invalid_content')), REST_Controller::HTTP_OK);
    //         exit;
    //     }

    //     $result = $this->education->update_favorite($request);
    //     $this->response($result, REST_Controller::HTTP_OK);

    // }

    /**
     * Get the list of all favorites added by a user
     */
    // public function my_favorite_get()
    // {
    //     $user_id = $this->get_user();
    //     if (!$user_id) {
    //         $this->response(array('status' => 'error', 'msg' => $this->lang->line('not_authorized')), REST_Controller::HTTP_OK);
    //         exit;
    //     }

    //     $result = $this->education->get_user_favorite($user_id);
    //     $this->response($result, REST_Controller::HTTP_OK);

    // }


    /**
     * Getting details of each exercise and exercise items
     */
    public function exercise_details_post()
    {
        $request = json_decode(file_get_contents('php://input'), true);
        $users_id = $this->get_user();
        $user = $this->user->get_detail($users_id);
        if ($user['user_type'] != 3) {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('unauthorised_see_content')), REST_Controller::HTTP_UNAUTHORIZED);
            exit;
        }

        $content_id = isset($request['content_id']) ? $request['content_id'] : '';
        $exercise_id = isset($request['exercise_id']) ? $request['exercise_id'] : '';
        $exercise_data = isset($request['exercise_data']) ? $request['exercise_data'] : '';
        $fields_all = isset($request['fields_all']) ? $request['fields_all'] :false;
        $arm = $user['arm_alloted'] ? $user['arm_alloted'] : '';

        if ($exercise_id=='') {
            $this->response(array('status' => 'error', 'msg' => $this->lang->line('invalid_data')), REST_Controller::HTTP_OK);
            exit;
        }

        if ($content_id=='') {
            $data = array('status' => 'error', 'msg' => $this->lang->line('select_chapter_id'), 'data' => array());
            $this->response($data, REST_Controller::HTTP_OK);
            exit;
        }

        $log="User [$users_id] has accessed exercise ".$exercise_id;
        $status='success'; $msg='';

        if(isset($exercise_data) && !empty($exercise_data) && $exercise_data){
            $content = $this->education->set_exercise_details(array('fields_all'=>$fields_all, 'content_id' => $content_id, 'arm' => $arm, 'exercise_id' => $exercise_id,'exercise_data'=>$exercise_data,'users_id'=>$users_id));
            $log="User [$users_id] has submited exercise ".$exercise_id;
        }else{
            $content = $this->education->get_exercise_details(array('fields_all'=>$fields_all, 'content_id' => $content_id, 'arm' => $arm, 'exercise_id' => $exercise_id,'users_id'=>$users_id));

            if(empty($content)){
                $status='error';
                $msg=$this->lang->line('user_not_active');
                $log="User [$users_id] has not accessed exercise ".$exercise_id;
            }
        }
        

        generate_log($log);
        $data = array('status' => $status, 'data' => $content,'msg' =>$msg);
        $this->response($data, REST_Controller::HTTP_OK);
    }
    
}

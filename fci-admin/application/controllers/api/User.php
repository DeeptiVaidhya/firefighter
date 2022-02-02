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

class User extends REST_Controller {

    /**
     * @desc Class Constructor
     */
    public function __construct() {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Token, Content-Type, X-XSRF-TOKEN");


        parent::__construct();
        $this->load->model('User_model', 'user');
        $this->load->model('Auth_model', 'auth');
        $this->check_token();
    }



    /**
     * Change Profile functionality
     */
    public function user_post() {
        $register_data = json_decode(file_get_contents('php://input'), true);

        if (!empty($register_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($register_data);
            $this->form_validation->set_rules($this->config->item("registerForm"));
            $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            // if ($register_data['user_type'] == 4) {
            //     $this->form_validation->set_rules('subject_id', 'subject id', 'required');
            //     // $this->form_validation->set_rules('cancer_type', 'Cancer type', 'required');
            //     // $this->form_validation->set_rules('primary_provider', 'primary provider', 'required');
            //     // $this->form_validation->set_rules('secondary_provider', 'secondary provider', 'required');
            //     $this->form_validation->set_rules('gender', 'gender', 'required');
            // }

            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $register_data['users_id'] = $this->get_user();
                $res = $this->auth->signup($register_data);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Change Profile functionality
     */
    public function user_put() {
        $update_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($update_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($update_data);
            $this->form_validation->set_rules($this->config->item("registerForm"));
            // if ($update_data['user_type'] == 4) {
            //     $this->form_validation->set_rules('subject_id', 'subject id', 'required');
            //     // $this->form_validation->set_rules('cancer_type', 'Cancer type', 'required');
            //     // $this->form_validation->set_rules('primary_provider', 'primary provider', 'required');
            //     // $this->form_validation->set_rules('secondary_provider', 'secondary provider', 'required');
            //     $this->form_validation->set_rules('gender', 'gender', 'required');
            // }
            if ($update_data['previous_email'] != $update_data['email']) {
                $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            }
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->user->update_user($update_data);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    public function user_get() {
        $user_type = $this->input->get('user_type');
        $is_active = $this->input->get('is_active');
        $is_active = isset($is_active) ?  $is_active  : false;
        $user_list = array();
        $user_id = $this->get_user();
        $user_list = $this->user->get_users_list(array('user_type' => $user_type,'is_active'=>$is_active)); // , 'created_by' => $user_id // commenting to get all patients / providers
        $this->response(array('status' => 'success', 'data' => $user_list['result']), REST_Controller::HTTP_OK);
    }

    public function user_detail_post() {
        $status = 'error';
        $msg = $this->lang->line('user_not_found');
        $data = array();
        $post_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($post_data)) {
            $user_id = $post_data['user_id'];
            $user_type = $post_data['user_type'];
            $result = $this->user->get_users_list(array('user_type' => $user_type, 'user_id' => $user_id,'is_active'=>true));
            if (!empty($result) && isset($result['result'])) {
                $status = 'success';
                $data = $result['result'];
                $msg = '';
            }
        }
        $this->response(array('status' => $status, 'data' => $data, 'msg' => $msg), REST_Controller::HTTP_OK);
    }

    /**
     * @desc get list of all patients who are having elevated symptoms
     */
    function report_get() {
        $status = 'success';
        $msg = $this->lang->line('patients_elevated_symptoms');
        if ($this->input->get('user_id')) {
            $user_id = $this->input->get('user_id');
        } else {
            $user_id = $this->get_user();
        }
        $user_detail = $this->user->get_detail($user_id);
        $data = array();
        $result = $this->user->get_patient_report(array('user_id' => $user_id));
        if (!empty($result)) {
            $data['result'] = $result['res'];
            $msg = '';
            $data['user_detail'] = $user_detail;
        }
        $data['current_week'] = $result['current_week'];
        $this->response(array('status' => $status, 'data' => $data, 'msg' => $msg), REST_Controller::HTTP_OK);
	}
	
    /**
     * @desc Add User Activity
     */
    function user_activity_post() {
        $input_data = json_decode(file_get_contents('php://input'), true);
        $input_data['users_id'] = $this->get_user();
        $result = $this->user->add_activity($input_data);
        $this->response($result, REST_Controller::HTTP_OK);
    }
}

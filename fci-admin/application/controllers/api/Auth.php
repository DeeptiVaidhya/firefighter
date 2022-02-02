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

require APPPATH . '/libraries/REST_Controller.php';

/**
 * This is Auth API Controller used to interact with application's common functionality like checking & managing
 * Tokens for logged in users, Login, Logout, etc.
 *
 * @package         Oncotool
 * @subpackage      Rest Server
 * @category        Controller
 * @license         MIT
 */
class Auth extends REST_Controller
{

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Authorization, Token, Content-Type, X-XSRF-TOKEN");

        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->load->model('User_model', 'users');
    }

    /**
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     */
    public function login_post()
    {

        $login_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($login_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            $this->form_validation->set_data($login_data);
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );

                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $username = $login_data['username'];
                $password = $login_data['password'];
                $result = $this->auth->login($username, $password, 3);

                $this->set_response($result);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Method: POST
     * Header Key: Authorization
     * Value: Auth token generated in GET call
     * desc: add user 
     */
    public function sign_up_post() {
        $register_data = json_decode(file_get_contents('php://input'), true);
    
        if (!empty($register_data)) {
            $this->config->load("form_validation");
            $this->form_validation->set_data($register_data);
            $this->form_validation->set_rules($this->config->item("addRegisterForm"));
            $this->form_validation->set_rules('email', 'Email', 'callback_unique_email');
            if ($this->form_validation->run() == FALSE) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array()
                );
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $res = $this->auth->app_register($register_data);
                $this->response($res, REST_Controller::HTTP_OK);
            }
        } else {
            $data = array(
                'status' => 'error',
            );
            $this->response($data, REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /**
     * Checking user is logged in and token is valid or not
     */
    public function check_login_post()
    {
        $this->check_token(true);
    }

    /** Forgot Password */
    public function forgot_password_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_email = (isset($insert_data['email'])) ? $insert_data['email'] : false;
        $result = false;
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $current_email);
        if (isset($user_detail['id']) && $user_detail['user_type'] == '3') {
            $result = $this->auth->email_check($current_email);
        }

        if (!$result) {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
        
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->auth->forgotten_password($insert_data['email'], true);
                $this->response($res, REST_Controller::HTTP_CREATED);
            }
        }
    }
    public function generate_access_code_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email');
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $access_code = $this->auth->generate_access_code($insert_data['email'], true);
                $data = array(
                    'status' => 'success',
                    'access_code' => $access_code,
                );
                $this->response($data, REST_Controller::HTTP_CREATED);
            }
        }
    }

    /** change Activate status */
    public function change_active_status_post()
    {
        $params = json_decode(file_get_contents('php://input'), true);
        if (!empty($params['user_id'])) {
            $result = $this->user->update_status($params);
            if ($result > 0) {
                $res['status'] = "success";
                $res['msg'] = $this->lang->line('user_update_success');
                $this->response($res, REST_Controller::HTTP_OK);
            }
        }
    }

    /**
     * Method: Get
     * Header Key: Authorization
     */
    public function profile_get()
    {
       
        $users_id = $this->get_user();
        $data = array();
        $data = $this->users->get_detail($users_id);

        $this->response(array('status' => 'success', 'data' => $data), REST_Controller::HTTP_OK);
    }

    public function check_email($email)
    {   
        if ($email != '') {
            $count = $this->auth->email_check($email);
            if (!$count) {
                $this->form_validation->set_message('check_email', $this->lang->line('email_not_exixt'));
                return false;
            } else {
                return true;
            }
        }
    }

    public function unique_email($email){
        $result = $this->check_email($email);
        if($result){
            $this->form_validation->set_message('unique_email', $this->lang->line('email_exixt'));
            return false;
        } else {
            return true;
        }
    }

    public function check_subject_id_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_subject_id = (isset($insert_data['current_subject_id'])) ? $insert_data['current_subject_id'] : false;
        $previous_subject_id = (isset($insert_data['previous_subject_id'])) ? $insert_data['previous_subject_id'] : false;
        $result = false;
        if ($current_subject_id != $previous_subject_id) {
            $result = $this->users->check_subject_id($current_subject_id);
        }

        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    public function check_email_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_email = (isset($insert_data['current_email'])) ? $insert_data['current_email'] : false;
        $previous_email = (isset($insert_data['previous_email'])) ? $insert_data['previous_email'] : false;
        $result = false;
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $current_email);
        if ($current_email != $previous_email && isset($user_detail['id']) && $user_detail['user_type'] == '3') {
            $result = $this->auth->email_check($current_email);
        }
        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_username_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        $current_username = (isset($insert_data['current_username'])) ? $insert_data['current_username'] : false;
        $previous_username = (isset($insert_data['previous_username'])) ? $insert_data['previous_username'] : false;
        $result = false;
        if ($current_username != $previous_username) {
            $result = $this->auth->username_check($current_username);
        }
        if ($result) {
            $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
        }
    }

    /** User logout */
    public function logout_get()
    {
        $token = $this->input->server('HTTP_TOKEN');
        if ($token) {
            if ($this->auth->update_token($token)) {
                $this->response(array('status' => 'success', 'msg' => $this->lang->line('logged_out')), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    public function profile_post()
    {
        $this->check_token();
        $insert_data = json_decode(file_get_contents('php://input'), true);

        // p($insert_data);
        if (!empty($insert_data)) {
            $this->form_validation->set_data($insert_data);
            $this->form_validation->set_rules('first_name', 'first_name', 'required');

            // Check Email
            if ($insert_data['previous_email'] != $insert_data['email']) {
                $this->form_validation->set_rules('email', 'Email', 'required|is_unique[users.email]|valid_email');
            }
            // Check Password
            if ($insert_data['password'] != '') {
                $this->form_validation->set_rules('current_password', 'Current Password', 'required');
                $this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]');
                $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required');
            }
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $users_id = $this->get_user();
                $user_data = $insert_data;
                // $user_data['last_name'] = $insert_data['last_name'];
                $user_data['id'] = $users_id;
                $this->auth->update_profile($user_data);
                $update_login_detail['email'] = $insert_data['email'];
                $update_login_detail['username'] = $insert_data['email'];
                $update_login_detail['password'] = $insert_data['password'];
                $update_login_detail['id'] = $users_id;
                $result = $this->auth->update_login_detail($update_login_detail);
                $this->response(array('status' => $result['status'], 'msg' => $result['msg']), REST_Controller::HTTP_OK);
            }
        } else {
            $this->response(array('status' => 'error'), REST_Controller::HTTP_METHOD_NOT_ALLOWED);
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_password_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $users_id = $this->get_user();
            $result = $this->auth->hash_password_db($users_id, $insert_data['password']);
            if (!$result) {
                $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    /** Callback function to check username exist or not in system */
    public function check_previous_password_post()
    {
        $insert_data = json_decode(file_get_contents('php://input'), true);
        if (!empty($insert_data)) {
            $users_id = $this->get_user();
            $result = $this->auth->check_password_history($users_id, $insert_data['password']);
            if ($result) {
                $this->response(array('status' => 'success'), REST_Controller::HTTP_OK);
            } else {
                $this->response(array('status' => 'error'), REST_Controller::HTTP_OK);
            }
        }
    }

    public function base64_to_image($profile_image = array())
    {
        if (!empty($profile_image)) {
            $config = $this->config->item('assets_images');
            $upload_path = check_directory_exists($config['path']);
            $path = $profile_image['filename'];
            $file_name = pathinfo($path, PATHINFO_FILENAME) . '-' . uniqid() . '.png';
            $img = $profile_image['value'];
            $data = base64_decode($img);
            $file_path = $upload_path . '/' . $file_name;
            $success = file_put_contents($file_path, $data);
            return $success ? $file_name : false;
        }
    }

    public function reset_password_post()
    {
        $res['status'] = 'error';
        $res['msg'] = $this->lang->line('unable_make_request');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $code = !empty($input['code']) != '' ? $input['code'] : (!empty($input['access_code']) != '' ? $input['access_code'] : '');

            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code, !empty($input['access_code']));
                if (!empty($profile)) {
                    $this->form_validation->set_data($input);
                    $this->form_validation->set_rules($this->config->item("resetPasswordForm"));
                    if ($this->form_validation->run() == false) {
                        $data = array(
                            'status' => 'error',
                            'data' => $this->form_validation->error_array(),
                        );
                        $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
                    } else {
                        $id = $profile->id;
                        $password = $input['password'];
                        $res = $this->auth->reset_password($id, $password);
                    }
                } else {
                    $res['msg'] = $this->lang->line('code_link_invalid');
                }
            } else {
                $res['status'] = "error";
                $res['msg'] = $this->lang->line('password_link_invalid');
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function reset_password_code_post()
    {
        $res['status'] = 'error';
        $res['msg'] = $this->lang->line('unable_make_request');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $code = $input['code'];
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);
                if (!empty($profile)) {

                    if (time() > $profile->forgotten_password_time) {
                        $res['status'] = "error";
                        $res['msg'] = $this->lang->line('password_link_expired');
                    } else {
                        $res['status'] = "success";
                        $res['msg'] = $this->lang->line('password_link_verified');
                    }
                } else {
                    $res['status'] = "error";
                    $res['msg'] = $this->lang->line('forgot_password_link_invalid');
                }
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function verify_survey_code_post()
    {
        $res['status'] = 'error';
        $res['msg'] = $this->lang->line('unable_make_request');
        $input = json_decode(file_get_contents('php://input'), true);

        $users_id = $this->get_user();

        if (!empty($input)) {
            $code = $input['code'];
            if ($code != '') {
                $profile = $this->auth->survey_code_detail($code,$users_id);
                if (!empty($profile)) {
                    if (time() > $profile->forgotten_password_time) {
                        $res['status'] = "error";
                        $res['msg'] = $this->lang->line('survey_link_expired');
                    } else {
                        $res['status'] = "success";
                        $res['msg'] = $this->lang->line('survey_link_verified');
                    }
                } else {
                    $res['status'] = "error";
                    $res['msg'] = $this->lang->line('survey_link_invalid');
                }
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function contact_us_post()
    {
        $res['status'] = 'error';
        $res['msg'] = $this->lang->line('unable_send_request');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!empty($input)) {
            $this->form_validation->set_data($input);
            $this->form_validation->set_rules($this->config->item("contactUsForm"));
            if ($this->form_validation->run() == false) {
                $data = array(
                    'status' => 'error',
                    'data' => $this->form_validation->error_array(),
                );
                $this->response($data, REST_Controller::HTTP_BAD_REQUEST);
            } else {
                $res = $this->auth->send_contact_us_email($input);
            }
        }
        $this->response($res, REST_Controller::HTTP_OK);
    }

    public function update_session_time_post()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $users_id = $this->get_user();
        // var_dump($users_id);
        if ($users_id && isset($input['focus']) && $input['focus']) {
            $res = $this->auth->update_session_time($users_id);
            $this->response($res, REST_Controller::HTTP_OK);
        }
        $this->response(array("status" => 'success'), REST_Controller::HTTP_OK);

    }

    public function country_list_get()
    {
        $result = $this->auth->country_list();
        $this->response($result, REST_Controller::HTTP_OK);

    }

}

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
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model', 'auth');
        $this->config->load('auth', TRUE);

        /** add error delimiters * */
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
	}
	
    /**
     * Login
     * Get username and password if user authenticate it redirect in 
     *  dashboard else it redirect to login page
     * @return Array
     * */
    public function login() {
        if ($this->session->userdata('logged_in') != FALSE) {
            redirect('user');
        }
        if ($this->input->post()) {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("loginForm"));
            if ($this->form_validation->run() != FALSE) {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $user_type = 1;
                $result = $this->auth->login($username, $password, $user_type);
               
                $this->session->set_flashdata($result['status'], $result['msg']);
                if (!empty($result) && $result['status'] == 'success') {
                    // Add user data in session
    
                    $this->session->set_userdata('logged_in', $result['userdetail']);
                    if($result['userdetail']->user_type == 1){
                        redirect('user');
                    }
                    redirect('dashboard');
                } else {
                    redirect('auth');
                }
            }
        }
        $this->load->view('login');
    }

    /**
     * Logout
     * Delete user session and redirect to login page
     * @return Bool
     * */
    public function logout() {
        $this->session->sess_destroy();
        $this->session->set_flashdata('success', 'User logout successfully');
        redirect('auth');
    }

    /**
     * Forgot Password
     * Take user email and emailed user password reset link in email address
     * @return Bool
     * */
    public function forgot_password() {
        if ($this->input->post()) {
            $this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email_exists');
            if ($this->form_validation->run() != FALSE) {
                $result = $this->auth->forgotten_password($this->input->post('email'));
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('auth');
            }
        }
        $this->load->view('forgot_password');
    }

    /**
     * Reset password functionality
     */
    public function reset_password($code) {
        $this->data['heading'] = 'RESET PASSWORD';
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if ($code != '') {
                $profile = $this->auth->forgotten_code_detail($code);

                if (!empty($profile)) {
                    if ($this->config->item('forgot_password_expiration', 'auth') > 0) {
                        $interval = abs(time() - $profile->forgotten_password_time);
                        $minutes = round($interval / 60);
                        $expiration = $this->config->item('forgot_password_expiration', 'auth');
                        if ($minutes > $expiration * 60) {
                            $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_expired'));
                            redirect('auth');
                        }
                    }
                    $this->data['code'] = $profile->forgotten_password_code;
                    $this->data['user_id'] = $profile->id;
                    $this->load->view('reset_password', $this->data);
                } else {
                    $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_invalid'));
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_invalid'));
                redirect('auth');
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->config->load("form_validation");
            $this->form_validation->set_rules($this->config->item("resetPasswordForm"));
            $code = $this->input->post('forgotten_code');
            $profile = $this->auth->forgotten_code_detail($code);
            $this->data['code'] = $profile->forgotten_password_code;
            $this->data['user_id'] = $profile->id;
            if ($this->form_validation->run() == FALSE) {
                $this->load->view('reset_password', $this->data);
            } else {
                $id = $this->input->post('user_id');
                $password = $this->input->post('password');
                $res = $this->auth->reset_password($id, $password);
                $this->session->set_flashdata($res['status'], $res['msg']);
                redirect('auth');
            }
        }
	}
	
	/***
	 * Send reset link if expired for a user
	 */
	function resend_invite_link(){
		if ($this->input->post('email')) {
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email|callback_check_email_exists');
			$sendInviteType = $this->input->post('send_type');
            if ($this->form_validation->run() != FALSE) {
				$result = $this->auth->forgotten_password($this->input->post('email'),false,$sendInviteType=='create-password');
				if($result['status']=='success'){
					$result['msg']=sprint($this->lang->line('link_generated_sent'),$this->input->post('email'));
				}
				echo json_encode($result);
				exit;
			}
		}
		echo json_encode(array('status'=>'error','msg'=> $this->lang->line('invalid_request')));
		exit;
	}

    /**
     * Reset password functionality
     */
    public function verify_email($code) {
        if ($code != '') {
            $profile = $this->auth->authorization_code_detail($code);
            if (!empty($profile)) {
                if ($this->config->item('authorization_code_expiration', 'auth') > 0) {
                    $interval = abs(time() - $profile->authorization_time);
                    $minutes = round($interval / 60);
                    $expiration = $this->config->item('authorization_code_expiration', 'auth');
                    if ($minutes > $expiration * 60) {
                        $this->session->set_flashdata('error', $this->lang->line('forgot_password_link_expired'));
                    }
                }
                $result = $this->auth->verify_email($profile->id);
                $this->session->set_flashdata($result['status'], $result['msg']);
            } else {
                $this->session->set_flashdata('error', $this->lang->line('email_line_invalid'));
            }
        } else {
            $this->session->set_flashdata('error', $this->lang->line('email_line_invalid'));
        }
        redirect($this->config->item('app_url'));
    }

    public function index() {
        $this->login();
    }

    /**
     * Check Email
     * It is a callback function take user email to check if email exist in system or not
     * @return Bool
     * */
    function check_email() {
        $email = $this->input->post('email');
        if ($email != '') {
            $count = $this->auth->email_check($email);
            if ($count) {
                $this->form_validation->set_message('check_email', $this->lang->line('email_exits'));
                return FALSE;
            } else {
                return TRUE;
            }
        }
	}
	
	/**
     * Check Email
     * It is a callback function take user email to check if email exist in system or not
     * @return Bool
     * */
    function check_email_exists() {
		$email = $this->input->post('email');
		$count = FALSE;
		$user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        if ($email != '' && isset($user_detail['user_type']) && isset($user_detail['id']) && ($user_detail['user_type'] == '1' || $user_detail['user_type'] == '2')) {
            $count = $this->auth->email_check($email);
            
		}
		if (!$count) {
			$this->form_validation->set_message('check_email_exists', $this->lang->line('email_not_exits'));
			return FALSE;
		} else {
			return TRUE;
		}
    }

    /**
     * Check Username
     * It is a callback function take user email to check if email exist in system or not
     * @return Bool
     * */
    function check_username() {
        $username = $this->input->post('username');
        if ($username != '') {
            $count = $result = $this->auth->username_check($username);
            if ($count) {
                $this->form_validation->set_message('check_username',$this->lang->line('username_exits'));
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    /**
     * Change Profile functionality
     */
    public function profile() {
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
    
        $data['login_user_detail'] = $this->session->userdata('logged_in');
        $this->breadcrumbs->push('Profile', 'profile');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        if ($this->input->post()) {
            if ($this->input->post('action') == 'basic_information') {
                $this->form_validation->set_rules($this->config->item("basicInfoForm"));
                if ($this->form_validation->run() == FALSE) {
                    $this->template->content->view('profile', $data);
                } else {
                    $user_data['first_name'] = $this->input->post('first_name');
					$user_data['last_name'] = $this->input->post('last_name');
					if($this->input->post('gender')){
						$user_data['gender'] = $this->input->post('gender');
					}
					if($this->input->post('email')){
						$user_data['email'] = $this->input->post('email');
					}
					if($this->input->post('phone_number')){
						$user_data['phone_number'] = $this->input->post('phone_number');
					}
                    $user_data['id'] = $data['login_user_detail']->id;
                    $user_data['profile_picture'] = $data['login_user_detail']->profile_picture;
                    $upload_data = $this->upload_image($user_data['profile_picture']);

                    if (!empty($upload_data)) {
                        $user_data['profile_picture'] = $upload_data['file_name'];
                    }

                  
                    $result = $this->auth->update_profile($user_data);
                    var_dump($result);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    if (!empty($result) && $result['status'] == 'success') {
                        $this->session->unset_userdata('logged_in');
                        $session_data = $result['userdetail'];
                        $this->session->set_userdata('logged_in', $session_data);
                        
					} 
					redirect('auth/profile');
                }
			}

            if ($this->input->post('action') == 'login_detail') {
                // Check Username 
                if ($data['login_user_detail']->username != $this->input->post('username')) {
                    $this->form_validation->set_rules('username', 'Username', 'required|callback_check_username');
                }

                // Check Email 
                if ($data['login_user_detail']->email != $this->input->post('email')) {
                    $this->form_validation->set_rules('email', 'Email', 'required|callback_check_email|valid_email');
                }

                // Check Password 
                if ($this->input->post('password') != '') {
                    $this->form_validation->set_rules('password', 'Password', 'matches[confirm_password]|is_valid_password');
                    $this->form_validation->set_rules('confirm_password', 'Confirm password', 'required');
                }

                $this->form_validation->set_rules('action', 'action', 'required');

                if ($this->form_validation->run() == FALSE) {
                    $this->template->content->view('profile', $data);
                } else {
                    $user_data['email'] = $this->input->post('email');
                    $user_data['username'] = $this->input->post('username');
                    $user_data['password'] = $this->input->post('password');
                    $user_data['id'] = $data['login_user_detail']->id;
                    $result = $this->auth->update_login_detail($user_data);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    if (!empty($result) && $result['status'] == 'success') {
                        $this->session->unset_userdata('logged_in');
                        $session_data = $result['userdetail'];
                        $this->session->set_userdata('logged_in', $session_data);
                        redirect('auth/profile');
                    } else {
                        redirect('auth/profile');
                    }
                }
            }
        } else {
            $this->template->content->view('profile', $data);
        }
        $this->template->publish();
    }

    function upload_image($previous_profile) {
        if ($_FILES['profile_image']['name'] != '') {
            $config = $this->config->item('assets_images');
            $filename = $_FILES['profile_image']['name'];
            $upload_path = check_directory_exists($config['path']);
            $config['upload_path'] = $upload_path;
            $path = $_FILES['profile_image']['name'];
            $config['file_name'] = pathinfo($path, PATHINFO_FILENAME) . '-' . uniqid();
            $this->load->library('upload', $config);
            // Upload the File
            if (!$this->upload->do_upload('profile_image')) {
                $error = $this->upload->display_errors();
                return $error;
            } else {
                $data = $this->upload->data();
                $filename = $previous_profile;
                $path = $upload_path . '/' . $filename;
                // file name not blank and file exists then delete it
                if ($filename != '' && file_exists($path)) {
                    unlink($path);
                }
                return $data;
            }
        } else {
            return array();
        }
    }

}

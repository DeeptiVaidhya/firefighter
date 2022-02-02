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
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Name:    Auth Model
 *
 * Requirements: PHP5 or above
 *
 */
class Auth_model extends CI_Model
{

    public $expire_time;

    public function __construct()
    {
        parent::__construct();
        $this->expire_time = $this->config->item('lockout_time');
        $this->config->load('auth', true);
        $this->load->helper('date');
        $this->tables = array('users' => 'users', 'users_secure' => 'users_secure');
        $this->load->model('user_model', 'user');
        $this->load->model('Questionnaire_model', 'questionnaire');
        $this->load->model('User_model', 'user');
        // initialize db tables data
        //initialize data
        $this->store_salt = $this->config->item('store_salt', 'auth');
        $this->salt_length = $this->config->item('salt_length', 'auth');
        // initialize hash method options (Bcrypt)
        $this->hash_method = $this->config->item('hash_method', 'auth');
        $this->default_rounds = $this->config->item('default_rounds', 'auth');
        $this->random_rounds = $this->config->item('random_rounds', 'auth');
        $this->min_rounds = $this->config->item('min_rounds', 'auth');
        $this->max_rounds = $this->config->item('max_rounds', 'auth');
        // load the bcrypt class if needed
        if ($this->hash_method == 'bcrypt') {
            if ($this->random_rounds) {
                $rand = rand($this->min_rounds, $this->max_rounds);
                $params = array('rounds' => $rand);
            } else {
                $params = array('rounds' => $this->default_rounds);
            }

            $params['salt_prefix'] = $this->config->item('salt_prefix', 'auth');
            $this->load->library('bcrypt', $params);
        }
    }

    /**
     * @desc Login to the Web Application
     * @param type $username
     * @param type $password
     * @param type $usertype
     * @return type
     */
    public function login($username, $password, $type) {
        $user = $this->user->get_encrypted_user_detail(array('username'), $username);
        if (!empty($user)) {
            if ($user['user_type'] != $type && $type == 3) {
                $status = 'error';
                $msg = $this->lang->line('not_access_application');
            } else {
                if ($user['user_type'] > 1) {
                    $user = $this->activate_account($user);
                }
                $user = (object) $user;

                if ($this->is_max_login_attempts_exceeded($user->id)) {
                    // Hash something anyway, just to take up time
                    generate_log("User [$user->id] account is deactivated due to many failed login attempts");
                    return array('status' => 'error', 'msg' => sprintf($this->lang->line('login_attempts_exceed'), $this->expire_time)); //'This account is inactive due to many failed login attempts. Please try again after ' . $this->expire_time . ' minute(s)'
                }
                $password = $this->hash_password_db($user->id, $password);
                if ($password === true) {
                    if (!$user->is_authorized) {
                        generate_log("User [$user->id] account is not verified yet. Please check your Inbox to verify your account.");
                        return array('status' => 'error', 'msg' => $this->lang->line('account_not_verified'));
                    }
                    if (!$user->is_active) {
                        generate_log("User [$user->id] account account is not active.");
                        return array('status' => 'error', 'msg' => $this->lang->line('account_not_active'));
                    }
                    $this->update_login($user->id);
                    $status = 'success';
                    $msg = $this->lang->line('account_logged_successfully');
                    if ($user->user_type > 2) {
                        $loged_in_user = $this->db->select('id, logout_time')->where('users_id', $user->id)->limit(1)->order_by('id', 'DESC')->get('user_tokens')->result_array();
                        if(isset($loged_in_user) && $loged_in_user){
                            $detail = $this->questionnaire->get_week_info($user->id);
                            if (!isset($loged_in_user[0]['logout_time']) && !$loged_in_user[0]['logout_time']) {
                                $detail = $this->questionnaire->get_week_info($user->id);
                                if (isset($detail->id)) {
                                    $this->db->update('week_sessions', array('end_time' => date('Y-m-d H:i:s')), array('week_info_id' => $detail->id, 'end_time' => null));
                                }
                            }
                        }
                        generate_log("User [$user->id] logged in successfully");
                        
                        return array('status' => $status, 'msg' => $msg, 'token' => $this->create_token($user->id),
                            'is_invite_singup'=>isset($user->created_by) && !empty($user->created_by),
                         'arm' => $user->arm_alloted, 'role' => $user->user_type, 'username' => ucwords(implode(' ', array($user->first_name, $user->last_name))));
                    
                    } else {
                        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
                    }
                } else {
                    $status = 'error';
                    $msg = 'Incorrect password';
                    if ($user->user_type > 1) {
                        $max_attempts = $this->config->item('maximum_login_attempts', 'auth');
                        $remaining_attemps = ($max_attempts - (int) ($user->login_attempts + 1));
                        if ($remaining_attemps < 1) {
                            $msg = $this->lang->line('account_inactive') . $this->expire_time . ' minute(s).';
                        } else {
                            $msg .= '. ' . $remaining_attemps . ' attempts remaining';
                        }
                    }
                    $this->increase_login_attempts($user->encrypt_username);
                    generate_log("User [$user->id] $msg");
                    return array('status' => $status, 'msg' => $msg);
                }
            }
        }
        $status = 'error';
        $msg = $this->lang->line('not_valid');
        return array('status' => $status, 'msg' => $msg);
    }

    public function app_register($params = array())
    {
        $status = 'error';
        $msg = $this->lang->line('error_while_register_user');
        $log = 'Error while user registration';
        extract($params);
        $salt = $this->store_salt ? $this->salt() : FALSE;
        $password = $this->hash_password($password, $salt);
        $first_name = isset($first_name) ? aes_256_encrypt(trim($first_name)) : FALSE;
        $email = (isset($email) && $email) ? trim($email) : NULL;
        $address = (isset($address) && $address) ? $address : NULL;
        $countries_id = (isset($country)) ? $country : NULL;        
        $user_type = '3';
        $arm_alloted = 'study';

        $user_data = array(
            'first_name' => $first_name,
            'email' => aes_256_encrypt(trim($email)),
            'username' => aes_256_encrypt(trim($email)),
            'password' => $password,
            'arm_alloted' => $arm_alloted,
            'salt' => $salt,
            'user_type' => $user_type,
            'address' => $address,
            'countries_id' => $countries_id,
            'created_at' => date('Y-m-d H:i:s'),
        );

        $this->db->trans_start();
        $this->db->insert('users', $user_data);
        $id = $this->db->insert_id('users');
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            if ($this->send_authorization_code(trim($email))) {
				$status = 'success';
				// activate user immediately at the time of registration
				// passing researcher id as -1 to identify if the participants has done self signup
				$response = $this->user->add_week_info($id,-1,date('Y-m-d H:i:s'));
				$msg = 'Your account has been created successfully. Please verify your email to activate your account';
				$log = "User[$id] account has been created successfully. Send verification email to activate account";
                if ($response['status']!='success') {
                    $msg .= 'Your week info is not activated, please contact your researcher to activate your account after email verification.';
                    $log .= "Your week info is not activated, please contact your researcher to activate your account.";
				}
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
        
    }

    /**
     * @desc Registration for users
     * @param type $data
     * @return type
     */
    public function signup($params = array())
    {
        $status = 'error';
        $msg = $this->lang->line('error_while_register_user');
        $log = 'Error while register user';
        extract($params);
        $first_name = (isset($first_name)) ? $first_name : null;
        $last_name = (isset($last_name)) ? $last_name : null;
        $email = (isset($email)) ? $email : null;
        $phone_number = (isset($phone_number)) ? $phone_number : null;
        $user_type = isset($user_type) ? $user_type : null;
        $gender = isset($gender) ? $gender : null;
        $subject_id = (isset($subject_id)) ? $subject_id : null;
        $arm_alloted = (isset($arm_alloted)) ? $arm_alloted : null;
        $users_id = (isset($users_id) && !empty($users_id))? $users_id : null;
        $username = $email;
        $userarr = [2 => 'Researcher', 3 => 'Patient'];
        // Users table.
        $user_data = array(
            'user_type' => $user_type,
            'gender' => $gender,
            'subject_id' => $subject_id,
            'arm_alloted' => $arm_alloted,
            'created_at' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'is_authorized' => 1,
            'created_by' => $users_id,
		);
		

		$user_data['first_name']= $first_name ? aes_256_encrypt(trim($first_name)) : '';
		$user_data['last_name']= $last_name ? aes_256_encrypt(trim($last_name)) : '';
		$user_data['email']= $email ? aes_256_encrypt(trim($email)) : '';
		$user_data['phone_number']= $phone_number ? aes_256_encrypt(trim($phone_number)) : '';
		$user_data['username']= $username ? aes_256_encrypt(trim($username)) : '';
		
        // filter out any data passed that doesnt have a matching column in the users table
        // and merge the set user data and the additional data
        $this->db->trans_start();
        $this->db->insert('users', $user_data);
        $id = $this->db->insert_id('users');
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {

            $is_signup = true;
            $is_app = $user_type != 2;

            $res = $this->forgotten_password($email, $is_app, $is_signup);
            if ($res['status'] == 'success') {
                $status = 'success';
                $msg = $userarr[$user_type] . " " . $this->lang->line('account_added_success');
                $log = "User[$users_id] created $userarr[$user_type] [$id] account.";
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function send_authorization_code($email) {
        // All some more randomness
        $activation_code_part = "";
        if (function_exists("openssl_random_pseudo_bytes")) {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        }

        $key = $this->hash_code($activation_code_part . $email);

        // If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == FALSE) {
            // preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
            // compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
            if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key)) {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        }

        // Limit to 40 characters since that's how our DB field is setup
		$expire_time = $this->config->item('authorization_code_expiration', 'auth');
		$link_expires_at = time()+$expire_time*60*60;
        $update = array(
            'authorization_code' => $key,
            'authorization_time' => $link_expires_at
        );
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        $status = 'error';
        $msg = 'Unable to make a request please try again later';
        if (!empty($user_detail)) {
            $this->db->update('users', $update, array('id' => $user_detail['id']));
            $return = $this->db->affected_rows() == 1;
            if ($return) {
                $expire_time = $this->config->item('authorization_code_expiration', 'auth');
                $content['link'] = base_url() . 'auth/verify-email/' . $key;
                $content['btntitle'] = $this->config->item('verify_email_btn_titte');
                $content['message'] = sprintf($this->config->item('verify_email_message'), ucfirst($user_detail['first_name']));
                $content['note'] = sprintf($this->config->item('verify_email_note'), $expire_time);
                $message = $this->load->view('email_template', $content, TRUE);
                $subject = $this->config->item('verify_email_subject');
                if (send_email($subject, $email, $message)) {
                    return TRUE;
                }
            }
            return FALSE;
        } else {
            return FALSE;
        }
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function forgotten_password($email, $is_app = false, $is_signup = false)
    {
        // All some more randomness
        $log = '';
        $activation_code_part = "";
        if (function_exists("openssl_random_pseudo_bytes")) {
            $activation_code_part = openssl_random_pseudo_bytes(128);
        }

        for ($i = 0; $i < 1024; $i++) {
            $activation_code_part = sha1($activation_code_part . mt_rand() . microtime());
        }

        $key = $this->hash_code($activation_code_part . $email);

        // If enable query strings is set, then we need to replace any unsafe characters so that the code can still work
        if ($key != '' && $this->config->item('permitted_uri_chars') != '' && $this->config->item('enable_query_strings') == false) {
            // preg_quote() in PHP 5.3 escapes -, so the str_replace() and addition of - to preg_quote() is to maintain backwards
            // compatibility as many are unaware of how characters in the permitted_uri_chars will be parsed as a regex pattern
            if (!preg_match("|^[" . str_replace(array('\\-', '\-'), '-', preg_quote($this->config->item('permitted_uri_chars'), '-')) . "]+$|i", $key)) {
                $key = preg_replace("/[^" . $this->config->item('permitted_uri_chars') . "]+/i", "-", $key);
            }
        }

        // Limit to 40 characters since that's how our DB field is setup
        $expire_time = $this->config->item('forgot_password_expiration', 'auth');
        $link_expires_at = time() + $expire_time * 60 * 60;
        $update = array(
            'forgotten_password_code' => $key,
            'forgotten_password_time' => $link_expires_at,
        );
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);
        $status = 'error';
        $msg = $this->lang->line('unable_make_request');
        if (!empty($user_detail)) {
            $this->db->update('users', $update, array('id' => $user_detail['id']));
            if ($this->db->affected_rows() == 1) {
                $content['note'] = sprintf($this->config->item('expiration_note'), $expire_time);
                if ($is_signup) {
                    $content['link'] = $this->config->item('app_url') . '#/create-password/' . $key;
                    if ($user_detail['user_type'] == 2) { // researcher will login to admin site
                        $content['link'] = base_url() . 'auth/reset-password/' . $key;
                    }
                    $content['btntitle'] = $this->config->item('create_password_btn_titte');
                    $content['heading'] = $this->config->item('create_password_heading');
                    $content['message'] = sprintf($this->config->item('welcome_message'), ucfirst($user_detail['first_name']));
                    $subject = $this->config->item('welcome_subject');
                } else {
                    $content['link'] = base_url() . 'auth/reset-password/' . $key;
                    if ($user_detail['user_type'] == 3) { // patient will login to app site
                        $content['link'] = $this->config->item('app_url') . '#/reset-password/' . $key;
                    }
                    $content['btntitle'] = $this->config->item('reset_password_btn_titte');
                    $content['message'] = sprintf($this->config->item('reset_password_message'), ucfirst($user_detail['first_name']));
                    $content['heading'] = $this->config->item('reset_password_heading');
                    $subject = $this->config->item('reset_password_subject');
                    $log = "User [$user_detail[id]] request for forgot password";
                }
                $message = $this->load->view('email_template', $content, true);

                $cc_email_sign_up = $this->config->item('cc_email_sign_up');

                if (send_email($subject, $email, $message, false, $cc_email_sign_up)) {
                    $status = "success";
                    $msg = $this->lang->line('password_reset_link');
                } else {
                    $status = 'error';
                    $msg = $this->lang->line('unable_make_request');
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Misc functions
     *
     * Hash password : Hashes the password to be stored in the database.
     * Hash password db : This function takes a password and validates it
     * against an entry in the users table.
     * Salt : Generates a random salt value.
     *
     */

    /**
     * @desc Hashes the password to be stored in the database.
     * @param type $password
     * @param type $salt
     * @param type $use_sha1_override
     * @return boolean
     */
    public function hash_password($password, $salt = false, $use_sha1_override = false)
    {
        if (empty($password)) {
            return false;
        }
        // bcrypt
        if ($use_sha1_override === false && $this->hash_method == 'bcrypt') {
            return $this->bcrypt->hash($password);
        }

        if ($this->store_salt && $salt) {
            return sha1($password . $salt);
        } else {
            $salt = $this->salt();
            return $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
        }
    }

    /**
     * This function takes a password and validates it
     * against an entry in the users table.
     *
     * @return void
     * ''
     * */
    public function hash_password_db($id, $password, $use_sha1_override = false)
    {
        if (empty($id) || empty($password)) {
            return false;
        }

        $query = $this->db->select('password, salt')
            ->where('id', $id)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get('users');

        $hash_password_db = $query->row();

        if ($query->num_rows() !== 1) {
            return false;
        }

        // bcrypt
        if ($use_sha1_override === false && $this->hash_method == 'bcrypt') {
            if ($hash_password_db->password != null && $this->bcrypt->verify($password, $hash_password_db->password)) {
                return true;
            }
            return false;
        }

        // sha1
        if ($this->store_salt) {
            $db_password = sha1($password . $hash_password_db->salt);
        } else {
            $salt = substr($hash_password_db->password, 0, $this->salt_length);

            $db_password = $salt . substr(sha1($salt . $password), 0, -$this->salt_length);
		}
		return $db_password == $hash_password_db->password;
    }

    /**
     * Generates a random salt value for forgotten passwords or any other keys. Uses SHA1.
     *
     * @return void
     * ''
     * */
    public function hash_code($password)
    {
        return $this->hash_password($password, false, true);
    }

    /**
     * Generates a random salt value.
     *
     * Salt generation code taken from https://github.com/ircmaxell/password_compat/blob/master/lib/password.php
     *
     * @return void

     * */
    public function salt()
    {

        $raw_salt_len = 16;

        $buffer = '';
        $buffer_valid = false;

        if (function_exists('random_bytes')) {
            $buffer = random_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('mcrypt_create_iv') && !defined('PHALANGER')) {
            $buffer = mcrypt_create_iv($raw_salt_len, MCRYPT_DEV_URANDOM);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && function_exists('openssl_random_pseudo_bytes')) {
            $buffer = openssl_random_pseudo_bytes($raw_salt_len);
            if ($buffer) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid && @is_readable('/dev/urandom')) {
            $f = fopen('/dev/urandom', 'r');
            $read = strlen($buffer);
            while ($read < $raw_salt_len) {
                $buffer .= fread($f, $raw_salt_len - $read);
                $read = strlen($buffer);
            }
            fclose($f);
            if ($read >= $raw_salt_len) {
                $buffer_valid = true;
            }
        }

        if (!$buffer_valid || strlen($buffer) < $raw_salt_len) {
            $bl = strlen($buffer);
            for ($i = 0; $i < $raw_salt_len; $i++) {
                if ($i < $bl) {
                    $buffer[$i] = $buffer[$i] ^ chr(mt_rand(0, 255));
                } else {
                    $buffer .= chr(mt_rand(0, 255));
                }
            }
        }

        $salt = $buffer;

        // encode string with the Base64 variant used by crypt
        $base64_digits = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64_digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64_string = base64_encode($salt);
        $salt = strtr(rtrim($base64_string, '='), $base64_digits, $bcrypt64_digits);

        $salt = substr($salt, 0, $this->salt_length);

        return $salt;
    }

    /**
     * Checks username
     *
     * @return bool
     * ''
     * */
    public function username_check($username = '')
    {
        if ($username != '') {
            $user_detail = $this->user->get_encrypted_user_detail(array('username'), $username);
            return count($user_detail) > 0;
        }
        return false;
    }

    /**
     * Checks email
     *
     * @return bool
     * ''
     * */
   public function email_check($email = '',$user_id=0) {
        if ($email != '') {
            $user_detail = $this->user->get_encrypted_user_detail(array('email'), trim($email));
            if(!$user_id) {
                return count($user_detail) > 0;
            } else {
                if(isset($user_detail['id']) && trim($user_detail['id']) != trim($user_id)) {
                    return count($user_detail) > 0;
                }
            }
        }

        return FALSE;
	}

    /**
     * Get User Detail Using Email
     *
     * @return Array
     * */
    public function user_detail($email = '')
    {
        if (empty($email)) {
            return false;
        }

        $query = $this->db->select('*')
            ->where('email', $email)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get('users');

        $user_detail = $query->row();

        if ($query->num_rows() > 0) {
            return $user_detail;
        } else {
            return false;
        }
    }

    /**
     * @param string $identity: user's identity
     * */
    public function increase_login_attempts($username)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {

            $this->db->select('login_attempts,id');
            $this->db->where('username', $username);
            $this->db->where('user_type !=', 1);
            $this->db->or_where('email', $username);
            $qres = $this->db->get('users');
            if ($qres->num_rows() > 0) {
                $user = $qres->row();
                if ($user->login_attempts == 2) {
                    $data = array('login_attempts' => $user->login_attempts + 1, 'is_active' => 0);
                } else {
                    $data = array('login_attempts' => $user->login_attempts + 1);
                }
                $data['updated_at'] = date('Y-m-d H:i:s');
                return $this->db->update('users', $data, array('id' => $user->id));
            }
        }
        return false;
    }

    /**
     * @param string $identity: user's identity
     * @return boolean
     * */
    public function is_max_login_attempts_exceeded($id)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $max_attempts = $this->config->item('maximum_login_attempts', 'auth');
            if ($max_attempts > 0) {
                $attempts = $this->get_attempts_num($id);
                return $attempts >= $max_attempts;
            }
        }
        return false;
    }

    /**
     * @param string $identity: user's identity
     * @return int
     */
    public function get_attempts_num($id)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $this->db->select('login_attempts');
            $this->db->where('id', $id);
            $qres = $this->db->get('users');
            if ($qres->num_rows() > 0) {
                $user = $qres->row();
                return $user->login_attempts;
            }
        }
        return 0;
    }

    /**
     * clear_login_attempts
     * Based on code from Tank Auth, by Ilya Konyukhov (https://github.com/ilkon/Tank-Auth)
     *
     * @param string $identity: user's identity
     * @param int $old_attempts_expire_period: in seconds, any attempts older than this value will be removed.
     *                                         It is used for regularly purging the attempts table.
     *                                         (for security reason, minimum value is lockout_time config value)

     * */
    public function update_login($user_id)
    {
        if ($this->config->item('track_login_attempts', 'auth')) {
            $this->db->where('id', $user_id);

            return $this->db->update('users', array('login_attempts' => 0, 'last_login' => date('Y-m-d H:i:s'), 'last_access_date' => date('Y-m-d H:i:s')));
        }
    }

    public function create_token($user_id)
    {
        // print_r($user_id);
        $this->db->select('token');
        $this->db->where('users_id', $user_id);
        $qres = $this->db->get('user_tokens');
        if ($qres->num_rows() > 0) {
            $user = $qres->row();
            $data = array(
                'users_id' => $user_id,
                'logout_time' => null,
            );
            $this->db->update('user_tokens', array('logout_time' => date("Y-m-d H:i:s")), $data);
        }
        $token = $this->hash_password(uniqid());
        $this->db->insert('user_tokens', array('users_id' => $user_id, 'token' => $token, 'login_time' => date("Y-m-d H:i:s")));
        $id = $this->db->insert_id('user_tokens');
        $user_detail = $this->user->get_detail($user_id);
        if ($user_detail['user_type'] == '3') {
            $week_info = $this->questionnaire->get_week_info($user_id);
            if (isset($week_info) && $week_info) {
                $this->db->insert('week_sessions', array('week_info_id' => $week_info->id, 'start_time' => date("Y-m-d H:i:s"), 'end_time' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d H:i:s")));
            }
            if(!$week_info){
                $last_week_info = $this->questionnaire->get_last_week_info($user_id);
                if($last_week_info){
                $today_date = new DateTime(date('Y-m-d H:i:s'));
                if(($today_date > new DateTime($last_week_info->week_ends_at)))
                {
                    $since_start = $today_date->diff(new DateTime($last_week_info->week_ends_at));
                    $diff = $since_start->days*24*60*60;
                    $diff += $since_start->h*60*60;
                    $diff += $since_start->i*60;
                    $diff += $since_start->s;
                    $current_week = ceil(($diff)/(7*24*60*60))+8;
                    $day_diff = (int)($since_start->days/7);
                    $start_date = date('Y-m-d', strtotime("+".((int)($day_diff*7+1))." day", strtotime($last_week_info->week_ends_at)));
                    $this->db->insert('week_info', array('week_number' => $current_week, 'is_study_week' => '1', 'week_starts_at' => $start_date, 'week_ends_at' => date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($start_date))), 'users_id' => $user_id));
                    $week_info_id = $this->db->insert_id();
                    $this->db->insert('week_sessions', array('week_info_id' => $week_info_id, 'start_time' => date("Y-m-d H:i:s"), 'end_time' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d H:i:s")));
                }
            }
        }

        }
        return (isset($id)) ? $token : false;
    }

    /**
     * Get forgotten code detail
     *
     * @return array
     * ''
     * */
    public function forgotten_code_detail($code = '', $is_access_code = false) {
        if (empty($code)) {
            return false;
        }
        $this->db->select('id,email,forgotten_password_code,forgotten_password_time');
        $this->db->where($is_access_code ? 'access_code' : 'forgotten_password_code', $code);
        $query = $this->db->limit(1)
            ->order_by('id', 'desc')
            ->get('users');
        // echo $this->db->last_query();
        $user_detail = $query->row();
        return $query->num_rows() > 0 ? $user_detail : false;
    }

    /**
     * Get survey code detail
     *
     * @return array
     * ''
     * */
    public function survey_code_detail($code = '',$user_id='') {
        if (empty($code)) {
            return false;
        }

        $this->db->select('id,email,forgotten_password_code,forgotten_password_time');
        $this->db->where('forgotten_password_code', $code);
        $query = $this->db->limit(1)
            ->order_by('id', 'desc')
            ->get('users');
        // echo $this->db->last_query();
        $user_detail = $query->row();

        if(trim($user_detail->id)==trim($user_id)){
            return $query->num_rows() > 0 ? $user_detail : false;
        }

        return false;
    }

    /**
     * reset password
     *
     * @return bool
     *
     * */
    public function reset_password($id, $password)
    {
        $is_exist = $this->db->where('id', $id)->limit(1)->count_all_results('users') > 0;
        $status = 'error';
        $msg = 'Password link is invalid';
        $log = "User [$id ] 'password link is invalid.";
        if ($is_exist) {
            $query = $this->db->select('id, password, salt')
                ->where('id', $id)
                ->limit(1)
                ->order_by('id', 'desc')
                ->get('users');

            if ($query->num_rows() == 1) {
                $is_password_exist = $this->check_password_history($id, $password);
                if (!$is_password_exist) {
                    $result = $query->row();
                    $salt = $this->store_salt ? $this->salt() : false;
                    $new = $this->hash_password($password, $salt);

                    $data = array(
                        'password' => $new,
                        'forgotten_password_code' => null,
                        'forgotten_password_time' => null,
                        'is_authorized' => 1,
                        'is_active' => 1,
                        'login_attempts' => 0,
                        'salt' => $salt,
                        'access_code' => null,
                    );

                    $this->db->update('users', $data, array('id' => $id));
                    $this->add_previous_password_detail($id);
                    $return = $this->db->affected_rows() == 1;
                    if ($return) {
                        $status = 'success';
                        $msg = $this->lang->line('password_reset_successfully');
                        $log = "User [$id] password reset successfully.";
                    }
                } else {
                    $status = 'error';
                    $msg = $this->lang->line('different_previous_password');
                    $log = "User [$id] your password must be different from the previous 6 passwords.";
                }
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Update Profile
     *
     * @return array
     *
     * */
    public function update_profile($params = array())
    {
        extract($params);
        $status = 'error';
        $msg = 'User not found.';
        $log = '';
        $user = array();
        $first_name = isset($first_name) ? aes_256_encrypt(trim($first_name)) : false;
        $last_name = isset($last_name) ? aes_256_encrypt(trim($last_name)) : false;
        $arm_alloted = isset($arm_alloted) ? $arm_alloted : false;
        $subject_id = isset($subject_id) ? trim($subject_id) : false;
        $gender = isset($gender) ? $gender : null;
        $address = isset($address) && $address ? $address : null;
        $countries_id = isset($country) && $country ? $country : null;
        $email = isset($email) ? $email : null;
        $phone_number = isset($phone_number) ? $phone_number : null;
        $profile_picture = isset($profile_picture) ? $profile_picture : null;
        $id = isset($id) ? $id : false;
        if ($id) {
            $data = array("first_name" => $first_name, 'profile_picture' => $profile_picture);
            !is_null($gender) && ($data['gender'] = $gender);
            $last_name && ($data['last_name'] = $last_name);
            $arm_alloted && ($data['arm_alloted'] = $arm_alloted);
            $subject_id && ($data['subject_id'] = $subject_id);
            !is_null($email) && ($data['email'] = aes_256_encrypt($email));
            !is_null($email) && ($data['username'] = aes_256_encrypt($email));
            !is_null($phone_number) && ($data['phone_number'] = aes_256_encrypt($phone_number));
            !is_null($address) && ($data['address'] = $address);
            !is_null($countries_id) && ($data['countries_id'] = $countries_id);

            $is_exist = $this->db->where('id', $id)->limit(1)->count_all_results('users') > 0;
            if ($is_exist) {
                $this->db->update($this->tables['users'], $data, array('id' => $id));
                $query = $this->db->select('*')
                    ->where(array('id' => $id))
                    ->limit(1)->get('users');
                if ($query->num_rows() > 0) {
                    $user = $query->row();
                    $user->username = aes_256_decrypt($user->username);
                    $user->first_name = aes_256_decrypt($user->first_name);
                    $user->last_name = aes_256_decrypt($user->last_name);
                    $user->email = $user->email ? aes_256_decrypt($user->email) : '';
                    $user->phone_number = $user->phone_number ? aes_256_decrypt($user->phone_number) : '';
                }
                $log = "User [$id] profile detail updated successfully";
                $status = 'success';
                $msg = $this->lang->line('profile_update_success');
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    /**
     * Update login detail
     *
     * @return array
     *
     * */
    public function update_login_detail($params = array())
    {
        extract($params);
        $is_password = false;
        if ($password != '') {
            $is_password = true;
        }
        $log = '';
        $user = array();
        $data = array("username" => aes_256_encrypt($username), 'email' => aes_256_encrypt($email));
        $query = $this->db->where('id', $id)->limit(1)->get('users');
        if ($query->num_rows() > 0) {
            $is_password_exist = $this->check_password_history($id, $password);
            if (!$is_password_exist) {
                $user_data = $query->row();
                if ($is_password) {
                    $salt = $this->store_salt ? $this->salt() : false;
                    $data['password'] = $this->hash_password($password, $salt);
                    $data['salt'] = $salt;
                }
                $this->db->update($this->tables['users'], $data, array('id' => $id));
                ($is_password) ? $this->add_previous_password_detail($id) : false;
                $query = $this->db->select('*')
                    ->where(array('id' => $id))
                    ->limit(1)->get('users');
                if ($query->num_rows() > 0) {
                    $user = $query->row();
                    $user->username = aes_256_decrypt($user->username);
                    $user->first_name = aes_256_decrypt($user->first_name);
                    $user->last_name = aes_256_decrypt($user->last_name);
                    $user->email = aes_256_decrypt($user->email);
                    $user->phone_number = $user->phone_number ? aes_256_decrypt($user->phone_number) : '';
                }
                $status = 'success';
                $log = "User [$id] login detail updated successfully";
                $msg = $this->lang->line('user_detail_update_success');
            } else {
                $status = 'error';
                $msg = $this->lang->line('different_previous_password');
                $log = "User [$id] your password must be different from the previous 6 passwords.";
            }
        } else {
            $status = 'error';
            $msg = $this->lang->line('user_not_found');
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg, 'userdetail' => $user);
    }

    /**
     * Store previous password detail
     *
     * @return boolean
     *
     * */
    public function add_previous_password_detail($id)
    {
        $query = $this->db->where('id', $id)->limit(1)->get('users');
        if ($query->num_rows() > 0) {
            $user_data = $query->row();
            $history = $this->db->where('users_id', $id)->order_by("id", "asc")->get($this->tables['users_secure']);
            if ($history->num_rows() > 5) {
                $history = $history->result();
                $this->db->delete($this->tables['users_secure'], array('id' => $history[0]->id));
            }
            $data['password_history'] = $user_data->password;
            $data['salt_history'] = $user_data->salt;
            $data['users_id'] = $id;
            $data["last_update"] = date('Y-m-d H:i:s');
            $this->db->insert($this->tables['users_secure'], $data);
            $insert_id = $this->db->insert_id('user_tokens');
            return (isset($insert_id)) ? true : false;
        }
    }

    /**
     * Get forgotten code detail
     *
     * @return array
     * ''
     * */
    public function authorization_code_detail($code = '')
    {   
        if (empty($code)) {
            return false;
        }

        $query = $this->db->select('id,authorization_code,authorization_time')
            ->where('authorization_code', $code)
            ->limit(1)
            ->order_by('id', 'desc')
            ->get('users');
        $user_detail = $query->row();
        if ($query->num_rows() > 0) {
            return $user_detail;
        } else {
            return false;
        }
    }

    /**
     * Store previous password detail
     *
     * @return boolean
     *
     * */
    public function verify_email($id)
    {
        $status = 'error';
        $msg = $this->lang->line('activate_account_error');
        if ($id > 0) {
            $query = $this->db->where('id', $id)->limit(1)->get('users');
            if ($query->num_rows() > 0) {
                $user_data = $query->row();
                $data["updated_at"] = date('Y-m-d H:i:s');
                $data["is_active"] = 1;
                $data["is_authorized"] = 1;
                $data["authorization_code"] = null;
                $data["authorization_time"] = null;
                $this->db->update($this->tables['users'], $data, array('id' => $id));
                $return = $this->db->affected_rows() == 1;
                if ($return) {
                    $status = 'success';
                    $msg = $this->lang->line('account_verified');
                }
            }
        }
        return array('status' => $status, 'msg' => $msg, 'id' => $class_id);
    }

    /**
     * Check Token
     * It is a callback function take user token to check if token exist in system or not
     * @return Bool
     * */
    public function check_token($token = false)
    {
        if ($token) {
			$query = $this->db->where(array('token' => $token, 'logout_time' => null))->limit(1)->get('user_tokens');
			//print_r($token);
            if ($query->num_rows() > 0) {
				// checks for last login and last access time,
				// if it exceeds with default session time then make a user log out forcefully.
				$tokenRow = $query->row();
				// get user details
				$user = $this->db->select('id,last_login,last_access_date')
								->where('id',$tokenRow->users_id)->get($this->tables['users'])->row();
				if(isset($user->last_access_date) && !is_null($user->last_access_date) && $user->last_access_date){
					// default session time
					$session_out_time = $this->config->item('session_logout_time');
					///$last_login = $user->last_login ? $user->last_login : $tokenRow->login_time;
					$diff = strtotime("now")-strtotime($user->last_access_date); // difference in seconds
					if($diff > $session_out_time){ // check for the difference
						$this->db->update('user_tokens', array('logout_time'=>$user->last_access_date), array('token' => $tokenRow->token));
						generate_log("User[$user->id] logged out forcefully because of session time out.");
						return FALSE;
					}
				}
				$this->db->update($this->tables['users'], array('last_access_date' => date('Y-m-d H:i:s')), array('id' => $user->id));
                
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * Delete Token
     * @return Bool
     * */
    public function update_token($token = false)
    {
        $flag = false;
        $data = '';
        $time_spent_in_min = null;
        if ($token) {
            $id = $this->get_user($token);
            $log = "User [$id] could not be logout.";
            $this->db->update('user_tokens', array('logout_time' => date('Y-m-d H:i:s')), array('token' => $token));
            $return = $this->db->affected_rows() == 1;
            if ($return) {
                $query = $this->db->where('token', $token)->limit(1)->get('user_tokens');
                $userdata = $query->row();

                $logout_time = new DateTime($userdata->logout_time);
                $time_spent = $logout_time->diff(new DateTime($userdata->login_time));
                $time_spent_in_min = ($time_spent->format('%h') * 60 + $time_spent->format('%i') + $time_spent->format('%s') / 60);

                $query = "SELECT * FROM week_info WHERE '" . $logout_time->format('Y-m-d') . "' between week_starts_at AND week_ends_at AND users_id = '" . $id . "'";
                $query = $this->db->query($query);

                if ($query->num_rows() > 0) {
                    $detail = $query->row();
                    $week_number = $detail->week_number;
                    $today_date = date('Y-m-d H:i:s');
                    $total_time_spent_in_week = $detail->total_time_spent_in_week;
                    if ($total_time_spent_in_week) {
                        $time_spent_in_min = ($total_time_spent_in_week + $time_spent_in_min) ? ($total_time_spent_in_week + $time_spent_in_min) : null;
                    }
                    $this->db->update('week_info', array('total_time_spent_in_week' => $time_spent_in_min), array('users_id' => $id, 'week_number' => $week_number));
                    $last_session = $this->db->select("id, end_time, start_time")->where('week_info_id', $detail->id)->order_by('id', 'desc')->limit(1)->get('week_sessions')->row();
                    if (isset($last_session) && $last_session) {
                        if ($last_session->id) {
                            $end_time = new DateTime($last_session->end_time);
                            $since_start = $end_time->diff(new DateTime(date('Y-m-d H:i:s')));
                            $sec = $since_start->days * 24 * 60 * 60;
                            $sec += $since_start->h * 60 * 60;
                            $sec += $since_start->i * 60;
                            $sec += $since_start->s;
                            if ($sec > 20) {
                                $this->db->insert('week_sessions', array('week_info_id' => $detail->id, 'start_time' => date("Y-m-d H:i:s"), 'end_time' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d H:i:s")));
                            } else {
                                $this->db->update('week_sessions', array('end_time' => date('Y-m-d H:i:s')), array('id' => $last_session->id));
                            }
                        }
                    }

                }
                $flag = true;
                $log = "User [$id] logged out successfully.";
            } else {
                $flag = false;
                $log = "User [$id] could not be logged out.";
            }
        }
        generate_log($log);
        return $flag;
    }

    /**
     * Check Token
     * It is a callback function take user token to check if token exist in system or not
     * @return Bool
     * */
    public function get_user($token = false)
    {
        if ($token) {
            $query = $this->db->where('token', $token)->limit(1)->get('user_tokens');
            if ($query->num_rows() > 0) {
                $userdata = $query->row();
                return $userdata->users_id;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @desc Insert a forgotten password key.
     * @param type $email
     * @return type
     */
    public function send_contact_us_email($params)
    {
        $status = 'error';
        $msg = $this->lang->line('unable_send_request');
        extract($params);
        $content['message_content'] = sprintf($this->config->item('contact_us_message'), ucfirst($name), $email, $telephone, $message);
        $content['is_admin'] = true;
        $email_content = $this->load->view('email_template', $content, true);
        $subject = $this->config->item('contact_us_subject');
        $to = $this->config->item('contact_us_email');
        if (send_email($subject, $to, $email_content)) {
            $status = "success";
            $msg = $this->lang->line('thanks_for_your_message');
        }

        return array('status' => $status, 'msg' => $msg);
    }

    public function check_password_history($id, $password)
    {

        if ($id) {
            $password_history = array();
            $is_password_exist = false;
            $history = $this->db->where('users_id', $id)->order_by("id", "asc")->get($this->tables['users_secure']);
            if ($history->num_rows() > 0) {
                $password_history = $history->result();
                foreach ($password_history as $val) {
                    if ($this->bcrypt->verify($password, $val->password_history)) {
                        $is_password_exist = true;
                    }
                }
            }
            return $is_password_exist;
        }
    }
    public function generate_access_code($email, $password)
    {
        $user_detail = $this->user->get_encrypted_user_detail(array('email'), $email);

        $user_id = $user_detail['id'];

        $query = "SELECT FLOOR(RAND() * 999999) AS access_code FROM users WHERE 'access_code' NOT IN (SELECT access_code FROM users where access_code IS NOT NULL) Limit 1";
        $query = $this->db->query($query);
        if ($query->num_rows() > 0) {
            $detail = $query->row();
            $access_code = $detail->access_code;
        }
        $this->db->update('users',
            array('access_code' => $access_code),
            array('id' => $user_id));
        if ($this->db->affected_rows() == 1);
        return $access_code;
    }

    public function activate_account($user, $forceActivate = false)
    {
        if (!empty($user)) {
            $last_update_time = $user['updated_at'];
            $current_time = date('Y-m-d H:i:s');
            $datetime1 = date_create($current_time);
            $datetime2 = date_create($last_update_time);
            $interval = date_diff($datetime1, $datetime2);
            $is_update = $forceActivate == true; // forcefully activate a user
            $data = array(
                'is_active' => 1,
                'login_attempts' => 0,
                'updated_at' => $current_time,
            );
            if (!$forceActivate) {
                $is_update = ($interval->i >= $this->config->item('lockout_time') || $interval->h);
                if (!$interval->h && $interval->i < $this->config->item('lockout_time')) {
                    $this->expire_time = $this->config->item('lockout_time') - $interval->i;
                }
            }
            if ($is_update) {
                $this->db->update($this->tables['users'], $data, array('id' => $user['id']));
                $user['is_active'] = 1;
                $user['login_attempts'] = 0;
            }
            return $user;
        }
    }

    public function update_session_time($user_id)
    {
        $week_info = $this->questionnaire->get_week_info($user_id);
        if (!empty($user_id) && $user_id && isset($week_info->id) && $week_info) {
            $last_session = $this->db->select("id, end_time, start_time")->where('week_info_id', $week_info->id)->order_by('id', 'desc')->limit(1)->get('week_sessions')->row();
            if ($last_session->id) {
                $end_time = new DateTime($last_session->end_time);
                $since_start = $end_time->diff(new DateTime(date('Y-m-d H:i:s')));
                $sec = $since_start->days * 24 * 60 * 60;
                $sec += $since_start->h * 60 * 60;
                $sec += $since_start->i * 60;
                $sec += $since_start->s;
                if ($sec > 20) {
                    $this->db->insert('week_sessions', array('week_info_id' => $week_info->id, 'start_time' => date("Y-m-d H:i:s"), 'end_time' => date("Y-m-d H:i:s"), 'created_at' => date("Y-m-d H:i:s")));
                    return array("status" => "success");
                }
                $this->db->update('week_sessions', array('end_time' => date('Y-m-d H:i:s')), array('id' => $last_session->id));
                return array("status" => "success");

            }
        }
        return array("status" => "error");

    }

    public function country_list()
    {
        $result = $this->db->select('id,country')
                            ->order_by('position')
                            ->get('countries')
                            ->result_array();
        return array('status'=>'success', 'data' => $result);
    }

}

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
 * Name:    User Model
 *
 * Requirements: PHP5 or above
 *
 */
class User_model extends CI_Model
{

    public $tables = array();

    public function __construct()
    {
        parent::__construct();
        $this->tables = array('users' => 'users', 'week_info' => 'week_info', 'user_activity' => 'user_activity', 'resources' => 'resources', 'week_sessions' => 'week_sessions');
        $this->load->model('questionnaire_model');
    }

    /**
     * @desc Get user detail by encrypted field.
     */

    public function get_encrypted_user_detail($field_name, $str, $is_case_insensitive = true)
    {
       
        $str = trim($str);
        $info_array = array('table' => $this->tables['users']);
        $info_array['fields'] = 'id,username as encrypt_username,username as username,first_name,last_name,email,is_active,is_authorized,user_type,profile_picture,gender,login_attempts,phone_number,updated_at,arm_alloted,countries_id, address,created_by';
        $userdata = $this->db_model->get_data($info_array);

        $user_detail = array();
        if (!empty($userdata['result'])) {
            $flagFound = false;
            foreach ($userdata['result'] as $key => $val) {
                if ($flagFound) {
                    break;
                }
                foreach ($field_name as $field) {
                    
                    //if (aes_256_decrypt($val[$field]) == $str) {
                    if (aes_256_decrypt($val[$field]) == $str || ($is_case_insensitive == true && strtolower(aes_256_decrypt($val[$field])) == strtolower($str))) {
                        $val['first_name'] = aes_256_decrypt($val['first_name']);
                        $val['last_name'] = aes_256_decrypt($val['last_name']);
                        $val['email'] = aes_256_decrypt($val['email']);
                        $val['phone_number'] = $val['phone_number'] ? aes_256_decrypt($val['phone_number']) : '';
                        $val['username'] = aes_256_decrypt($val['username']);
                        $user_detail = $val;
                        $flagFound = true;
                        break;
                    }
                }
            }
        }
        return $user_detail;
    }
    /**
     * Changing users arm
     * $user_id: whose arm to be changed
     * $arm: the arm to be assigned
     * $researcher_id: who changes the arm
     */
    public function switch_arm($user_id, $arm = null, $researcher_id)
    {
        $status = 'error';
        $msg = $this->lang->line('arm_update_error');

        $this->db->trans_start();
        $this->db->update($this->tables['users'], array('arm_alloted' => $arm), array('id' => $user_id));
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $status = 'success';
            $msg = $this->lang->line('arm_update_success');
            $log = "Researcher[$researcher_id] changed User[$user_id] arm to[$arm].";
            generate_log($log);
        }
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * Checks Subject Id for a patient is taken or not
     *
     * @return bool
     * ''
     *
     * */
    public function check_subject_id($subject_id = '')
    {
        if (empty($subject_id)) {
            return false;
        }
        return $this->db->where('subject_id', $subject_id)
            ->group_by("id")
            ->order_by("id", "ASC")
            ->limit(1)
            ->count_all_results('users') > 0;
    }

    /**
     * Get User Detail
     * @param  user_id
     * @return Array
     * */
    public function get_users_list($params = array())
    {
        extract($params);
        $user_type = isset($user_type) ? $user_type : false;
        $arm = isset($arm) ? $arm : null;
        $created_by = isset($created_by) ? $created_by : false;
        $user_id = isset($user_id) ? $user_id : false;
        $data = array('result' => []);

        if ($this->session->userdata('logged_in')->user_type == 2) {
            $col_sort = array("id", "", "activation_date", "subject_id", "");
        } else {
            $col_sort = array("id", "", "activation_date", "subject_id", "");
        }

        $info_array['fields'] = 'users.id as user_id,users.subject_id,users.phone_number,users.email,users.first_name,users.last_name,users.user_type,users.username as user_name,users.authorization_time,users.forgotten_password_time,users.is_active,users.login_attempts, users.activation_date, users.countries_id, users.address';
        $where = array('users.user_type !=' => 1);
        $order_by = "users.is_active";
        $order = 'DESC';
        $is_active = isset($is_active) ? $is_active : false;

        if (!$is_active) {
            $where = array('users.is_active' => 1, 'users.user_type !=' => 1);
            $order_by = "users.id";
        }

        if ($user_type) {
            $where['users.user_type'] = $user_type;
        }

        if ($arm) {
            $where['users.arm_alloted'] = "'" . strtoupper($arm) . "'";
        }

        if ($created_by) {
            $where['users.created_by'] = $created_by;
        }
        if ($user_id) {
            $where['users.id'] = $user_id;
        }

        $search_array = false;
        $join = false;
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }
        if (isset($where)) {
            $info_array['where'] = $where;
        }

        if (isset($params['sSearch']) && $params['sSearch'] != "") {
            $words = $params['sSearch'];
            $search_array = array();
            foreach ($col_sort as $key => $value) {
                $search_array[$value] = $words;
            }
            $info_array['like'] = $search_array;
        }
        if (isset($params['iDisplayStart']) && $params['iDisplayLength'] != '-1') {
            $start = intval($params['iDisplayStart']);
            $limit = intval($params['iDisplayLength']);
        }

        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        if (isset($start) && isset($limit)) {
            $info_array['start'] = $start;
            $info_array['limit'] = $limit;
        }
        $info_array['count'] = true;
        $info_array['join'] = $join;

        $info_array['table'] = $this->tables['users'];

        $result = $this->db_model->get_data($info_array);

        if (!empty($result['result'])) {
            foreach ($result['result'] as $key => $val) {

                $result['result'][$key]['email'] = aes_256_decrypt($val['email']);
                $result['result'][$key]['username'] = aes_256_decrypt($val['user_name']);
                $result['result'][$key]['first_name'] = ucwords(aes_256_decrypt($val['first_name']));
                $result['result'][$key]['last_name'] = ucwords(aes_256_decrypt($val['last_name']));
                $result['result'][$key]['phone_number'] = $val['phone_number'] ? aes_256_decrypt($val['phone_number']) : '';
                $result['result'][$key]['fullname'] = ucwords($result['result'][$key]['last_name'] . ' ' . $result['result'][$key]['first_name']);
                $result['result'][$key]['is_reset_password_required'] = ($val['forgotten_password_time'] && time() > $val['forgotten_password_time']);
				$result['result'][$key]['is_active'] = $val['is_active'];

                if ($user_type == 3) {
                    // get wee status for the user to update his questionnaire start date
                    $week_start_row = $this->db->where('users_id', $result['result'][$key]['user_id'])->get($this->tables['week_info'])->row();
                    if (isset($week_start_row->week_starts_at)) {
                        $result['result'][$key]['is_questionnaire_started'] = true;
						$result['result'][$key]['week_started_at'] = date('m/d/Y', strtotime($week_start_row->week_starts_at));
						if(is_null($val['activation_date'])){
							$val['activation_date']=$week_start_row->week_starts_at;
							$this->db->update('users',array('activation_date'=>$week_start_row->week_starts_at),array('id'=>$val['user_id']));
						}
                    } else {
                        $result['result'][$key]['is_questionnaire_started'] = false;
                    }
				}
				
				$result['result'][$key]['activation_date'] = $val['activation_date'];
            }
            $data = $result;
            if (!$is_active) {
                $data['result'] = $this->array_orderby($result['result'], 'last_name', SORT_ASC, 'first_name', SORT_ASC);
            }

        }
        $data['total'] = $result['total'];
        return $data;
    }

    public function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row) {
                    $tmp[$key] = $row[$field];
                }
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    /**
     * @desc get single user detail
     * @param type $params
     * @return array
     */
    public function get_detail($user_id)
    {
        $is_week_started = false;
        $info_array = array('where' => array('id' => $user_id), 'table' => $this->tables['users']);
        $info_array['fields'] = 'id,username,first_name,user_type,last_name,phone_number,email,gender,subject_id,is_active,is_authorized,profile_picture,arm_alloted,last_access_date, countries_id, address';
        $all_week_info = $this->db->select('week_starts_at')->where('users_id', $user_id)->order_by("id", "asc")->limit(1)->get($this->tables['week_info'])->row();
        if (isset($all_week_info->week_starts_at) && $all_week_info->week_starts_at) {
            $is_week_started = ($all_week_info->week_starts_at) <= date('Y-m-d H:i:s');
        }

        // getting sub page details like topics, testimonials
        $result = $this->db_model->get_data($info_array);
        if ($result['result']) {
            $userdetails = $result['result'][0];
            $userdetails['username'] = aes_256_decrypt($userdetails['username']);
            $userdetails['first_name'] = ucfirst(aes_256_decrypt($userdetails['first_name']));
            $userdetails['is_week_started'] = $is_week_started;
            $userdetails['last_name'] = ucfirst(aes_256_decrypt($userdetails['last_name']));
            $userdetails['phone_number'] = $userdetails['phone_number'] ? aes_256_decrypt($userdetails['phone_number']) : '';
            $userdetails['fullname'] = ucwords($userdetails['first_name'] . ' ' . $userdetails['last_name']);
            $userdetails['email'] = aes_256_decrypt($userdetails['email']);
            $userarr = [2 => 'Researcher', 3 => 'Patient'];
            $userdetails['role'] = $userarr[$userdetails['user_type']];
            $userdetails['last_access_date'] = $userdetails['last_access_date'];
            $assets_config = $this->config->item('assets_images');
            if (!empty($userdetails['profile_picture'])) {
                $userdetails['profile_picture'] = base_url($assets_config['path'] . '/' . $userdetails['profile_picture']);
            } else {
                $userdetails['profile_picture'] = assets_url('images/default-avatar.png');
            }

            return $userdetails;
        } else {
            return false;
        }
    }

    /**
     * @desc Update users
     * @param type $data
     * @return type
     */
    public function update_user($params = array())
    {
        $status = 'error';
        $msg = $this->lang->line('user_update_error');
        extract($params);
        $user_id = (isset($user_id)) ? $user_id : $user_id;
        $log = "User [$user_id] error while updating user.";
        $first_name = (isset($first_name)) ? trim($first_name) : null;
        $last_name = (isset($last_name)) ? trim($last_name) : null;
        $email = (isset($email)) ? $email : null;
        $gender = (isset($gender)) ? $gender : null;
        $cancer_type = (isset($cancer_type)) ? $cancer_type : null;
        $primary_provider = (isset($primary_provider)) ? $primary_provider : null;
        $secondary_provider = (isset($secondary_provider)) ? $secondary_provider : null;
        $subject_id = (isset($subject_id)) ? trim($subject_id) : null;

        $user_data = array(
            'first_name' => aes_256_encrypt($first_name),
            'last_name' => aes_256_encrypt($last_name),
            'subject_id' => $subject_id,
            'email' => aes_256_encrypt($email),
            'username' => aes_256_encrypt($email),
            'gender' => $gender,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $this->db->trans_start();
        $this->db->update($this->tables['users'], $user_data, array('id' => $user_id));
        $return = $this->db->affected_rows() == 1;
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            if ($return) {
                $status = 'success';
                $msg = $this->lang->line('user_info_update_success');
                $log = "User [$user_id] profile information updated successfully.";
            }
        }
        generate_log($log);
        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Get Week Info
     * @param type $user_id
     * @return array or Boolean in case not result is found
     */
    public function get_user_week_detail($user_id)
    {
        $user_detail = array();
        $info_array['fields'] = "u1.id as userId,u2.first_name as first_name,u2.last_name as last_name,week_info.week_number,week_info.week_starts_at,week_info.week_ends_at";
        $where = array("u1.id" => $user_id);
        $order_by = "week_number";
        $order = 'DESC';
        $join = false;

        if (isset($where)) {
            $info_array['where'] = $where;
        }

        $join = array(
            array('table' => 'users as u1', 'on' => 'u1.id = week_info.users_id', 'type' => 'LEFT'),
        );

        $info_array['order_by'] = $order_by;
        $info_array['group_by'] = "week_number";
        $info_array['order'] = $order;
        $info_array['count'] = true;
        $info_array['join'] = $join;

        $info_array['table'] = $this->tables['week_info'];
        $result = $this->db_model->get_data($info_array);
        $user_detail = $result['result'];
        if ($user_detail) {
            foreach ($user_detail as $key => $val) {
                $user_detail[$key]['start_date'] = date('M d Y', strtotime($val['week_starts_at']));
                $user_detail[$key]['end_date'] = date('M d Y', strtotime($val['week_ends_at']));
                $user_detail[$key]['is_email_sent'] = ($val['is_email_sent']) ? 'Send' : 'Not send';
                $user_detail[$key]['first_name'] = aes_256_decrypt($val['first_name']);
                $user_detail[$key]['last_name'] = aes_256_decrypt($val['last_name']);
                $user_detail[$key]['fullname'] = ucwords($user_detail[$key]['first_name'] . ' ' . $user_detail[$key]['last_name']);
            }
        }
        return $user_detail;
    }

    public function check_week_info($user_id)
    {
        $row = $this->db->where(array('week_number' => 1, 'users_id' => $user_id))->get('week_info')->row();
        if (isset($row->week_starts_at)) {
            return $row->week_starts_at;
        }
        return false;
    }

    /**
     * @desc Add Week Info, and activate a user for survey
     * @param type $user_id
     * @return array
     */
    public function add_week_info($user_id, $researcher_id, $date)
    {
        $msg = $this->lang->line('user_activate_error');
        $status = 'error';
        if ($this->db->where('users_id', $user_id)->count_all_results('week_info') < 1) {
            $this->db->trans_start();
            $start_date = $date ? $date : date('Y-m-d');
            $end_date = date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($start_date)));
            $this->db->insert('week_info', array('week_number' => 1, 'week_starts_at' => $start_date, 'week_ends_at' => $end_date, 'users_id' => $user_id));
            if ($this->db->insert_id()) {
                for ($i = 2; $i <= 8; $i++) {
                    $query = $this->db->where('users_id', $user_id)->limit(1)->order_by('id', 'desc')->get('week_info');
                    $week_detail = $query->row();
                    $week_starts_at = date('Y-m-d', strtotime("+1 day", strtotime($week_detail->week_ends_at)));
                    $this->db->insert('week_info', array('week_number' => $i, 'week_starts_at' => $week_starts_at, 'week_ends_at' => date('Y-m-d 23:59:59', strtotime("+6 day", strtotime($week_starts_at))), 'users_id' => $user_id));
                }
            }
            $this->db->trans_complete();

            if ($this->db->trans_status() != false) {
				
                $log = "Researcher[$researcher_id] activate week info for User[$user_id]";
                $status = 'success';
                generate_log($log);
                $msg = $this->lang->line('week_activated_success');
            }
        } else {
            $msg = $this->lang->line('week_info_already_added');
        }

        return array('status' => $status, 'msg' => $msg);
    }

    /**
     * @desc Add User Activity
     * @param type $data
     * @return type
     */
    public function add_activity($params = array())
    {
        $status = 'error';
        extract($params);
        $type = (isset($type)) ? $type : null;
        $action = (isset($eventAction)) ? $eventAction : null;
        $title = (isset($eventLabel)) ? $eventLabel : null;
        $page_name = isset($eventCategory) ? $eventCategory : null;
        $users_id = isset($users_id) ? $users_id : null;
        $device_info = isset($device_info) ? $device_info : null;
        $user_data = array(
            'type' => $type,
            'action' => $action,
            'page_name' => $page_name,
            'users_id' => $users_id,
            'title' => $title,
            'device_info' => $device_info,
        );
        $this->db->trans_start();
        $this->db->insert('user_activity', $user_data);
        $id = $this->db->insert_id('users');
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $status = 'success';
        }
        return array('status' => $status);
    }

    /**
     * @desc Get  User Activity Report
     * @param type $data
     * @return type
     */
    public function get_user_activity_report()
    {
        $this->load->helper('download');
        $csv_header = array();
        $csv_header = array('Name', 'Subject Id', 'Time', 'Type', 'Page', 'Title', 'Action', 'Device Information');
        $data = array();
        $query = $this->db->select('user_activity.*,first_name,last_name,subject_id')
            ->join('users', 'users.id=user_activity.users_id')
            ->order_by('user_activity.created_at', 'DESC')
            ->where("users.is_active = '1'")
            ->get($this->tables['user_activity']);
        if ($query->num_rows() > 0) {
            $user_activity = $query->result_array();
            foreach ($user_activity as $key => $val) {
                $data[$key][] = ucfirst(aes_256_decrypt($val['first_name'])) . ' ' . ucfirst(aes_256_decrypt($val['last_name']));
                $data[$key][] = $val['subject_id'];
                $data[$key][] = date('d-m-Y h:i:s T', strtotime($val['created_at']));
                $data[$key][] = ucfirst($val['type']);
                $data[$key][] = $val['page_name'];
                $data[$key][] = $val['title'];
                $data[$key][] = $val['action'];
                $data[$key][] = $val['device_info'];
            }
            $name = 'Tracking_report_' . uniqid() . '.csv';
            $csv_data = array();
            foreach ($data as $val) {
                $csv_data[] = implode('|', $val);
            }
            force_download($name, implode('|', $csv_header) . "\r\n" . implode("\r\n", $csv_data));
        }
    }

    public function update_status($params)
    {
        $update_data = array('is_active' => $params['status']);
        $this->db->update($this->tables['users'], $update_data, array('id' => $params['user_id']));
        return $this->db->affected_rows();
    }

}

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

class User extends CI_Controller
{

    /**
     * @desc Class Constructor
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->session->userdata('logged_in') == false) {
            redirect('auth');
        }
        $this->load->model('User_model', 'user');
        $this->load->model('Auth_model', 'auth');
        $this->load->model('Settings_model', 'settings');
        $this->load->model('Questionnaire_model', 'questionnaire');
        $this->load->model('Educational_model', 'educational');
        $this->load->model('Resource_model', 'resource');
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');

        $this->template->javascript->add(base_url('assets/js/sweetalert2.min.js'));
        $this->template->stylesheet->add(base_url('assets/css/sweetalert2.css'));  
    }

    // Call list users function by default
    public function index()
    {
        $this->list_users();
    }

    /**
     * Get arm type in capital letters
     */
    public function get_arm($arm_type = '')
    {
        $arm_type = strtolower($arm_type);
        return $arm_type == 'study' ? 'STUDY' : '';
    }
    /**
     * Text to be shown for arm
     */
    public function get_arm_text($arm_type = '')
    {
        return ucfirst(strtolower($this->get_arm($arm_type)));
    }

    /**
     * Function to switch user's arm
     */
    public function switch_arm()
    {
        $result = array('status' => 'error', 'msg' => 'No user selected');
        if ($this->input->post('user_id')) {
            $user_id = $this->input->post('user_id');
            $arm = $this->input->post('arm');
            $result = $this->user->switch_arm($user_id, $arm, $this->session->userdata('logged_in')->id);
        }
        echo json_encode($result);
        exit;
    }

    /**
     * @desc Showing list of all users
     *
     */
    public function list_users($arm_type = '')
    {

        get_plugins_in_template('datatable');

        $arm_type = $this->get_arm($arm_type);

        $this->template->title = 'List ' . $this->get_arm_text($arm_type) . ' User';
        $data['subheading'] = 'List of ' . ($arm_type ? $this->get_arm_text($arm_type) . ' participants' : 'users');
        $data['arm_type'] = strtolower($arm_type);
        $data['calling_url'] = base_url() . 'user/get-users-data?is_active=1';
        $data['is_researcher'] = false;
        if ($this->session->userdata('logged_in')->user_type == 2) {
            $data['subheading'] = 'List of ' . ($arm_type ? strtolower($arm_type) . ' participants' : 'study personnel');
            $data['calling_url'] = base_url() . 'user/get-users-data?arm=' . ($arm_type ? $arm_type . '&user_type=3' : '&user_type=2') . '&is_active=1';
            $data['is_researcher'] = true;
        }

        $this->template->content->view('list_users', $data);
        // Publish the template
        $this->template->publish();
    }

    /**
     * Function to add participant/researcher
     */
    public function add_user($arm_type = '')
    {
        if ($arm_type == 'researcher') {
            $data['user_type'] = 2;
            $data['arm_type'] = '';
        } else {
            $arm_type = $this->get_arm($arm_type);
            if (!$arm_type) {
                $this->session->set_flashdata('error', 'Invalid page');
                redirect('dashboard');
            }
            $data['user_type'] = 3;
            $data['arm_type'] = $arm_type;
        }

        $this->breadcrumbs->push('User', 'Add ' . $this->get_arm_text() . ' User');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("registerForm"));
            $this->form_validation->set_rules('email', 'Email', 'required|callback_check_email|valid_email');
            if ($this->input->post('user_type') == 3 && trim($this->input->post('subject_id')) || $data['user_type'] == 3) {
                $this->form_validation->set_rules('subject_id', 'subject id', array('regex_match[/^[a-zA-Z0-9--]+$/]', 'required'));
            }
            if ($this->form_validation->run() != false) {
                $request = $this->input->post();

                if ($request['user_type'] == 3) {
                    $request['arm_alloted'] = $data['arm_type'];
                    $request['users_id'] = $this->session->userdata('logged_in')->id;
                }
                $result = $this->auth->signup($request);
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('user/list-users/' . strtolower($data['arm_type']));
            }
        }
        $this->template->content->view('add_user', $data);
        $this->template->publish();
    }

    /**
     * Get list of users in Datatable
     */

    public function get_users_data()
    {
        $data = $this->user->get_users_list($this->input->get());
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        $userarr = [2 => 'Researcher', 3 => 'Patient'];
        foreach ($data['result'] as $val) {
            $li = '';
            $user_type = $userarr[$val['user_type']];
            $link = '<a class="user_detail" href="#" data-params=' . json_encode(array("user_id" => $val['user_id'])) . ' data-toggle="modal" data-target="#view_user" data-url="' . base_url() . 'user/get-detail" title="View"><i class="fa fa-eye"></i></a>';
            $email_week_start = $val['email'];
            if ($this->session->userdata('logged_in')->user_type == 2) {
                if ($val['user_id'] != $this->session->userdata['logged_in']->id && ($val['user_type'] == 2)) {

                    if ($val['is_active'] == 1) {
                        $li .= '<li><a href="' . base_url('user/edit-status/' . $val['user_id'] . '/0') . '">DE-ACTIVATE</a></li>';
                        $class = 'btn-tertiary';
                    } else if ($val['is_active'] == 0) {
                        $li .= '<li><a href="' . base_url('user/edit-status/' . $val['user_id'] . '/1') . '">ACTIVATE</a></li>';
                        $class = 'btn-primary-outline';
                    }
                    $link .= '<div class="dropdown d-inline">
                                <button class="btn ' . $class . ' dropdown-toggle" type="button" data-toggle="dropdown" title="Change Status">Status
                                <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    ' . $li . '
                                </ul>
                            </div>';

                }
                $third_field = !empty($val['phone_number']) ? $val['phone_number'] : '--';
                if ($val['user_type'] == 3) {
                    $email_week_start = '';
                    $third_field = !empty($val['subject_id']) ? $val['subject_id'] : '--';
                    $link .= 'Study';
                    $week_starts_at = $this->user->check_week_info($val['user_id']);
                    if ($week_starts_at != false) {
                        $email_week_start = date('Y-m-d', strtotime($week_starts_at));
                    }
                    $link = '<a class="user_detail" href="' . base_url() . 'user/participants-detail/' . $val['user_id'] . '" ><i class="fa fa-eye"></i></a>';
                }
            } else {

                $sendInviteType = '';
                if ($val['authorization_time'] && time() > $val['authorization_time']) {
                    $sendInviteType = 'create-password';
                } else if ($val['forgotten_password_time'] && time() > $val['forgotten_password_time']) {
                    $sendInviteType = 'reset-password';
                }
                if ($sendInviteType) {
                    $link .= '<a class="ajax-call" data-is-reload="true" href="javascript:void(0);" data-params=' . json_encode(array("email" => $val['email'], "user_id" => $val['user_id'], "last_name" => $val['last_name'], "first_name" => $val['first_name'], 'send_type' => $sendInviteType)) . ' data-url="' . base_url() . 'auth/resend-invite-link">' . ($sendInviteType == 'create-password' ? 'Invite again' : 'Reset password') . '</a>';
                }
                $third_field = $user_type;
            }
            $armUser = 'study';
            if ($val['login_attempts'] >= 5 && $val['is_active'] == 0) {
                $link .= '<a class="" href="' . base_url('user/activate-account/' . $val['user_id']) . '/' . $armUser . '" title="Unlock"><i class="fa fa-unlock" aria-hidden="true"></i></a>';
            }
            $full_name = ucfirst($val['first_name']) . ' <strong>' . ucfirst($val['last_name']) . '</strong>';
            $output['aaData'][] = array(
                "DT_RowId" => $val['user_id'],
                $i++,
                $full_name,
                $email_week_start,
                $third_field,
                $link,
            );
        }

        echo json_encode($output);
    }

    public function get_detail()
    {
        $data = array('status' => 'error', 'msg' => $this->lang->line('user_not_found'));
        if ($this->input->post()) {
            $user_id = $this->input->post('user_id');
            $result = $this->user->get_detail($user_id);
            if (!empty($result)) {
                $data = array('status' => 'success', 'data' => $result);
            }
        }
        echo json_encode($data);
        exit;
    }

    public function get_users_list()
    {
        $data = $this->user->get_users_list();
        $user = $data['result'];

        foreach ($user as $key => $value) {
            if ($value['user_name'] != null) {
                $update = array(
                    'username' => ($value['user_name'] != null) ? encrypt($value['user_name']) : null,
                    'first_name' => ($value['first_name'] != null) ? encrypt($value['first_name']) : null,
                    'last_name' => ($value['last_name'] != null) ? encrypt($value['last_name']) : null,
                    'email' => ($value['email'] != null) ? encrypt($value['email']) : null,
                );
                $this->db->update('users', $update, array('id' => $value['user_id']));
            }
        }
    }

    function check_email() {
        $email = $this->input->post('email');
        $id = $this->input->post('id');
        if ($email != '') {
            $count = $this->auth->email_check($email,$id);
            if ($count) {
                $this->form_validation->set_message('check_email', 'This email address is already being used.');
                return FALSE;
            } else {
                return TRUE;
            }
        }
	}

    public function edit_status($id = '', $status = '')
    {
        if (isset($status) && isset($id)) {
            $info_array['user_id'] = $id;
            $info_array['status'] = $status;

            $result = $this->user->update_status($info_array);
            if ($result) {
                $log = "User " . $this->session->userdata['logged_in']->id . " updated status of researcher " . $id;
                generate_log($log);
                $status = 'success';
                $msg = $this->lang->line('status_updated');
            } else {
                $status = 'error';
                $msg = $this->lang->line('status_not_updated');
            }
            $this->session->set_flashdata($status, $msg);
            redirect('/user/list-users/');
        }
    }

    public function activate_account($user_id, $arm = '')
    {
        $user['id'] = $user_id;
        $user['updated_at'] = $this->session->userdata('logged_in')->updated_at;
        $result = $this->auth->activate_account($user, true);
        $status = 'success';
        $msg = $this->lang->line('user_unlock_success');
        $this->session->set_flashdata($status, $msg);
        redirect('/user/list-users/' . $arm);
    }
    /**
     * get participant details
     */
    public function participants_detail($user_id, $type = '', $arm = '')
    {
        if (isset($user_id) && $user_id) {

            $user_info = is_numeric($user_id)?$this->user->get_detail($user_id):'';
            if (empty($user_info) || $user_info['user_type'] != 3) {
                $this->session->set_flashdata('error', 'Invalid user');
                redirect('/dashboard');
            }

            if ($user_info['user_type'] == 3) {
                $week_starts_at = $this->user->check_week_info($user_id);
                $user_info['week_starts_at'] = $week_starts_at;
                $data['details'] = $user_info;
            }

            if (isset($type) && $type) {
                switch ($type) {
                    case "events":
                        $params = array('user_id' => $user_id);
                        $data['type']['events'] = $this->questionnaire->get_achivements_and_events($params);
                        break;
                    case "exercises":
                        $data['type']['exercises'] = $this->resource->get_user_resources($arm, $user_id);
                        break;
                    case "time-spent":
                        $data['type']['time_spent'] = $this->questionnaire->weekly_time_spent($user_id);
                        break;
                    case "chapters":
                        $data['type']['chapters'] = $this->questionnaire->visited_subtopic_chapter($user_id, 'STUDY');
                        break;
                    case "resources":
                        $data['type']['resource'] = $this->questionnaire->get_visited_resources($user_id);
                }
            }
        }
        

        if ($this->input->post()) {

            $this->form_validation->set_rules($this->config->item("editParticipant"));
            $this->form_validation->set_rules('subject_id', 'Subject Id', array('regex_match[/^[a-zA-Z0-9--]+$/]', 'required'));
            $this->form_validation->set_rules('email', 'Email', 'required|callback_check_email|valid_email');
            if ($this->form_validation->run() != false) {
                $user_data = array();
                $user_data['first_name'] = $this->input->post('first_name');
                $user_data['last_name'] = $this->input->post('last_name');
                $user_data['email'] = $this->input->post('email');
                $user_data['subject_id'] = $this->input->post('subject_id');
                $user_data['week_starts_at'] = $this->input->post('week_starts_at');
                $user_data['phone_number'] = $this->input->post('phone_number');
                $user_data['id'] = $this->input->post('id');


                if ($user_data['week_starts_at'] && DateTime::createFromFormat('Y-m-d', $user_data['week_starts_at']) !== false) {
                    $res_start = $this->user->add_week_info($user_data['id'], $this->session->userdata('logged_in')->id, $this->input->post('week_starts_at'));
                }

                $result = $this->auth->update_profile($user_data);
                $this->session->set_flashdata($result['status'], $result['msg']);
                if ($result['status'] == "success") {
                    redirect('user/list_users/' . $user_data['arm_alloted']);
                }
               
            }
        }

        $this->template->content->view('participant/participant_details', $data);
        $this->template->publish();
    }

    public function save_events()
    {
        $event_data = $this->input->post();
        if (isset($event_data) && $event_data) {
            $data = $this->educational->save_events($event_data);
        }
        $this->session->set_flashdata($data['status'], $data['msg']);
        redirect('user/participants_detail/' . $event_data['user_id']);
    }
}

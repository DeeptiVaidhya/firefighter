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
 * Description of Questionnaire_model
 *
 */
class Questionnaire_model extends CI_Model
{

    public $tables = array();

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Auth_model', 'auth');

		$this->tables = array('options' => 'options', 'week_sessions' => 'week_sessions', 'week_info' => 'week_info', 'users' => 'users', 'users_content' => 'users_has_content', 'content' => 'content', 'question_groups' => 'question_groups','users_has_exercise_item'=>'users_has_exercise_item', 'questionnaires' => 'questionnaires','questionnaires_has_options' => 'questionnaires_has_options','questions'=>'questions');
		
	}
	
	/**
     * save the questionnaire answer
     * @param type $param
     * @return boolean
     */
    public function save_questions_answer($questions)
    {
        if (!isset($questions) || empty($questions)) {
            return array('status' => 'error', 'msg' => $this->lang->line('no_answer_selected'));
        }

        $question=$questions;
        $user_id = $questions['user_id'];
        $questionnaire_id='';

        $questionnaire=$this->db->select('id')->where(array('users_id'=>$user_id,'survey_date'=>date('Y-m-d')))->get($this->tables['questionnaires'])->row();

        if(empty($questionnaire)){
            $this->db->insert($this->tables['questionnaires'], array('users_id'=>$user_id,'survey_date'=>date('Y-m-d')));
            $questionnaire_id = $this->db->insert_id();
        }else{
            $questionnaire_id=$questionnaire->id;
            $this->db->delete($this->tables['questionnaires_has_options'], array('questionnaires_id' => $questionnaire_id,'questions_id'=>$question['questions_id']));
        }

        $this->db->trans_start();

        $data = array();
        $data['questionnaires_id'] = $questionnaire_id ;
        $data['questions_id'] = isset($question['questions_id']) ? $question['questions_id'] : NULL;
        $data['options_id'] = isset($question['options_id']) ? $question['options_id'] : NULL;
        $data['response'] = isset($question['response']) ? $question['response'] : NULL;
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->tables['questionnaires_has_options'], $data);

        $this->db->trans_complete();

        $status = 'error';
        $log="User [$user_id] has not submited to question ". $question['questions_id'];

        $msg = $this->lang->line('answer_save_error');
        if ($this->db->trans_status() !== false) {
            $status = 'success';
            $log = "User [$user_id] has submited to question ". $question['questions_id'];
            $msg = $this->lang->line('answer_save_success');
        }

        generate_log($log);
        return array('status' => $status, 'msg' => $msg);

	}
	
	/**
     * @desc Get all questionnaire's list
     */
    public function get_questionnaire($params = array())
    {
        extract($params);

        $user_id = isset($user_id) ? $user_id : false;
        $question_pos=$question_id = isset($question_id) ? $question_id : '';
        $type = isset($type) ? $type : '';
        $next_modules = array( 'M-1'=>'module-2', 'M-2'=> 'module-3', 'M-3'=> 'module-4', 'M-4'=> 'module-5');

        if (!isset($question_pos) || empty($question_pos) || !isset($type) || empty($type)) {
            return array('status' => 'error', 'msg' => $this->lang->line('invalid_question'));
        }

        $question=array();
        $questions = $this->db->select('id, title, question,short_description')
                                    ->where(array('position'=> $question_pos,'type'=>$type))
                                    ->get($this->tables['questions'])->result_array();

        $question_next = $this->db->select('position')
                                    ->where(array('position'=> $question_pos+1,'type'=>$type))
                                    ->get($this->tables['questions'])->row();


        if(isset($questions[0]) || !empty($questions[0])){
            $question=$questions[0];
            $question['options']= $this->db->select('id, option_value, option_label, answer_status')->where('questions_id', $questions[0]['id'])->get($this->tables['options'])->result_array();
         }

        $question['next_question']=null;
        if(isset($question_next) || !empty($question_next)){
            $question['next_question']=$question_next->position;
        }

        $question['next_module']=$next_modules[$type];

        $log = "User [$user_id] requesting for question type ". $type ." id " . $question['id'];
        generate_log($log);

        return array('question' => $question ,'status' => 'success');
    }

    /**
     * Time spent for a week
     */
    public function weekly_time_spent($user_id)
    {
        $all_weeks = $this->db->select('id, week_number')
            ->where(array('users_id' => $user_id))->get($this->tables['week_info'])->result_array();
        foreach ($all_weeks as $key => $week) {
            $total_time_spent_in_week = 0;
            $all_weeks[$key]['week_session'] = $this->db->select('id, start_time, end_time')->where('week_info_id', $week['id'])->get($this->tables['week_sessions'])->result_array();
            if (isset($all_weeks[$key]['week_session']) && $all_weeks[$key]['week_session']) {
                foreach ($all_weeks[$key]['week_session'] as $wkey => $wvalue) {
                    if (isset($wvalue['end_time'])) {
                        $end_time = new DateTime($wvalue['end_time']);
                        $since_start = $end_time->diff(new DateTime($wvalue['start_time']));
                        $total_time = $since_start->days * 24 * 60 * 60;
                        $total_time += $since_start->h * 60 * 60;
                        $total_time += $since_start->i * 60;
                        $total_time += $since_start->s;
                        $total_time_spent_in_week += $total_time;
                    }
                }
            }
            $all_weeks[$key]['total_time_spent_in_week'] = ($total_time_spent_in_week / 60);
        }
        return $all_weeks;
    }

    /**
     * update visited topic end time
     * @param type $params
     * @return boolean
     */

    public function update_visited_chapter_subtopic($params)
    {
        extract($params);
        $users_id = isset($user_id) && $user_id ? $user_id : false;
        $content_id = isset($content_id) && $content_id ? $content_id : false;
        $info_array['end_time'] = date('Y-m-d H:i:s');
        return array('status' => 'error');
    }

   
    /**
     * @desc Get Week Info
     * @param type $user_id
     * @return array or Boolean in case not result is found
     */
    public function get_week_info($user_id)
    {
        $start_date = date('Y-m-d');
        $this->db->where('week_starts_at <=', $start_date);
        $this->db->where('week_ends_at >=', $start_date);
        $this->db->where('users_id', $user_id);
        $query = $this->db->get('week_info');

        if ($query->num_rows() > 0) {
            $res = $query->row();
            return $res;
        } else {
            return false;
        }
    }

    /**
     * add chapter as visited
     * @param type $params
     * @return boolean
     */

    public function add_chapter_subtopic_visited($params)
    {
        extract($params);
        $info_array['content_callee_page'] = isset($callee_page) && $callee_page ? $callee_page : null;
        $info_array['users_id'] = isset($user_id) && $user_id ? $user_id : false;
        $info_array['content_id'] = isset($content_id) && $content_id ? $content_id : false;
        $info_array['created_at'] = date('Y-m-d H:i:s');
        $info_array['start_time'] = date('Y-m-d H:i:s');
        $info_array['end_time'] = date('Y-m-d H:i:s');

      
        if ($info_array['content_id'] && $info_array['users_id']) {
            -$this->db->trans_start();
            $this->db->insert($this->tables['users_content'], $info_array);
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                return array('status' => 'success');
            }
        }
        return array('status' => 'error');
    }

    /**
     * get visited subtopic list
     * @param type $params
     * @return array()
     */

    public function visited_subtopic_chapter($user_id = '', $type)
    {
        if (!$user_id) {
            return array();
        }
        $user_detail = $this->user->get_detail($user_id);
        $all_week = $this->db->select('id,week_number,week_starts_at, week_ends_at')->where('users_id', $user_id)->get('week_info')->result_array();
        // print_r($all_week);die();
        $week_data = array();
        $chapters = array();
        foreach ($all_week as $wkey => $wvalue) {
            $chapter = $this->db->select('uc.id,uc.end_time, uc.start_time,c.type,c.content_name, c.arm, uc.content_id, COUNT(c.id) AS total,SUM(TIMESTAMPDIFF(MINUTE, uc.start_time, uc.end_time)) AS TotalTimeSpentInMinutes')
					->where(array('uc.users_id' => $user_id, 'c.type' => 'CONTENT', 'c.arm'=>$user_detail['arm_alloted'],
					                'start_time>='=>date('Y-m-d', strtotime($wvalue['week_starts_at'])), 'start_time<='=>date('Y-m-d 23:59:59', strtotime($wvalue['week_ends_at']))))
                    ->join($this->tables['content'] . ' c', 'c.id = uc.content_id', 'inner')->group_by('uc.content_id')
                    ->get($this->tables['users_content'] . ' uc')
                    ->result_array();
            if(isset($chapter) && !empty($chapter)){
                $chapters[$wvalue['week_number']]=$chapter;
            } 
        }
        //die();
        return $chapters;
    }

    /**
     * get visited resource
     * @param type $user_id
     * @return array()
     */

    public function get_visited_resources($user_id = '')
    {
        // var_dump($user_id);
        if (!$user_id) {
            return array();
        }
        $resources = $this->db->select('r.type,r.title,uc.id, uc.users_id, uc.resources_id, COUNT(resources_id) AS access_count,COUNT(IF( total_player_time/2-left_player_time>0,1,NULL)) AS is_completed')
            ->where('uc.users_id = ' . $user_id . ' AND resources_id IS NOT NULL')
            ->join('resources r', 'r.id = uc.resources_id', 'inner')->group_by('uc.resources_id')
            ->get($this->tables['users_content'] . ' uc')->result_array();
        return $resources;
    }

    /**
     * add resource to visited list
     * @param type $params
     * @return array()
     */

    public function add_resource_to_visited($params)
    {
        extract($params);
        $info_arr = array();
        $info_arr['users_id'] = isset($user_id) && $user_id ? $user_id : false;
        $info_arr['content_id'] = isset($content_id) && $content_id ? $content_id : null;
        $info_arr['resources_id'] = isset($resource_id) && $resource_id ? $resource_id : false;
        $info_arr['content_callee_page'] = isset($callee_page) && $callee_page ? $callee_page : null;
        $info_arr['start_time'] = date('Y-m-d H:i:s');
        if ($info_arr['users_id'] && $info_arr['resources_id']) {
            $this->db->trans_start();
            $this->db->insert($this->tables['users_content'], $info_arr);
            $this->db->trans_complete();

            if ($this->db->trans_status() !== false) {
                return array('status' => 'success');
            }
        }

        return array('status' => 'error');
    }

    /**
     * @desc Get last Week Info
     * @param type $user_id
     * @return array or Boolean in case not result is found
     */
    public function get_last_week_info($user_id = '')
    {
        $data = $this->db->select('*')->where(array('users_id'=>$user_id, 'is_study_week' => '0'))->order_by('id', 'desc')->limit(1)->get('week_info')->row();
        if(isset($data) && !empty($data)){
            return $data;
        }
        return FALSE;
    }

    /**
     * @desc Get last Week Info
     * @param type $user_id
     * @return array or Boolean in case not result is found
     */
    public function get_achivements_and_events($params = array())
    {
       extract($params);
       $user_id = (isset($user_id) ? $user_id : false);
       $week_info_arr = array();
       $week_info_id = '';
       $week_info = $this->get_week_info($user_id);
       if($week_info){
        $week_info_id = $week_info->id;
       }
       if($user_id){
            $all_week_info = $this->db->select("id")->where('users_id', $user_id)->get($this->tables['week_info'])->result_array();
            $total_time_spent_in_week;    
            if(isset($all_week_info) && !empty($all_week_info)){
                foreach ($all_week_info as $key => $value) {
                    $total_time_in_week = 0;
                    $totalwatched = 0;
                    $info_arr = $this->db->select("id, week_number, users_id, event, is_study_week")->where('id', $value['id'])->get($this->tables['week_info'])->result_array();
                    $week_info_arr[] = $info_arr[0];
                    if($week_info_id == $value['id']){
                        $week_info_arr[$key]['is_current_week'] = true;
                    }else{
                        $week_info_arr[$key]['is_current_week'] = false;
                    }
                    $all_weeks_session = $this->db->select('id, start_time, end_time')->where('week_info_id', $value['id'])->get($this->tables['week_sessions'])->result_array();
                    if (isset($all_weeks_session) && $all_weeks_session) {
                        foreach ($all_weeks_session as $wkey => $wvalue) {
                            if (isset($wvalue['end_time'])) {
                                $end_time = new DateTime($wvalue['end_time']);
                                $since_start = $end_time->diff(new DateTime($wvalue['start_time']));
                                $total_time = $since_start->days * 24 * 60 * 60;
                                $total_time += $since_start->h * 60 * 60;
                                $total_time += $since_start->i * 60;
                                $total_time += $since_start->s;
                                $total_time_in_week += $total_time;
                                $week_info_arr[$key]['total_time_spent_in_week'] = floor($total_time_in_week/60);
                            }
                        }
                    }else{
                        $week_info_arr[$key]['total_time_spent_in_week'] = $total_time_in_week;
                    }
                    $total_time_and_week_number = $this->db->select("*")->where('id', $value['id'])->get($this->tables['week_info'])->result_array();
                    
            }
        }
        return $week_info_arr;
    }
}
    
}

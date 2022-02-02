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
 * Name:    Resource Model
 *
 * Requirements: PHP5 or above
 *
 */
class Resource_model extends CI_Model
{

    public $tables = array();

    public function __construct()
    {
        parent::__construct();
        $this->tables = array('resources' => 'resources', 'files' => 'files');
        $this->load->model('Questionnaire_model', 'questionnaire');
    }

    public function get_sub_content_resources($topic, $type)
    {
        $allResources = array();
        $resources = $this->db->select('r.id,r.type,r.title,r.link,r.description', false)
            ->where('cd.content_id=' . $topic['id'])
            ->order_by('r.id')
            ->join('content_details cd', 'r.id=cd.resources_id', 'inner')
            ->get('resources r')->result_array();
        foreach ($resources as $topRes) {
            $topRes['chapter_content_id'] = $topic['id'];
            $topRes['chapter_content_title'] = $topic['title'];
            $topRes['chapter_type'] = $type;
            $allResources[] = $topRes;
        }
        return $allResources;
    }

    /**
     * Function to get user resources to show on Resources page.
     */
    public function get_user_resources($arm, $user_id, $week_info = false)
    {
        $all_chapters = $this->db->where(array('arm' => $arm, 'type' => 'CONTENT'))->order_by('position')->get('content')->result_array();
        // print_r($all_chapters);
        $week_ids = array();
        $current_week = '';
        $week_after = true;
        $chapters = array();
       
        if ($week_info === false) {
            $week_info = $this->questionnaire->get_week_info($user_id);
        }
        
        if (isset($week_info->id) && $week_info->id) {
            $week_info_id = $week_info->id;
        }

        if (!isset($week_info_id)) {
            $last_week_info = $this->questionnaire->get_last_week_info($user_id);
            $today_date = new DateTime(date('Y-m-d H:i:s'));

            if(!$last_week_info){ return array(); exit;} 

            $diff = $today_date->diff(new DateTime($last_week_info->week_ends_at))->format("%a");
            $current_week = ceil($diff/7);
            if((isset($last_week_info) && !empty($last_week_info) && $today_date < $last_week_info->week_ends_at) || !isset($last_week_info) && empty($last_week_info))
            {
                return array();
            }
        }
        if(isset($week_info_id)){
            $week_after = false;
        }
        $all_week_info = $this->db->select('id')->where('users_id', $user_id)->get('week_info')->result_array();
        foreach ($all_week_info as $wkey => $wvalue) {
            $week_ids[] = $wvalue['id'];
        }

        foreach ($all_chapters as $key => $chapt) {
            $topics = $this->db->select('id,slug,title')->where(array('type' => 'TOPIC', 'content_id' => $chapt['id']))->get('content')->result_array();
            $chap_res_data = $this->db->select('chr.content_id as chapter_content_id, chr.resources_id as id, r.type,r.title,r.link,r.description')
                             ->where('content_id',$chapt['id'])
                             ->join('content_has_resources chr', 'r.id=chr.resources_id', 'inner')
                             ->get('resources r')->result_array();
            $chapt['resources'] = $chap_res_data; 
            foreach ($topics as $topic) {
                $chapt['resources'] = array_merge($chapt['resources'], $this->get_sub_content_resources($topic, 'TOPIC'));
                $subtopics = $this->db->select('id,slug,title')->where(array('type' => 'SUBTOPIC', 'content_id' => $topic['id']))->get('content')->result_array();
                foreach ($subtopics as $stopic) {
                    $sub_res_data = $this->db->select('chr.content_id as chapter_content_id, chr.resources_id as id, r.type,r.title,r.link,r.description')
                                             ->where('content_id',$stopic['id'])
                                             ->join('content_has_resources chr', 'r.id=chr.resources_id', 'inner')
                                             ->get('resources r')->result_array();
                    $chapt['resources'] = array_merge($chapt['resources'], $this->get_sub_content_resources($stopic, 'SUBTOPIC'));
                    $chapt['resources'] = array_merge($chapt['resources'], $sub_res_data);
                }
            }
	    if (isset($chapt['resources'])) {
                foreach ($chapt['resources'] as $rKey => $resource) {
                   
                    $chapt['resources'][$rKey]['link'] = trim_video_link($chapt['resources'][$rKey]['type'],$chapt['resources'][$rKey]['link']);
                    if(isset($week_info_id) && $week_info_id){
                        $chapt['resources'][$rKey]['week_info_id'] = $week_info_id;
                    }
                }
            }
            $chapters[] = $chapt;
        }
        
        return $chapters;
    }

    public function get_resources_list($params = array(), $type = '')
    {
        extract($params);
        $type = isset($type) ? strtoupper($type) : null;
        $data = array('result' => []);
        $col_sort = array("id", "title", "type");
        $info_array['fields'] = 'resources.id as id,resources.title as title,resources.type as type';
        $order_by = "resources.id";
        $order = 'DESC';
        $search_array = false;
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
        }
        if ($type) {
            $info_array['where'] = array('type' => "'" . $type . "'");
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
        // $info_array['debug'] = true;
        $info_array['table'] = $this->tables['resources'];
        $result = $this->db_model->get_data($info_array);
        $data = $result;
        if (!empty($result['result']) && $type) {
            foreach ($result['result'] as $key => $val) {
                if ($result['result'][$key]['type'] == $type) {
                    $result['result'][$key]['title'] = $val['title'];
                    $result['result'][$key]['type'] = $val['type'];
                }
            }
            $data = $result;
        }
        $data['total'] = $result['total'];
        return $data;
    }

    /**
     * This function is used to delete a resource
     */
    public function delete_resources($params = array())
    {
        extract($params);
        if (isset($where) && $where) {
            $info_array['where'] = $where;
        }
        $info_array['table'] = $this->tables['resources'];
        $result = $this->db_model->delete_data($info_array);
        if ($result == true) {
            $status = 'success';
        } else {
            $status = 'error';
        }
        return array('status' => $status, 'msg' => $this->lang->line('resourse_delete_' . $status));
    }

    public function update_resource($params = array())
    {
        $status = 'error';
        extract($params);
        $resource_type = (isset($resource_type)) ? $resource_type : null;
        $title = (isset($title)) ? $title : null;
        $external_link = isset($external_link) ?trim_video_link($resource_type,$external_link): null;
        $description = isset($description) ? $description : null;
        $id = (isset($id) && $id) ? $id : null;
        $files = (isset($files) && !empty($files)) ? $files : null;
        $files_id = (isset($files_id) && $files_id) ? $files_id : false;
        // $resource_level = isset($resource_level) ? $resource_level : null;
        if ($resource_type == 'WEBSITE' || $resource_type == 'READING') {
            $resource_level = '';
        }

        $resource_data = array(
            'type' => $resource_type,
            'title' => ucfirst($title),
            'link' => $external_link,
            'description' => $description,
        );

        $this->db->trans_start();

        if ($resource_type == 'AUDIO' && isset($files['audio']) && $files['audio']) {
            $audio = $this->upload_resource_audio($files, $files_id);
            if (isset($audio['file_id'])) {
                $resource_data['link'] = $audio['fileurl'];
                $resource_data['files_id'] = $audio['file_id'];
            }
        }

        $save_type = $id ? 'edit' : 'add';

        if ($id) {
            $this->db->update($this->tables['resources'], $resource_data, array('id' => $id));
        } else {
            $this->db->insert($this->tables['resources'], $resource_data);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $status = 'success';
        }

        return array('status' => $status, 'msg' => $this->lang->line("resourse_" . $save_type . "_" . $status));
    }

    /**
     * Upload audio on Media server
     */
    public function upload_resource_audio($files, $file_id = false)
    {
        $assets_config = $this->config->item('sftp_assets_audios');
        // $this->load->library('sftp');
        $file_type = strtolower(pathinfo(basename($files['audio']['name']), PATHINFO_EXTENSION));
        $file_name = pathinfo('audio', PATHINFO_FILENAME) . '-' . uniqid() . '.' . $file_type;

        $config = $this->config->item('sftp_details');

        $connection = ssh2_connect($config['hostname'], 22);
        if ($connection) {
            ssh2_auth_password($connection, $config['username'], $config['password']);
            if (ssh2_scp_send($connection, $files['audio']['tmp_name'], $assets_config['path'] . $file_name, 0644)) {

                $file_data = array('type' => $files['audio']['type'], 'name' => $files['audio']['name'], 'unique_name' => $file_name,
                    'size' => $files['audio']['size'], 'created_at' => date('Y-m-d H:i:s'), 'is_active' => '1');
                if ($file_id) {
                    $query = $this->db->where('id', $file_id)->limit(1)->get($this->tables['files']);
                    if ($query->num_rows() > 0) {
                        $file_detail = $query->row();
                        if ($file_detail->unique_name) {
                            ssh2_sftp_unlink(ssh2_sftp($connection), $assets_config['path'] . $file_detail->unique_name);
                        }
                        $this->db->update($this->tables['files'], $file_data, array('id' => $file_id));
                    }
                } else {
                    $this->db->insert($this->tables['files'], $file_data);
                    $file_id = $this->db->insert_id();
                }
            }

            ssh2_exec($connection, 'exit');
        }
        return $file_id ? array('file_id' => $file_id, 'fileurl' => $assets_config['url'] . $file_name) : array();
    }

    /**
     * get a resource list
     */

    public function get_resource($id = '')
    {
        $info_array = array('fields' => 'id,type,title,link,files_id,description,created_at');
        if ($id) {
            $info_array['where'] = array('id' => $id);
        }
        $info_array['table'] = $this->tables['resources'];
        $data = $this->db_model->get_data($info_array);
        return $data['result'];
    }
}

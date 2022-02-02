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

class Educational extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->load->model('Educational_model', 'education');
        $this->load->model('User_model', 'user');
        
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
        $this->template->javascript->add(base_url('assets/js/sweetalert2.min.js'));
        $this->template->stylesheet->add(base_url('assets/css/sweetalert2.css'));   
    }

    // Call list chapters function by default
    function index(){
        $this->list_chapters();
	}
	/**
	 * Get arm type in capital letters
	 */
	function get_arm($arm_type=''){
		return 'STUDY';
	}
	/**
	 * Text to be shown arm
	 */
	function get_arm_text($arm_type=''){
		return 'Study';
	} 

	/**
	 * Breadcrumbs for both arms
	 */
	function add_breadcrumb($arm_type,$title=''){
		$this->breadcrumbs->push($this->get_arm_text($arm_type).' Content', 'educational/list-chapters/'.strtolower($this->get_arm($arm_type)));
		$this->breadcrumbs->push($title, ' ');
	}

    function list_chapters($arm_type='control'){
		get_plugins_in_template('datatable');
		$arm_type = $this->get_arm($arm_type);
        $this->template->title = $this->get_arm_text($arm_type).' Content';
        $this->template->javascript->add(base_url('assets/js/bootstrap-switch.min.js'));
		$this->template->stylesheet->add(base_url('assets/css/bootstrap-switch.min.css'));
		
		$data = $this->education->get_content_list($this->input->get(), $arm_type);
        $data['arm_type']=$arm_type;
        
		$this->template->content->view('educational/list_chapters',$data);
		
        $this->template->publish();
    }

    function detail_chapter($arm_type='control',$content_id = '') {
        $arm_type = $this->get_arm($arm_type);
        
		$this->template->javascript->add(base_url('assets/js/tinymce/tinymce.min.js'));
		
		$this->template->title = $this->get_arm_text($arm_type).' Content';
		$this->add_breadcrumb($arm_type,'Add a chapter');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['arm_type'] = $arm_type;

        if($this->input->post()){
            $this->form_validation->set_rules($this->config->item("chapterContent"));
			if ($this->form_validation->run() != FALSE) {
    
                $slug_str = create_slug($this->input->post('chapter_title'));
                $result = $this->education->check_slug_availability($slug_str);
				$final_slug = $slug_str;
                if($result) {
                    $final_slug = $slug_str.'-'.$result;
                }
                $_POST['slug'] = $final_slug;
                $chapter_detail['data']         = $this->input->post();
                $chapter_detail['files']        = $_FILES;
                $chapter_detail['content_type'] = 'CONTENT';
                $chapter_detail['type']         = 'DETAIL_CHAPTER';

                $result = $this->education->save_content($chapter_detail,$this->get_arm($arm_type));

                if(array_key_exists("save",$this->input->post())){
                    redirect('/educational/list-chapters/'.strtolower($arm_type));
                } else {
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('/educational/detail-chapter/'.strtolower($arm_type));
                }
            }
        }

        $this->template->content->view('educational/detail_chapter', $data);
        $this->template->publish();
    }

    public function file_check_pdf($str='',$file_name){
        $error=file_check($file_name,'application/pdf');
        if(!empty($error)){
            $this->form_validation->set_message('file_check_pdf',  $error);
            return false; 
        }
        return true;
    }

    function detail_exercise($arm_type='control',$content_id = '') {

		if(!$content_id){
			$this->session->set_flashdata('error', 'No chapter selected');
            redirect('/educational/list-chapters/'.strtolower($arm_type));
		}
        $arm_type = $this->get_arm($arm_type);
        
		$this->template->javascript->add(base_url('assets/js/tinymce/tinymce.min.js'));
		
		$this->template->title = $this->get_arm_text($arm_type).' Content';
        $this->add_breadcrumb($arm_type,'Add a chapter');
        $data = $this->input->post();
        $data['breadcrumb'] = $this->breadcrumbs->show();  
        $data['arm_type'] = $arm_type;
        $data['content_id'] = $content_id;
        $file_uploaded=true;

        extract($this->input->post());
        // image_upload
        if($this->input->post()){
            if(array_key_exists("is_worksheet",$this->input->post()) && $is_worksheet=='on' ){
                $file_uploaded=false;
               
                $this->form_validation->set_rules('worksheet_file', 'File', 'callback_file_check_pdf[worksheet_file]');

                if ($this->form_validation->run()!=FALSE) 
                {
                    $file_uploaded=true;
                }

            }
            $this->form_validation->set_rules($this->config->item("exerciseContent"));
           
			if ($this->form_validation->run() != FALSE && $file_uploaded) {
				$requestData = $this->input->post();
                $requestData['files'] = $_FILES;
                $result = $this->education->save_exercise($requestData, $id=false);
				$this->session->set_flashdata($result['status'], $result['msg']);
				redirect('/educational/'.(array_key_exists("save",$this->input->post()) ? 'get-exercise-list' :'detail-exercise').'/'.strtolower($arm_type).'/'.$content_id); 
            }
        }

        $this->template->content->view('educational/detail_exercise', $data);
        $this->template->publish();
    }

    function edit_detail_exercise($arm_type='control',$exercise_id = ''){
        $arm_type = $this->get_arm($arm_type);
        if(empty($exercise_id)){
            $this->session->set_flashdata('200', $this->lang->line('chapter_content_not_exixt'));
            redirect('/educational/list-chapters/'.$arm_type);
        }

        $this->template->javascript->add(base_url('assets/js/tinymce/tinymce.min.js'));
		$this->template->title = '('.$this->get_arm_text($arm_type).') edit `chapter` content';
		$this->add_breadcrumb($arm_type,'Edit chapter content');

        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['arm_type'] = $arm_type;

        if(!empty($exercise_id)){
            $data['id'] = $exercise_id;
            $data['exercise_data'] = $this->education->get_content(array('table'=>'exercises','where' => array('exercises.id' => $exercise_id),'fields'=>'*'));
           
            $content_deta = $this->education->get_content(array('table'=>'exercise_item','where' => array('exercise_item.exercises_id' => $exercise_id)));

            $content_details = $this->education->get_exercise_details(array('arm' =>$arm_type, 'exercise_id' => $exercise_id))['exercise_items'];

            $content_id=null;
            $textarea_count = 1;

            if($data['exercise_data'] && !empty($data['exercise_data'])){
                $details_exercise=$data['exercise_data'][0];
                $exercise_data = array();
                $exercise_data['exercise_title'] =  $details_exercise['title'];
                $exercise_data['id']           =  $details_exercise['id'];
                $exercise_data['sub_header']         = $details_exercise['sub_header'];
                $exercise_data['description']  = $details_exercise['description'];
                $exercise_data['content_id']  =$content_id= $details_exercise['content_id'];
                $exercise_data['arm_type']=strtolower($arm_type);
                $exercise_data['worksheet_id'] = NULL;
                $exercise_data['is_worksheet'] =NULL;
                $exercise_data['worksheet_detail']=NULL;

                if( !empty($details_exercise['worksheet_id']) && $details_exercise['worksheet_id']) {
                    $exercise_data['worksheet_detail']=$this->education->get_content(array('table'=>'files','where' => array('files.id' =>$details_exercise['worksheet_id'])))[0];
                    $exercise_data['worksheet_id'] = $details_exercise['worksheet_id'];
                    $exercise_data['is_worksheet'] ='checked';
                    $exercise_data['worksheet_detail']['warn']='The uploaded worksheet will be erased permanently!';
                }
                    
            } else {
                $this->session->set_flashdata('200', $this->lang->line('chapter_content_not_exixt'));
                redirect('/educational/list-chapters/'.$arm_type);
            }

            $htm='';
            
            if(!empty($content_details)){
                foreach($content_details as $exerciseCounter=>$items){
                    switch($items['type']){
                        case "TEXT_ITEM":{
                            $htm.=create_dynamic_text_item($exerciseCounter,$items);
                            break;
                        }
                        case "RADIO":{
                            $htm.=create_dynamic_radio($exerciseCounter,$items); 
                            break;
                        }
                        case "CHECKBOX":{
                            $htm.=create_dynamic_checkbox($exerciseCounter,$items);
                            break;
                        }
                        case "TWO_COL":{
                            $htm.=create_dynamic_two_col($exerciseCounter,$items);
                        break;
                        }
                        case "RATING":{
                            $htm.=create_dynamic_rating($exerciseCounter,$items);
                            break;
                        }
                        case "GOAL":{
                            $htm.=create_dynamic_goal($exerciseCounter);
                            break;
                        }
                        case "GOAL_TRACKING":{
                            $htm.=create_dynamic_goal_tracking($exerciseCounter);
                            break;
                        }
                        default:{     
                            //Reseacher has not item
                        }
                    }
                }
            }
            $data['exercise_content'] = $exercise_data;    

            $data['exercise_items_html'] = $htm;
            $data['content_id']=$content_id;
        }
        if($this->input->post()){
            $file_uploaded=true;
            if($this->input->post()){
                extract($this->input->post());
              
                $requestData = $this->input->post();
                $requestData['files'] = $_FILES;
                
                if(array_key_exists("is_worksheet",$this->input->post()) && $is_worksheet=='on' &&  (isset($_FILES['worksheet_file']['name']) && $_FILES['worksheet_file']['name'] ||  empty($worksheet_id))   ){
                    $file_uploaded=false;
                    $this->form_validation->set_rules('worksheet_file', 'File', 'callback_file_check_pdf[worksheet_file]');
                
                    if ($this->form_validation->run()!=FALSE) 
                    {
                        $file_uploaded=true;
                    }
                }

                $this->form_validation->set_rules($this->config->item("exerciseContent"));
               
                if ($this->form_validation->run() != FALSE && $file_uploaded) {
                    $result = $this->education->save_exercise($requestData,$exercise_id,'Edit');
                    generate_log('Reseacher ['.$this->session->userdata('logged_in')->id.'] has edited exercise '.$exercise_id);
                    $this->session->set_flashdata($result['status'], $result['msg']);
                    redirect('/educational/get-exercise-list/'.strtolower($arm_type).'/'.$content_id);
                }
            }
        }
        $this->template->content->view('educational/edit_detail_exercise', $data);
        $this->template->publish();
    }

	function edit_detail_chapter($arm_type='control',$content_id = '') {
		$arm_type = $this->get_arm($arm_type);
        if(empty($content_id)){
            $this->session->set_flashdata('200', $this->lang->line('chapter_content_not_exixt'));
            redirect('/educational/list-chapters/'.$arm_type);
        }

        $this->template->javascript->add(base_url('assets/js/tinymce/tinymce.min.js'));
		$this->template->title = '('.$this->get_arm_text($arm_type).') edit `chapter` content';
		$this->add_breadcrumb($arm_type,'Edit chapter content');

        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['arm_type'] = $arm_type;
        if(!empty($content_id)){
            $data['content_id'] = $content_id;
            $data['content_data'] = $this->education->get_content(array('table'=>'content','where' => array('content.id' => $content_id),'fields'=>'*'));
            $content_details = $this->education->get_content(array('table'=>'content_details','where' => array('content_details.content_id' => $content_id)));
            $content_details_topic = $this->education->get_content(array('table'=>'content','where' => array('content.content_id' => $content_id),'fields'=>'*'));
            $htm            = '';
            $textarea_count = 1;
            $file_count     = 1;
            $topic_count    = 1;

            // p($data);die;
			if (!empty($content_details)) {
                foreach ($content_details as $key => $details) {
                    $file_data = array();
                    $paragraph_data = array();
                    if(!empty($details['text'])){
                        
                        $paragraph_data['name'] = 'custom_paragarph_edit'.$textarea_count; 
                        $paragraph_data['text'] =  $details['text'];
                        $paragraph_data['id']   =  $details['id'];
                        $textarea_count++;
                        $htm .= create_dynamic_textarea($paragraph_data);
                    }
                    if($details['image_id'] != NULL){
                        $file_data['hidden_image_name']    = 'image_hidden_edit'.$file_count;
                        $file_data['name_and_value']       = 'image_edit'.$file_count;
                        $file_data['image_credit_name_id'] = 'image_credit_edit'.$file_count;
                        $file_data['image_credit_value']   =  $details['image_credit'];
                        $file_data['image_id']             =  $details['image_id'];
                        $file_data['id']                   =  $details['id'];
                        $file_data['hidden_image_id']      =  'hidden_image_edit'.$file_count;
                        $file_count++;
                        $htm .= create_dynamic_fileupload($file_data);
                    }
                }
            }
            if(!empty($content_details_topic)){
                foreach ($content_details_topic as $key => $details_topic) { 
                    $topic_data = array();
                    $topic_data['content_name'] =  $details_topic['content_name'];
                    $topic_data['id']           =  $details_topic['id'];
                    $topic_data['name']         = 'topic_title_edit'.$topic_count;
                    $topic_data['hidden_name']  = 'hidden_topic_title_edit'.$topic_count;
					$topic_data['arm_type']=strtolower($arm_type);
                    $topic_count++;
                    $htm .= create_dynamic_topic($topic_data);
                }
            }
           
            $data['htm'] = $htm;
        }
        if($this->input->post()){
            $this->form_validation->set_rules($this->config->item("chapterContent"));
			if ($this->form_validation->run() != FALSE) {

                $slug_str = create_slug($this->input->post('chapter_title'));
                $result = $this->education->check_slug_availability($slug_str);

                if(!$result) {
                    $final_slug = $slug_str;
                    $_POST['slug'] = $final_slug;
                } 
                $chapter_detail['data']         = $this->input->post();
                $chapter_detail['files']        = $_FILES;
                $chapter_detail['content_type'] = 'CONTENT';
                $chapter_detail['type']         = 'EDIT_DETAIL_CHAPTER';
                $result = $this->education->save_content($chapter_detail,$this->get_arm($arm_type));


                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('/educational/list-chapters/'.strtolower($arm_type));
            }   
        }
        
        $this->template->content->view('educational/edit_detail_chapter', $data);
        $this->template->publish();
    }

    public function get_content_data() {
        $data = $this->education->get_content_list($this->input->get());
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart');
        
        foreach ($data['result'] as $val) {
            $li = '';
            if(isset($val['is_topic'])){
                $method = 'edit-topic-content';
            } else if (isset($val['is_sub_topic'])){
                $method = 'edit-sub-topic-content';
            } else {
                $method = 'edit_detail_chapter';
                $flow = '';
            }
            $link = '<a id="edit" href="' . base_url('educational/'.$method.'/' . $val['id'] .'/'.$flow) . '" class="btn btn-tertiary btn-sm" data-toggle="tooltip" data-placement="left" title="Edit Content">Edit</a>';

            if(isset($val['is_sub_topic']) || isset($val['is_chapter'])){

                $link .= '<a id="resource" href="' . base_url('educational/get-side-resources/' . $val['id']) . '" class="btn btn-tertiary  btn-sm pointer" data-placement="left" title="Assign Resource">Resources</a>';
            }
            if($val['status'] == 'UNPUBLISHED'){
                $li .= '<li><a href="' . base_url('educational/edit-status/' . $val['id'] .'/PUBLISHED') . '">PUBLISHED</a></li>';
                $li .= '<li><a href="' . base_url('educational/edit-status/' . $val['id'] .'/DRAFT') . '">DRAFT</a></li>';
            } else if ($val['status'] == 'PUBLISHED') {
                $li .= '<li><a href="' . base_url('educational/edit-status/' . $val['id'] .'/UNPUBLISHED') . '">UNPUBLISHED</a></li>';
                $li .= '<li><a href="' . base_url('educational/edit-status/' . $val['id'] .'/DRAFT') . '">DRAFT</a></li>';
            } else if ($val['status'] == 'DRAFT') {
                $li .= '<li><a href="' . base_url('educational/edit-status/' . $val['id'] .'/UNPUBLISHED') . '">UNPUBLISHED</a></li>';
                $li .= '<li><a href="' . base_url('educational/edit-status/' . $val['id'] .'/PUBLISHED') . '">PUBLISHED</a></li>';
            }
            $link .= '<div class="dropdown d-inline">
                        <button class="btn btn-tertiary  dropdown-toggle" type="button" data-toggle="dropdown" title="Change Status">Status
                        <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            '.$li.'
                        </ul>
                    </div>';
            $link .= '<a class="pointer" onclick="return confirmBox(\'Do you want to delete it ?\',\''.base_url("educational/delete-content/" . $val["id"]).'\');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>';
        
            if(isset($val['is_chapter'])){
                $count = ++$i;
                $j =  0;
            } else if(isset($val['is_topic'])) {
                $count = $i .'.'. ++$j;
                $k = 0;
            } else if(isset($val['is_sub_topic'])) {
                $count = $i .'.'. $j.'.'. ++$k;
            } else {
                $count = '';
            }
            $output['aaData'][] = array(
                "DT_RowId" => $val['id'],
                $count,
                $val['content_name'],
                ucfirst(strtolower($val['status'])),
                $link
            );
        }
        echo json_encode($output);
        die;
    }
    

    public function edit_topic_content($arm_type='control',$content_id_topic = '') {
		$arm_type = $this->get_arm($arm_type);
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->template->javascript->add(base_url('assets/js/tinymce/tinymce.min.js'));
        // $this->breadcrumbs->push('Educational Content', 'educational');
		// $this->breadcrumbs->push('Edit topic content', ' ');
		
		$this->template->title = '('.$this->get_arm_text($arm_type).') edit topic content';
		$this->add_breadcrumb($arm_type,'Edit topic content');

        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['arm_type'] = $arm_type;

        if(!empty($content_id_topic)){
            
            $data['content_data_topic'] = $this->education->get_content(array('table'=>'content','where' => array('content.id' => $content_id_topic),'fields'=>'*'));
            $data['sub_heading'] = ucfirst(get_content_name($data['content_data_topic'][0]['content_id']));
            $content_details = $this->education->get_content(array('table'=>'content_details','where' => array('content_details.content_id' => $content_id_topic)));
            $content_details_sub_topic = $this->education->get_content(array('table'=>'content','where' => array('content.content_id' => $content_id_topic),'fields'=>'*'));
            $htm = '';
            $textarea_count  = 1;
            $file_count      = 1;
            $sub_topic_count = 1;
            $resource_count  = 1;
            if (!empty($content_details)) {
                $is_edit = TRUE;
                foreach ($content_details as $key => $details) {
                    $file_data      = array();
                    $paragraph_data = array();
                    $resource_data  = array();
                    if(!empty($details['text'])){
                        
                        $paragraph_data['name'] = 'custom_paragarph_edit'.$textarea_count;
                        $paragraph_data['text'] =  $details['text'];
                        $paragraph_data['id']   =  $details['id'];
                        $textarea_count++;
                        $htm .= create_dynamic_textarea($paragraph_data);
                    }
                    if($details['image_id'] != NULL){
                        $file_data['hidden_image_name']    = 'image_hidden_edit'.$file_count;
                        $file_data['name_and_value']       = 'image_edit'.$file_count;
                        $file_data['image_credit_name_id'] = 'image_credit_edit'.$file_count;
                        $file_data['image_credit_value']   =  $details['image_credit'];
                        $file_data['image_id']             =  $details['image_id'];
                        $file_data['id']                   =  $details['id'];
                        $file_data['hidden_image_id']      =  'hidden_image_edit'.$file_count;
                        $file_count++;
                        $htm .= create_dynamic_fileupload($file_data);

                    }
                    if($details['resources_id'] != NULL){
                        $resource_data['resource_id_name_class']  = 'resource_edit'.$resource_count;
                        $resource_data['resources_id']            =  $details['resources_id'];
                        $resource_count++;
                        $htm .= create_dynamic_resource($resource_data);
                    }
                }
                if(!empty($content_details_sub_topic)){
                    foreach ($content_details_sub_topic as $key => $details_sub_topic) {
                        $sub_topic_data = array();
                        $sub_topic_data['content_name'] =  $details_sub_topic['content_name'];
                        $sub_topic_data['id']           =  $details_sub_topic['id'];
                        $sub_topic_data['name']         = 'sub_topic_title_edit'.$sub_topic_count;
                        $sub_topic_data['hidden_name']  = 'hidden_sub_topic_title_edit'.$sub_topic_count;
						$sub_topic_data['arm_type']=strtolower($arm_type);
                        $sub_topic_count++;
                        $htm .= create_dynamic_sub_topic($sub_topic_data);

                    }
                }
                $data['htm'] = $htm;
            } else {
                $is_edit = FALSE;
            }
		}
		if($this->input->post()){
            $slug_str = create_slug($this->input->post('topic_title'));
            $result = $this->education->check_slug_availability($slug_str);
            if($is_edit){
                $this->form_validation->set_rules($this->config->item("edit_topic_content"));
                $topic_content['type'] = 'EDIT_TOPIC_CONTENT';
                
                if(!$result) {
                    $final_slug = $slug_str;
                    $_POST['slug'] = $final_slug;
                } 
            }else{
                $this->form_validation->set_rules($this->config->item("add_topic_content"));
                $topic_content['type'] = 'ADD_TOPIC_CONTENT';

                if($result) {
                    $final_slug = $slug_str.'-'.$result;
                } else {
                    $final_slug = $slug_str;
                }
                $_POST['slug'] = $final_slug; 
            }
			if ($this->form_validation->run() != FALSE) {
                
				$topic_content['data']         = $this->input->post();
                $topic_content['content_type'] = 'TOPIC';
                
				if(!empty($content_id_topic)){
					$topic_content['content_id'] = $content_id_topic;
				}
                $result = $this->education->save_content($topic_content,$this->get_arm($arm_type));
                $this->session->set_flashdata($result['status'], $result['msg']);
				redirect('/educational/list-chapters/'.strtolower($arm_type));	
            }
        }
        if($is_edit){
            $this->template->content->view('educational/edit_topic_content', $data);
        } else {
            $this->template->content->view('educational/add_topic_content', $data);
        }
        $this->template->publish();
    }

    public function edit_sub_topic_content($arm_type='control',$content_id_sub_topic = '') {
		$arm_type=$this->get_arm($arm_type);
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->template->javascript->add(base_url('assets/js/tinymce/tinymce.min.js'));
        // $this->breadcrumbs->push('Educational Content', 'educational');
		// $this->breadcrumbs->push('Edit sub-topic content', ' ');
		$this->template->title = '('.$this->get_arm_text($arm_type).') edit sub topic content';
		$this->add_breadcrumb($arm_type,'Edit sub-topic content');
        $data['breadcrumb'] = $this->breadcrumbs->show();
		$data['arm_type'] = $arm_type;

        if(!empty($content_id_sub_topic)){
            $data['content_data_sub_topic'] = $this->education->get_content(array('table'=>'content','where' => array('content.id' => $content_id_sub_topic),'fields'=>'*'));
            $data['sub_heading'] = ucfirst(get_content_name($data['content_data_sub_topic'][0]['content_id']));
            $content_details = $this->education->get_content(array('table'=>'content_details','where' => array('content_details.content_id' => $content_id_sub_topic)));
            $htm = '';
            $textarea_count=1;
            $file_count=1;
            $sub_topic_count=1;
            $resource_count=1;
            if (!empty($content_details)) {
                $is_edit = TRUE;
                foreach ($content_details as $key => $details) {
                    $file_data      = array();
                    $paragraph_data = array();
                    $resource_data  = array();
                    if(!empty($details['text'])){
                        
                        $paragraph_data['name'] = 'custom_paragarph_edit'.$textarea_count;
                        $paragraph_data['text'] =  $details['text'];
                        $paragraph_data['id']   =  $details['id'];
                        $textarea_count++;
                        $htm .= create_dynamic_textarea($paragraph_data);

                    }
                    if($details['image_id'] != NULL){
                        $file_data['hidden_image_name']    = 'image_hidden_edit'.$file_count;
                        $file_data['name_and_value']       = 'image_edit'.$file_count;
                        $file_data['image_credit_name_id'] = 'image_credit_edit'.$file_count;
                        $file_data['image_credit_value']   =  $details['image_credit'];
                        $file_data['image_id']             =  $details['image_id'];
                        $file_data['id']                   =  $details['id'];
                        $file_data['hidden_image_id']      =  'hidden_image_edit'.$file_count;
                        $file_count++;
                        $htm .= create_dynamic_fileupload($file_data);
                    }
                    if($details['resources_id'] != NULL){
                        $resource_data['resource_id_name_class']  = 'resource_edit'.$resource_count;
                        $resource_data['resources_id']            = $details['resources_id'];
                        $resource_count++;
                        $htm .= create_dynamic_resource($resource_data);
                    }
                }
                $data['htm'] = $htm;
            } else {
                $is_edit = FALSE;
            }
		}
		if($this->input->post()){
            $slug_str = create_slug($this->input->post('sub_topic_title'));
            $result = $this->education->check_slug_availability($slug_str);
            if($is_edit){
                $this->form_validation->set_rules($this->config->item("edit_sub_topic_content"));
                $sub_topic_content['type'] = 'EDIT_SUBTOPIC_CONTENT';

                if(!$result) {
                    $final_slug = $slug_str;
                    $_POST['slug'] = $final_slug;
                }
            } else {
                $this->form_validation->set_rules($this->config->item("add_sub_topic_content"));
                $sub_topic_content['type'] = 'ADD_SUBTOPIC_CONTENT';

                if($result) {
                    $final_slug = $slug_str.'-'.$result;
                } else {
                    $final_slug = $slug_str;
                }
                $_POST['slug'] = $final_slug; 
            }
			if ($this->form_validation->run() != FALSE) {
   
				$sub_topic_content['data'] = $this->input->post();
                $sub_topic_content['content_type'] = 'SUBTOPIC';
                
				if(!empty($content_id_sub_topic)){
					$sub_topic_content['content_id'] = $content_id_sub_topic;
				}
                $result = $this->education->save_content($sub_topic_content);
                $this->session->set_flashdata($result['status'], $result['msg']);
				redirect('/educational/list-chapters/'.strtolower($arm_type));	
            }
        }
        if($is_edit){
            $this->template->content->view('educational/edit_sub_topic_content', $data);
        } else {
            $this->template->content->view('educational/add_sub_topic_content', $data);
        }
        $this->template->publish();
    }

    public function delete_content($arm_type='control',$content_id = '') {
        if (isset($content_id) && $content_id) {
            $result = $this->education->delete_content($content_id);
            $this->session->set_flashdata($result['status'], $result['msg']);
            redirect('/educational/list-chapters/'.strtolower($arm_type));
        }
    }
    public function delete_exercise($arm_type='control',$content_id,$exercise_id = '') {
        if (isset($exercise_id) && $exercise_id) {
            $result = $this->education->delete_exercise($exercise_id);
            $this->session->set_flashdata($result['status'], $result['msg']);
            redirect('/educational/get_exercise_list/'.strtolower($arm_type).'/'.$content_id);
        }
    }

    
    public function get_exercise_list($arm_type='control',$id='') {
		if(!$id || $arm_type=='control'){
			$this->session->set_flashdata('error', 'No Exercise exist!');
            redirect('/educational/list-chapters/'.strtolower($arm_type));
		} 
        get_plugins_in_template('datatable');
		$arm_type = $this->get_arm($arm_type);
        $this->template->title = 'Exercises in'.$this->get_arm_text($arm_type).' content';
        $this->template->javascript->add(base_url('assets/js/bootstrap-switch.min.js'));
		$this->template->stylesheet->add(base_url('assets/css/bootstrap-switch.min.css'));
		
		$data = $this->education->get_exercise_list($this->input->get(), $arm_type,$id);

		$data['arm_type']=$arm_type;
		$data['content_id']=$id;
		$this->template->content->view('educational/list_exercises',$data);
		
        $this->template->publish(); 
    }
    
    public function get_resource_list() {
        $data = $this->education->get_resource_list($this->input->post());
        if (!empty($data)) {
            echo json_encode($data);
        } else {
            echo 0;
        }
    }

    public function assign_resource()
    {
        // print_r($this->input->post());die();
        $data = $this->education->assign_resource_to_content($this->input->post());
        if ($data == 'EXIST') {
            echo 0;
        } else if($data == 'NOTINSERTED') {
            echo 2; 
        } else {
            echo 1;
        }
    }

    function get_side_resources($content_id = ''){
        get_plugins_in_template('datatable');
        $this->template->title = 'Sidebar Resources';
        $this->breadcrumbs->push('Resources', 'resources');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        $data['sub_heading'] = 'List of resources in sidebar';
        if(!empty($content_id)){
            $data['content_id'] = $content_id;
            // print_r($data['content_id']);die();
			$content = $this->education->get_content(array('table'=>'content','where' => array('content.id' => $content_id)));
            $content = isset($content[0]['type']) ? $content[0] : array();
            // print_r($content);die();
			$data['content_data'] = $this->education->get_content_details(array('field'=>'id','param'=>$content_id, 'is_sub_topic'=>$content['type']=='SUBTOPIC','arm'=>$content['arm']));
			
			$data['side_resources']=$data['content_data']['side_resources'];
			$data['side_resources']['reading'] = isset($data['side_resources']['reading']) ? $data['side_resources']['reading'] : array();
			$data['side_resources']['video'] = isset($data['side_resources']['video']) ? $data['side_resources']['video'] : array();
			$data['side_resources']['audio'] = isset($data['side_resources']['audio']) ? $data['side_resources']['audio'] : array();
			$data['side_resources']['website'] = isset($data['side_resources']['website']) ? $data['side_resources']['website'] : array();
			
        }
    
        $this->template->content->view('educational/sidebar_resource_list', $data);
        $this->template->publish();
    }

    public function delete_cources_has_res($resource_id = '',$content_id = '') {
        
        if (isset($resource_id) && isset($content_id)) {
            $result = $this->education->delete_content_has_resource($resource_id,$content_id);
            $this->session->set_flashdata($result['status'], $result['msg']);
            redirect('/educational/get-side-resources/'.$content_id);
        }
    }

    public function reorder_position_resources()
    {
        $data = $this->education->reorder_content_has_resources($this->input->post());
        if ($data == 'EXIST') {
            echo 0;
        } else if($data == 'NOTINSERTED') {
            echo 2; 
        } else {
            echo 1;
        }
    }

    public function get_side_resources_data($content_id,$type) {
        $data = $this->education->get_side_resources_list($this->input->get(),$content_id,$type);
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
        $i = $this->input->get('iDisplayStart') + 1;
        foreach ($data['result'] as $val) {
            
            $link = '';
            $link .= '<a class="pointer" onclick="return confirmBox(\'Do you want to delete it ?\',\''.base_url("resources/delete-resources/" . $val["id"]).'\');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete"><i class="fa fa-trash"></i></a>';
            $output['aaData'][] = array(
                "DT_RowId" => $val['id'],
                $i++,
                ucfirst($val['title']),
                $link
            );
        }
        echo json_encode($output);
        die;
    }

    public function reorder_position_chapters()
    {
        $data = $this->education->reorder_content($this->input->post());
    }
}

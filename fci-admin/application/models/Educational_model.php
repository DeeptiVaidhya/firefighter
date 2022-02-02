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
 * Name:    Educational Model
 * Requirements: PHP5 or above
 */
class Educational_model extends CI_Model
{

    public $tables = array();

    public function __construct()
    {
        parent::__construct();
        $this->tables = array('content' => 'content', 'content_details' => 'content_details', 'content_has_resources' => 'content_has_resources', 'files' => 'files', 'resources' => 'resources', 'week_info' => 'week_info','exercises'=>'exercises','exercise_item'=>'exercise_item','exercise_item_details'=>'exercise_item_details','goals'=>'goals','users_has_exercise_item'=>'users_has_exercise_item');
        $this->load->model('Questionnaire_model', 'questionnaire');
    }

    // public function get_user_favorite($user_id = '')
    // {
    //     $response = array('msg' => '', 'status' => 'error');
    //     if (!$user_id) {
    //         $response['msg'] = $this->lang->line('invalid_user');
    //         return $response;
    //     }

    //     $info_array = array('fields' => 'c.id,c.slug,c.content_id,c.content_name', 'table' => 'content c', 'where' => "f.users_id='" . $user_id . "'");

    //     $info_array['join'] = array(
    //         array('table' => $this->tables['favorite'] . ' f', 'on' => 'c.id = f.content_id', 'type' => 'INNER'),
    //     );

    //     $data = $this->db_model->get_data($info_array);
    //     $subtopic = $data['result'];
    //     $all_chapters = array();
    //     $assets_config = $this->config->item('assets_images');
    
    //     return array('chapters' => array_values($all_chapters), 'status' => 'success', 'msg' => '');
    // }

    // public function check_content_added_in_favorite($content_id = '', $user_id = '')
    // {
    //     return $this->db->where(array('users_id' => $user_id, 'content_id' => $content_id))->get($this->tables['favorite'])->num_rows() > 0;
    // }
    // /**
    //  * Add/Remove sub content in favorite for a user
    //  */
    // public function update_favorite($params)
    // {
    //     extract($params);
    //     $response = array('msg' => $this->lang->line('favorite_data_saving_error'), 'status' => 'error');
    //     $user_id = (isset($user_id) && $user_id) ? $user_id : false;
    //     $is_added = isset($is_added) ? $is_added : false;
    //     $content_id = (isset($content_id) && $content_id) ? $content_id : false;
    //     if (!$user_id) {
    //         $response['msg'] = $this->lang->line('invalid_user');
    //         return $response;
    //     }
    //     if (!$content_id) {
    //         $response['msg'] = $this->lang->line('invalid_content');
    //         return $response;
    //     }

    //     $this->db->trans_start();
    //     $data = array('users_id' => $user_id, 'content_id' => $content_id);
    //     if ($is_added) { // check for existance
    //         $this->db->delete($this->tables['favorite'], $data);
    //     } else {
    //         $data['created_at'] = date('Y-m-d H:i:s');
    //         $this->db->insert($this->tables['favorite'], $data);
    //     }

    //     $this->db->trans_complete();

    //     if ($this->db->trans_status() !== false) {
    //         $msg = $is_added ? $this->lang->line('content_removed_favorite') : $this->lang->line('content_added_favorite');
    //         $status = 'success';
    //         generate_log("User[$user_id] " . ($is_added ? " removed content[$content_id] from favorites" : "added content[$content_id] in favorite"));
    //     }
    //     return array('status' => $status, 'msg' => $msg);

    // }

	/**
	 * Adding/Updating excercise and its item for a chapter
	 */
    public function save_exercise($params = array(),$id,$type='Add'){ 
        $status = 'error';
        $msg = $this->lang->line('save_content_error');
        extract($params);
        $content_id = isset($content_id) ? $content_id : false;
        $id = isset($id) ? $id : false;
        $exercise_title = isset($exercise_title) ? $exercise_title : false;
        $sub_header = isset($sub_header) ? $sub_header : false;
        $description = isset($description) ? $description : false;
        $worksheet_id = isset($worksheet_id) ? $worksheet_id : null;
        $unique_items = isset($items) ?  $items : null;

        $contentDetailLastInserted = '';
        $content_last_inserted = '';

        if($type!='Edit'){
            $find_height = array('table' => 'exercises', 'column' =>'exercises.position','order_by'=>'exercises.position','where'=>array('content_id'=>$content_id));
            $position = get_highest_last_value($find_height) + 1;
    
            $exercise_data = array('title' => $exercise_title, 'sub_header' =>$sub_header, 'description' => $description,'content_id '=>$content_id,'position'=>$position );
        }
        else{
            $exercise_data = array('title' => $exercise_title, 'sub_header' =>$sub_header, 'description' => $description,'content_id '=>$content_id);
        }
        

        /**
         * Below code work in 'files' table
         */
        if(isset($is_worksheet) && $is_worksheet && isset($files['worksheet_file']['name']) && !empty($files['worksheet_file']['name']) ){
            $exercise_data['worksheet_id']=$this->db_model->upload_document($files, 'worksheet_file', 'pdf', $worksheet_id); //upload image
        }else if(isset($is_worksheet) && $is_worksheet){
            $exercise_data['worksheet_id']=$worksheet_id;
        }else{
            $exercise_data['worksheet_id']=null;
            $this->db_model->delete_document('pdf', $worksheet_id);
        }
        
      
        /**
         * Below code work in 'exercises' table
         */
		$this->db->trans_start();
		if($id) {
			$this->db->where('id',$id);
            $this->db->update($this->tables['exercises'], $exercise_data);
		} else {
			$exercise_data['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert($this->tables['exercises'], $exercise_data);
            $id = $this->db->insert_id();
            generate_log('Reseacher ['.$this->session->userdata('logged_in')->id.'] has added exercise '.$id);
        }
    
        /**
         * Below code work in 'exercises_item' table
         */

        // p($params);p($unique_items);die;
        if($type=='Edit'){
            $where = array('exercise_item.exercises_id' => $id);
            $exercises_ids= $this->get_content(array('table' => 'exercise_item', 'where' => $where, 'fields' => 'id'));
            $exercises_ids=array_column($exercises_ids,'id');

            foreach ($exercises_ids as $ex_id) {
                $info_array = array();
                $info_array['where'] = array('exercise_item_details.exercise_item_id' => $ex_id );
                $info_array['table'] = $this->tables['exercise_item_details'];
                $this->db_model->delete_data($info_array);
            }

            //delete exercise_item table content
            $this->db_model->delete_data(array('table'=>'exercise_item','where'=>array('exercise_item.exercises_id' => $id )));
            //delete users_has_exercise_item table content
            $this->db_model->delete_data(array('table'=>'users_has_exercise_item','where'=>array('users_has_exercise_item.exercises_id' => $id )));
        }

        $text_item_sno=0;
        $radio_sno=0;
        $checkbox_sno=0;
        $two_col_sno=0;
        $rating_sno=0;

        foreach($unique_items as $key_item=>$type_item){ 
            $position_items = $this->height_exercise_items($id);
            $exercise_items=array();

            switch ($type_item) {
                case 'TEXT_ITEM':{
                    $position_items+=1; 
                    $exercise_items=array('type'=>$type_item,
                        'primary_prompt'=>$primary_prompt[$text_item_sno],
                        'secondary_prompt'=>$secondary_prompt[$text_item_sno],
                        'text_field_size'=>$text_field_size[$text_item_sno],
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));
                  
                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $text_item_sno+=1;
                    break;
                }

                case 'RADIO':{
                    $position_items+=1; 

                    $exercise_items=array('type'=>$type_item,
                        'primary_prompt'=>$primary_prompt_rd[$radio_sno],
                        'secondary_prompt'=>$secondary_prompt_rd[$radio_sno],
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));

                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $last_id = $this->db->insert_id();

                    if(!empty($option_text_rd)) {
                        $this->save_exercise_option($option_text_rd,$key_item,$last_id);
                    }
                    $radio_sno+=1;
                    break;
                }
                case 'CHECKBOX':{
                    $position_items+=1; 

                    $exercise_items=array('type'=>$type_item,
                        'primary_prompt'=>$primary_prompt_cb[$checkbox_sno],
                        'secondary_prompt'=>$secondary_prompt_cb[$checkbox_sno],
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));

                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $last_id = $this->db->insert_id();

                    if(!empty($option_text_cb)) {
                        $this->save_exercise_option($option_text_cb,$key_item,$last_id);
                    }
                    $checkbox_sno+=1;
                    break;
                }
                case 'TWO_COL':{
                    $position_items+=1; 

                    $exercise_items=array('type'=>$type_item,
                        'primary_prompt'=>$primary_prompt_col[$two_col_sno],
                        'first_heading'=>$first_head_col[$two_col_sno],
                        'second_heading'=>$second_head_col[$two_col_sno],
                        'number_of_items'=>$number_repeat_col[$two_col_sno],
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));

                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $two_col_sno+=1;
                    break;
                }
                case 'RATING':{
                    $position_items+=1; 
                    
                    $exercise_items=array('type'=>$type_item,
                        'first_heading'=>$first_head_rate[$rating_sno],
                        'second_heading'=>$second_head_rate[$rating_sno],
                        'number_of_items'=>$number_other_rate[$rating_sno],
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));


                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $last_id = $this->db->insert_id();

                    if(!empty($option_text_rate)) {
                        $this->save_exercise_option($option_text_rate,$key_item,$last_id); 
                    }
                    $rating_sno+=1;

                    break;
                }
                case 'GOAL':{
                    $position_items+=1; 
                    
                    $exercise_items=array('type'=>$type_item,
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));

                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $last_id = $this->db->insert_id();
                    break;
                }
                case 'GOAL_TRACKING':{
                    $position_items+=1; 
                    
                    $exercise_items=array('type'=>$type_item,
                        'exercises_id'=>$id,
                        'position'=>$position_items, 
                        'created_at'=>date('Y-m-d H:i:s'));

                    $this->db->insert($this->tables['exercise_item'], $exercise_items);
                    $last_id = $this->db->insert_id();
                    break;
                }
                default:
                    return array('status' => 'error', 'msg' => 'Error while saving exercise data');
            }
        }        
		$this->db->trans_complete();
		if ($this->db->trans_status() !== false) {
			return array('status' => 'success', 'msg' => 'Exercise data saved successfully');
        }
        return array('status' => 'error', 'msg' => 'Error while saving exercise data');
    }

    public function save_exercise_option($types_option,$key,$id){
        if(isset($types_option) && $types_option) { 
            // $option_array=array_values($types_option);
            $option_array=$types_option;

            if(isset($option_array[$key]) && !empty($option_array[$key])){
                foreach ($option_array[$key] as $key => $option) {
                    $exercise_item_detail=array(
                        'title'=>$option,
                        'exercise_item_id'=>$id
                    );
                    $this->db->insert($this->tables['exercise_item_details'], $exercise_item_detail);
                }
            }
        }
    }

    public function height_exercise_items($id){
        $find_height_items = array('table' => 'exercise_item', 'column' =>'exercise_item.position','order_by'=>'exercise_item.position','where'=>array('exercises_id'=>$id));

        return get_highest_last_value($find_height_items);
    }


    public function save_content($params = array(), $arm = "CONTROL")
    {
        $status = 'error';
        $msg = $this->lang->line('save_content_error');
        extract($params);
        $type = (isset($type)) ? $type : false;
        $content_id = (isset($content_id)) ? $content_id : false;
        $chapter_name = (isset($chapter_name)) ? $chapter_name : false;
        $chapter_icon = (isset($chapter_icon)) ? $chapter_icon : false;
        $content_type = (isset($content_type)) ? $content_type : false;
        $contentDetailLastInserted = '';
        $content_last_inserted = '';

        if ($content_type == 'CONTENT') {
            if ($type == 'ADD_CHAPTER') {
                if ($content_id) {
                    $dataUpdateContentById = array('content_name' => $chapter_name, 'icon_class' => $chapter_icon);
                    $this->db->update($this->tables['content'], $dataUpdateContentById, array('id' => $content_id));
                    $status = 'success';
                    $msg = $this->lang->line('chapter_updated');
                    $id = $content_id;
                } else {
                    $this->db->insert($this->tables['content'], array('arm' => $arm, 'type' => $content_type, 'content_name' => $chapter_name, 'icon_class' => $chapter_icon, 'created_at' => date('Y-m-d H:i:s')));
                    $status = 'success';
                    $msg = $this->lang->line('chapter_name_added');
                    $id = $this->db->insert_id($this->tables['content']);
                }
            }
            if ($type == 'DETAIL_CHAPTER') { 
                if (!empty($data)) {
                    $counter = 1;
                    foreach ($data as $key => $value) {
                        if ($key == 'chapter_title') {
                            $position = get_highest_last_value() + 1;
                            $this->db->insert($this->tables['content'], array('arm' => $arm, 'type' => $content_type, 'content_name' => $value, 'title' => $value, 'slug' => $data['slug'], 'created_at' => date('Y-m-d H:i:s'), 'position' => $position ));
                            $content_last_inserted = $this->db->insert_id($this->tables['content']);

                        } else if ($key == 'chapter_icon_hid' && $content_last_inserted != '') {

                            $file_name = !empty($files[$value]['name']) ? image_upload($value) : '';
                            if (!empty($file_name)) {
                                $dataUpdateContent = array('icon_class' => $file_name);
                                $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $content_last_inserted));
                            }
                        } else if ($key == 'first_paragragh' && $content_last_inserted != '') {
                            $dataUpdateContent = array('intro_text' => $value);
                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $content_last_inserted));
                        } else {
                            if (\strpos($key, 'image_hidden') !== false) {
                                $file_id = !empty($files[$value]['name']) ? $this->db_model->upload_document($files, $value, 'images', '') : '';
                                if (!empty($file_id)) {
                                    $this->db->insert($this->tables['content_details'], array('content_id' => $content_last_inserted, 'image_id' => $file_id, 'position' => $counter));
                                    $contentDetailLastInserted = $this->db->insert_id($this->tables['content_details']);
                                }
                            } else if (\strpos($key, 'image_credit') !== false && $contentDetailLastInserted != '') {

                                $dataUpdate = array('image_credit' => trim($value));
                                $this->db->update($this->tables['content_details'], $dataUpdate, array('id' => $contentDetailLastInserted));
                            } else if (\strpos($key, 'topic_title') !== false) {
                                if (\strpos($key, 'hidden_topic_title') !== false) {
                                    continue;
                                }
                                $this->db->insert($this->tables['content'], array('arm' => $arm, 'type' => 'TOPIC', 'content_name' => $value, 'created_at' => date('Y-m-d H:i:s'), 'content_id' => $content_last_inserted));
                            } else if ($key == 'click_to_add' || $key == 'save' || $key == 'save_another' || $key == 'slug') {
                                continue;
                            } else {
                                $this->db->insert($this->tables['content_details'], array('text' => $value, 'content_id' => $content_last_inserted, 'position' => $counter));
                            }
                            $counter++;
                        }
                    }
                    $status = 'success';
                    $msg = $this->lang->line('chapter_detail_added');
                    $id = '';
                }
            }
            if ($type == 'EDIT_DETAIL_CHAPTER') {
                if (!empty($data)) {
                    $info_array = array();
                    $info_array['where'] = array('content_details.content_id' => $data['content_id']);
                    $info_array['table'] = $this->tables['content_details'];
                    $data_del = $this->db_model->delete_data($info_array);
                    $counter = 1;
                    foreach ($data as $key => $value) {
                        if ($key == 'chapter_title') {
                            $dataUpdateContent = array('content_name' => $value, 'title' => $value);
                            if (isset($data['slug'])) {
                                $dataUpdateContent['slug'] = $data['slug'];
                            }
                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $data['content_id']));
                        } else if ($key == 'chapter_icon_hid') {

                            $file_name = !empty($files[$value]['name']) ? image_upload($value) : '';
                            if (!empty($file_name)) {
                                $assets_config = $this->config->item('assets_images');
                                if (!empty($file_name)) {
                                    // get image data icon to unlink
                                    $get_icon_file_details['fields'] = 'icon_class';
                                    $get_icon_file_details['where'] = array('id' => $data['content_id']);
                                    $get_icon_file_details['table'] = $this->tables['content'];
                                    $get_icon_file_details = $this->db_model->get_data($get_icon_file_details);

                                    $dataUpdateContent = array('icon_class' => $file_name);
                                    $update_image_data = $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $data['content_id']));
                                    if ($update_image_data) {

                                        $image_path = $assets_config['path'] . '/' . $get_icon_file_details['result'][0]['icon_class'];
                                        unlink($image_path);
                                    }
                                }
                            }
                        } else if ($key == 'first_paragragh') {
                            $dataUpdateContent = array('intro_text' => $value);
                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $data['content_id']));
                        } else {
                            if (\strpos($key, 'image_hidden') !== false) {
                                $file_id = !empty($files[$value]['name']) ? $this->db_model->upload_document($files, $value, 'images', '') : '';
                                if (!empty($file_id)) {
                                    //Deletion of file from folder and table
                                    $info_array_file['fields'] = 'files.id,files.name,files.unique_name';
                                    $info_array_file['where'] = array('files.id' => $data['hidden_' . $value]);
                                    $info_array_file['table'] = $this->tables['files'];
                                    $file_data = $this->db_model->get_data($info_array_file);
                                    if (!empty($file_data['result'])) {
                                        $assets_config = $this->config->item('assets_images');
                                        $image_path = $assets_config['path'] . '/' . $file_data['result'][0]['unique_name'];
                                        unlink($image_path);

                                        $this->db_model->delete_data($info_array_file);
                                    }

                                    $this->db->insert($this->tables['content_details'], array('content_id' => $data['content_id'], 'image_id' => $file_id, 'position' => $counter));
                                    $contentDetailLastInserted = $this->db->insert_id($this->tables['content_details']);
                                } else {
                                    $this->db->insert($this->tables['content_details'], array('content_id' => $data['content_id'], 'image_id' => $data['hidden_' . $value], 'position' => $counter));
                                    $contentDetailLastInserted = $this->db->insert_id($this->tables['content_details']);
                                }
                            } else if (\strpos($key, 'image_credit') !== false && $contentDetailLastInserted != '') {
                                $dataUpdate = array('image_credit' => trim($value),'position' => $counter);
                                $this->db->update($this->tables['content_details'], $dataUpdate, array('id' => $contentDetailLastInserted));
                            } else if (\strpos($key, 'topic_title') !== false) {
                                if (\strpos($key, 'hidden_topic_title') !== false) {
                                    continue;
                                }
                                if (\strpos($key, 'topic_title_edit') !== false) {
                                    $dataUpdateContent = array('content_name' => $value, 'position' => $counter);
                                    $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $data['hidden_' . $key]));
                                } else {
                                    $this->db->insert($this->tables['content'], array('arm' => $arm, 'type' => 'TOPIC', 'content_name' => $value, 'created_at' => date('Y-m-d H:i:s'), 'content_id' => $data['content_id']));
                                }
                            } else if (\strpos($key, 'delete_topic') !== false) {
                                $this->delete_content($value);
                            } else if (\strpos($key, 'paragarph') !== false) {

                                $this->db->insert($this->tables['content_details'], array('text' => $value, 'content_id' => $data['content_id'], 'position' => $counter));
                            }
                            $counter++;
                        }
                    }
                    $status = 'success';
                    $msg = $this->lang->line('chaptee_content_updated');
                    $id = '';
                }
            }
        } else if ($content_type == 'TOPIC') {
            if ($type == 'ADD_TOPIC_CONTENT') {
                if (!empty($data)) {
                    $counter = 1;
                    foreach ($data as $key => $value) {
                        if ($key == 'topic_title') {
                            $dataUpdateContent = array('content_name' => $value, 'title' => $value, 'slug' => $data['slug']);
                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $content_id));
                        } else if ($key == 'first_paragragh') {
                            $dataUpdateContent = array('intro_text' => $value);
                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $content_id));
                        } else {
                            if (\strpos($key, 'image_hidden') !== false) {
                                $file_id = !empty($_FILES[$value]['name']) ? $this->db_model->upload_document('', $value, 'images', '') : '';
                                if (!empty($file_id)) {
                                    $this->db->insert($this->tables['content_details'], array('content_id' => $content_id, 'image_id' => $file_id, 'position' => $counter));
                                    $contentDetailLastInserted = $this->db->insert_id($this->tables['content_details']);
                                }
                            } else if (\strpos($key, 'image_credit') !== false && $contentDetailLastInserted != '') {

                                $dataUpdate = array('image_credit' => trim($value));
                                $this->db->update($this->tables['content_details'], $dataUpdate, array('id' => $contentDetailLastInserted));
                            }
                            else if (\strpos($key, 'resource') !== false) {

                                $this->db->insert($this->tables['content_details'], array('content_id' => $content_id, 'resources_id' => $value, 'position' => $counter));
                            } else if ($key == 'click_to_add' || $key == 'slug') {
                                $counter++;
                                continue;
                            } else {
                                $this->db->insert($this->tables['content_details'], array('text' => $value, 'content_id' => $content_id, 'position' => $counter));
                            }
                            $counter++;
                        }
                    }

                    $status = 'success';
                    $msg = $this->lang->line('topic_content_added');
                    $id = '';

                }
            }
            if ($type == 'EDIT_TOPIC_CONTENT') {
                if (!empty($data)) {
                    $info_array = array();
                    $info_array['where'] = array('content_details.content_id' => $data['content_id']);
                    $info_array['table'] = $this->tables['content_details'];
                    $data_del = $this->db_model->delete_data($info_array);
                    $counter = 1;
                    foreach ($data as $key => $value) {
                        if ($key == 'topic_title') {

                            $dataUpdateContent = array('content_name' => $value, 'title' => $value);
                            if (isset($data['slug'])) {
                                $dataUpdateContent['slug'] = $data['slug'];
                            }

                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $data['content_id']));
                        } else if ($key == 'first_paragragh') {

                            $dataUpdateContent = array('intro_text' => $value);
                            $this->db->update($this->tables['content'], $dataUpdateContent, array('id' => $data['content_id']));
                        } else {
                            if (\strpos($key, 'image_hidden') !== false) {
                                $file_id = !empty($_FILES[$value]['name']) ? $this->db_model->upload_document('', $value, 'images', '') : '';
                                if (!empty($file_id)) {

                                    //Deletion of file from folder and table
                                    $info_array_file['fields'] = 'files.id,files.name,files.unique_name';
                                    $info_array_file['where'] = array('files.id' => $data['hidden_' . $value]);
                                    $info_array_file['table'] = $this->tables['files'];
                                    $file_data = $this->db_model->get_data($info_array_file);
                                    if (!empty($file_data['result'])) {
                                        $assets_config = $this->config->item('assets_images');
                                        $image_path = $assets_config['path'] . '/' . $file_data['result'][0]['unique_name'];
                                        unlink($image_path);

                                        $this->db_model->delete_data($info_array_file);
                                    }

                                    $this->db->insert($this->tables['content_details'], array('content_id' => $data['content_id'], 'image_id' => $file_id, 'position' => $counter));
                                    $contentDetailLastInserted = $this->db->insert_id($this->tables['content_details']);
                                } else {

                                    $this->db->insert($this->tables['content_details'], array('content_id' => $data['content_id'], 'image_id' => $data['hidden_' . $value], 'position' => $counter));
                                    $contentDetailLastInserted = $this->db->insert_id($this->tables['content_details']);
                                }
                            } else if (\strpos($key, 'image_credit') !== false && $contentDetailLastInserted != '') {

                                $dataUpdate = array('image_credit' => trim($value));
                                $this->db->update($this->tables['content_details'], $dataUpdate, array('id' => $contentDetailLastInserted));
                            }else if (\strpos($key, 'resource') !== false) {
                                $this->db->insert($this->tables['content_details'], array('content_id' => $data['content_id'], 'resources_id' => $value, 'position' => $counter));
                            } else if (\strpos($key, 'paragarph') !== false) {

                                $this->db->insert($this->tables['content_details'], array('text' => $value, 'content_id' => $data['content_id'], 'position' => $counter));
                            }
                            $counter++;
                        }
                    }
                    $status = 'success';
                    $msg = $this->lang->line('topic_content_updated');
                    $id = '';
                }
            }
        }
        return array('status' => $status, 'msg' => $msg, 'content_id' => $id);
    }

    public function get_content($params = array())
    {
        extract($params);
        $table = isset($table) ? $table : 'content';
        $fields = isset($fields) ? $fields : '*';

        $info_array = array('fields' => $fields);
        if ($where) {
            $info_array['where'] = $where;
        }
        if ($table == 'content_details') {
            $order_by = "content_details.position";
            $order = 'ASC';
        } else if ($table == 'content') { 
            $order_by = "content.position";
            $order = 'ASC';
        }
        if (isset($order_by)) {
            $info_array['order_by'] = $order_by;
        }
        if (isset($order)) {
            $info_array['order'] = $order;
        }

        $info_array['table'] = $this->tables[$table];
        $data = $this->db_model->get_data($info_array);

        // p($data);die;
        return $data['result'];

    }

    public function get_content_details($params = '')
    {
        extract($params);
        $is_sub_topic = isset($is_sub_topic) ? $is_sub_topic : '';
        $is_app = isset($is_app) ? $is_app : false;
        $arm = (isset($arm) && $arm) ? $arm : '';
        $users_id = (isset($users_id) && $users_id) ? $users_id : '';
        $final_arr = array();
        $side_resources = array();
        $where = array('content.' . $field . '' => "'" . $param . "'");
        if ($arm) {
            $where['content.arm'] = "'" . $arm . "'";
        }
        $content = $this->get_content(array('table' => 'content', 'where' => $where, 'fields' => 'id,content_name,icon_class,title,intro_text,slug'));
        $breadcrumb = array();
        $next_prev_sub_topic = array();
        if (!empty($content)) {
            
			$content_details = $this->get_content(array('table' => 'content_details', 'where' => array('content_details.content_id' => $content[0]['id'])));
            // print_r($content_details);
            $content_detail = array();
            if (!empty($content_details)) {
				// print_r($content_details);
                foreach ($content_details as $key => $details) {
                    if (!empty($details['text'])) {
                        $content_detail[$key]['text'] = $details['text'];
                    }
                    if ($details['image_id'] != null) {
                        $content_detail[$key]['image'] = get_file($details['image_id']);
                        $content_detail[$key]['credit'] = $details['image_credit'];
                    }
                }
			}
            $topic_array = array();
            if (!$is_sub_topic) {
                $topic_content = $this->get_content(array('table' => 'content', 'where' => array('content.content_id' => $content[0]['id']), 'fields' => 'id,content_name,intro_text,slug'));
                // print_r($topic_content);
                if (!empty($topic_content)) {
                    foreach ($topic_content as $key => $topic_details) {
                        $desc_arr = array();
                        $sub_top_arr = array();
                        $resource = array();
                        $topic_content_array = $this->get_content(array('table' => 'content_details', 'where' => array('content_details.content_id' => $topic_details['id'])));
                        // print_r($topic_content_array);
                        if (!empty($topic_content_array)) {
                            foreach ($topic_content_array as $key => $topic_content_details) {
                                // print_r($topic_content_details);
                                if (!empty($topic_content_details['text'])) {
                                    $desc_arr[$key]['text'] = $topic_content_details['text'];
                                }

                                if ($topic_content_details['image_id'] != null) {
                                    $desc_arr[$key]['image'] = get_file($topic_content_details['image_id']);
                                    $desc_arr[$key]['credit'] = $topic_content_details['image_credit'];
                                }
                                if ($topic_content_details['resources_id'] != null) {
									$resourceData = $this->get_resource_list(array('id' => $topic_content_details['resources_id'], 'users_id' => $users_id, 'content_id' => $topic_content_details['content_id']));
                                    if (isset($resourceData[0]['type']) && $resourceData[0]['type']) {
                                        $week_info = $this->questionnaire->get_week_info($users_id);
                                        $is_completed = 0;
                                        $last_left_time = 999999;
                                        $resource_id = array();
                                        if (isset($week_info) && $week_info) {
                                            $week_info_id = $week_info->id;
                                            
                                        }
                                        $resourceData[0]['left_content'] = true;
                                        // $resourceData[0]['chapter_content_id'] = $content_id->content_id;
                                        $resourceData[0]['is_completed'] = $is_completed>0;
					$resourceData[0]['link'] =trim_video_link($resourceData[0]['type'],$resourceData[0]['link']);
                                        $resourceData[0]['chapter_has_content_id'] = $topic_details['id'];
                                       
                                        $side_resources[strtolower($resourceData[0]['type'])][] = $resourceData[0];
                                        $resource['resource_data'][]=$resourceData[0];

                                        
									}
                                }
                            }
                        }
                        $topic_array[] = array('id'=>$topic_details['id'],'title' => $topic_details['content_name'], 'first_paragraph' => $topic_details['intro_text'], 'description' => $desc_arr, 'resource_data' => isset($resource['resource_data']) ? $resource['resource_data'] : array(), 'slug' => $topic_details['slug']); // , 'sub_topic' => $sub_top_arr
                    }
                }
            }

			$cources_has_resource_array = $this->get_cources_has_resource(array('where' => array('content_has_resources.content_id' => $content[0]['id'])));
            if (!empty($cources_has_resource_array)) {
                foreach ($cources_has_resource_array as $cources_has_resource) {
					$resource_detail = $this->get_resource_list(array('id' => $cources_has_resource['resources_id'], 'users_id' => $users_id, 'content_id' => $cources_has_resource['content_id']));
                    if (!empty($resource_detail)) {
                        $week_info = $this->questionnaire->get_week_info($users_id);
                        $is_completed = 0;
                        if (isset($week_info) && $week_info) {
                            $week_info_id = $week_info->id;
                        }
			$resource_link = trim_video_link($resource_detail[0]['type'],$resource_detail[0]['link']);

                        $res_data = array('chapter_has_content_id'=>$content[0]['id'],'id' => $resource_detail[0]['id'], 'title' => $resource_detail[0]['title'], 'link' => $resource_link, 'type' => $resource_detail[0]['type'], 'is_completed' => $is_completed>0);
                        
                        if (isset($resource_detail[0]['type']) && $resource_detail[0]['type']) {
                            $side_resources[strtolower($resource_detail[0]['type'])][] = $res_data;
                        }
                    }
                }
            }
            
            if($arm!='control'){
                $side_resources['exercises'] = $this->get_chapter_resources(array('fields'=>'content_id,id,position,sub_header,title','where' => array('exercises.content_id' => $content[0]['id']),'table'=>'exercises','user_order_by'=>'position'));
            }

            $final_arr = array(
                'id' => $content[0]['id'],
                'content_name' => $content[0]['content_name'],
                'slug' => $content[0]['slug'],
                'first_paragraph' => $content[0]['intro_text'],
                'content_details' => $content_detail,
                'topic' => $topic_array,
                'breadcrumb' => $breadcrumb,
                'side_resources' => !empty($side_resources) ? $side_resources : array(),
            );
        }
        return $final_arr;

    }

    public function get_exercise_details($params=array()){

        extract($params);
        if(isset($param) || !is_numeric($exercise_id)){
            return 0;
        }

        $this->db->select('ex.title,ex.sub_header,ex.description,ex.content_id,ex.worksheet_id,ex.position');
        $this->db->where('ex.id',$exercise_id);
        $this->db->from('exercises As ex');
        $exercise = $this->db->get()->result_array();

        if(isset($content_id) && $content_id){
            $week_data=current_week_number($users_id);
            if(empty($week_data)){
                return 0;
            }
        }

        if(!empty($exercise[0])){
            $this->db->select('ex_item.primary_prompt,ex_item.secondary_prompt,ex_item.first_heading,ex_item.second_heading,ex_item.type,ex_item.id,ex_item.number_of_items,ex_item.text_field_size,ex_item.exercises_id,ex_item.position');
            $this->db->where('ex_item.exercises_id',$exercise_id);
            $this->db->from('exercise_item AS ex_item');
            $exercise_items = $this->db->get()->result_array();

            foreach ($exercise_items as $key => $item) {
                switch($item['type']){
                    case 'RADIO':
                    case 'CHECKBOX':{
                        $options=$this->db->select('id,title')
                                            ->where('exercise_item_id',$item['id'])
                                            ->get($this->tables['exercise_item_details'])
                                            ->result_array();

                        if(isset($content_id) && $content_id){
                            //user answer if given
                            $user_answer=$this->db->select('id,exercise_item_id,exercise_item_details_id as answer_id,response_2')->where(array('exercise_item_id'=>$item['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))->get($this->tables['users_has_exercise_item'])->result_array();
                            
                            $exercise_items[$key]['options']=$this->get_users_answer($options,$user_answer);
                            break;
                        }
                        $exercise_items[$key]['options']=$options;
                        break;
                        
                    }
                    case 'RATING':{
                        $options=$this->db->select('id,title')->where('exercise_item_id',$item['id'])->get($this->tables['exercise_item_details'])->result_array();

                        if(isset($content_id) && $content_id){
                            $user_answer=$this->db->select('exercise_item_details_id as answer_id,response_1,response_2')->where(array('exercise_item_id'=>$item['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))->get($this->tables['users_has_exercise_item'])->result_array();

                            $exercise_items[$key]['options']=$this->get_users_answer($options,$user_answer,true);

                            if(!empty($user_answer)){
                                $exercise_items[$key]['user_answer']=array_values(array_filter($user_answer,function($answer){
                                    return $answer['answer_id']=='';
                                }));
                            }

                            break;
                        }
                        $exercise_items[$key]['options']=$options;
                        break;
                    }
                    case 'TEXT_ITEM':{
                        $exercise_items[$key]['options']=[];

                        if(isset($content_id) && $content_id){
                            $user_answer=$this->db->select('response_1')->where(array('exercise_item_id'=>$item['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))->get($this->tables['users_has_exercise_item'])->result_array();

                            $exercise_items[$key]['user_answer']=isset($user_answer[0])?$user_answer[0]['response_1']:'';
                            break;
                        }
                        break;
                    }
                    case 'TWO_COL':{
                        $exercise_items[$key]['options']=[];

                        if(isset($content_id) && $content_id){
                            $user_answer=$this->db->select('response_1,response_2')->where(array('exercise_item_id'=>$item['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))->get($this->tables['users_has_exercise_item'])->result_array();

                        
                            !empty($user_answer) && $user_answer ?$exercise_items[$key]['user_answer']=$user_answer:'';
                            break;
                        }

                        break;
                    }
                    case 'GOAL':{
                        $exercise_items[$key]['first_heading']='Take a moment to write down 2-3 goals that SmartManage could help with. We will spend a little more
                        time on this after you have learned more about the program.';
                        $options=$this->db->select('id,title')->get($this->tables['goals'])->result_array();

                        if(isset($content_id) && $content_id){
                            $user_answer=$this->db->select('id,exercise_item_id, goals_id as answer_id,response_1')->where(array('exercise_item_id'=>$item['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))->get($this->tables['users_has_exercise_item'])->result_array();
                            $exercise_items[$key]['options']=$this->get_users_answer($options,$user_answer);

                            if(!empty($user_answer)){
                                $tempArr=array_values(array_filter($user_answer,function($answer){
                                    return $answer['answer_id']=='';
                                }));
                                !empty($tempArr)?$exercise_items[$key]['user_answer']=$tempArr:'';
                            }

                            break;
                        }
                        $exercise_items[$key]['options']=$options;
                        break;
                    }
                    case 'GOAL_TRACKING':{
                        $exercise_items[$key]['headings']=array(
                            'These were the personal goals you articulated at the beginning of the training:',
                            'Have you gotten closer to any of these goals?if so ,What progress have you seen?',
                            'What Smartmanage skills can you to help you to keep making progress towards these goals?'
                        );

                        $options=$this->db->select('id,title')->get($this->tables['goals'])->result_array();

                        if(isset($content_id) && $content_id){
                            $exercise_item=$this->db->select('exercise_item_id as id,type,count(*) as dup')
                                                    ->where(array('type'=>'GOAL','users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))
                                                    ->group_by('uei.exercise_item_id')
                                                    ->order_by('uei.id','ASC')
                                                    ->having("dup>1")
                                                    ->get($this->tables['users_has_exercise_item']. ' uei')
                                                    ->result_array();

                            if(!empty($exercise_item)){
                                $user_answer=$this->db->select('id,exercise_item_id, goals_id as answer_id')
                                                        ->where(array('exercise_item_id'=>$exercise_item[0]['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))
                                                        ->get($this->tables['users_has_exercise_item'])
                                                        ->result_array();
                                                    
                                $options=$this->get_users_answer($options,$user_answer);

                                if(!empty($options)){
                                    $tempArr=array_values(array_filter($options,function($option){
                                        return $option['checked'];
                                    }));
                                    if(!empty($tempArr)){
                                        $exercise_items[$key]['options']=$tempArr;
                                        $exercise_items[$key]['number_of_items']=count($tempArr);
                                    };
                                }
                            }

                            $user_answer=$this->db->select('id,exercise_item_id, goals_id as answer_id,response_1')
                                                        ->where(array('exercise_item_id'=>$item['id'],'users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']))
                                                        ->get($this->tables['users_has_exercise_item'])
                                                        ->result_array();

                            if(!empty($user_answer)){
                                $tempArr=array_values(array_filter($user_answer,function($answer){
                                    return $answer['answer_id']=='';
                                }));
                                !empty($tempArr)?$exercise_items[$key]['user_answer']=$tempArr:'';
                            }

                            break;
                        }
                        $exercise_items[$key]['options']=$options;
                        break;
                    }
                    default:{
                        //user has no item
                    }
                }
            }

            if(!empty($exercise_items[0])){
                $result['exercise']=$exercise;
                $result['exercise_items']=$exercise_items;

                // p($exercise_items);
            }else{
                $result['exercise']=$exercise;
                $result['exercise_items']=[];
            }
        }
      
        if(isset($content_id) && $content_id){

            $pos = $this->db->query('select position from exercises where id = '.$exercise_id)->result_array();

            if(isset($pos[0])){
                $pos=$pos[0]['position'];
                $next = $this->db->query('select id, content_id from exercises where position = (select min(position) from exercises where position > '.$pos.' && content_id = '.$content_id.')')->result_array();
                $next=$this->get_position_exercise($next,$content_id);
                
                $pre = $this->db->query('select id, content_id from exercises where position = (select max(position) from exercises where position < '.$pos.' && content_id = '.$content_id.') ')->result_array();
                $pre=$this->get_position_exercise($pre,$content_id);
            }

            $result['next']=isset($next[0])?$next[0]['id']:'end';
            $result['pre']=isset($pre[0])?$pre[0]['id']:'end';
        }
        return $result;
    }

    public function get_users_answer($options=[],$user_answer=[],$res_txt=false){
        if($user_answer && !empty($options)){
            foreach ($options as $i=>$option) {
                $flag=false;
                foreach ($user_answer as $answer) {
                    if($option['id']==$answer['answer_id']){
                        $flag=$res_txt?$answer['response_2']:true;
                    }
                }
                $options[$i]['checked']=$flag;
            }
        }
        return $options;
    }

    public function set_exercise_details($params=array()){
        extract($params);

        if(isset($param) || !is_numeric($exercise_id)){
            return 0;
        }

        $week_data=current_week_number($users_id);
        
        $exercise_items=[];
        foreach ($exercise_data as $key => $item) {
            extract($item);
            $exercise_item_details_id=$exercise_item_details_id?$exercise_item_details_id:NULL;
            $goals_id=$goals_id?$goals_id:NULL;
            
            if(isset($type) && $type){
                $exercise_items[]=array('exercise_item_id'=>$exercise_item_id,
                'exercises_id'=>$exercise_id,
                'type'=>$type,
                'users_id'=>$users_id,
                'exercise_item_details_id'=>$exercise_item_details_id,
                'goals_id'=>$goals_id,
                'response_1'=>$response_1,
                'response_2'=>$response_2,
                'week_info_id'=>$week_data['week_info_id']
                );
            }
        };

        $this->db->trans_start();
        if(!empty($exercise_items)){
            $info_array = array();
            $info_array['where'] = array('users_has_exercise_item.exercises_id' => $exercise_id,'users_has_exercise_item.users_id'=>$users_id,'week_info_id'=>$week_data['week_info_id']);
            $info_array['table'] = $this->tables['users_has_exercise_item'];
            $del=$this->db_model->delete_data($info_array);

            $this->db->insert_batch($this->tables['users_has_exercise_item'], $exercise_items);

            generate_log("User[$users_id] has given execise, id=".$exercise_id);
        }
        $this->db->trans_complete();

		if ($this->db->trans_status() !== false) {
			return array('status' => 'success', 'msg' => 'Exercise data saved successfully');
        }
        return array('status' => 'error', 'msg' => 'Error while saving exercise data');
    }


    public function get_position_exercise($next,$content_id){
        if(!empty($next)){
            return array_values(array_filter($next,function($ele) use ($content_id){
                return $ele['content_id']==$content_id;
            }));
        }
        return array();
    } 

    public function get_content_list($params = array(), $arm = "CONTROL")
    {
        extract($params);
        
        $data = array('result' => []);
        $col_sort = array("id", "content_name", "status");
        $info_array['fields'] = 'content.id as id,content.content_name as content_name,content.position';
        $order_by = "content.position";
        $order = 'ASC';
        $search_array = false;
        $join = false;
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
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
        $info_array['where'] = array('content_id' => null, 'arm' => '"' . $arm . '"');
        $info_array['table'] = $this->tables['content'];

        $result = $this->db_model->get_data($info_array);

        $arr = array();
        $i = 0;
        if (!empty($result['result'])) {
            foreach ($result['result'] as $key => $val) {
                $arr['result'][$i]['is_chapter'] = true;
                $arr['result'][$i]['id'] = $val['id'];
                $arr['result'][$i]['chapter'] = 'chapter-' . $val['id'];
                $arr['result'][$i]['content_name'] = $val['content_name'];

                $info_array2['where'] = array('content_id' => $val['id']);
                $info_array2['order_by'] = $order_by ;
                $info_array2['table'] = $this->tables['content'];
                $result2 = $this->db_model->get_data($info_array2);
                
                if (!empty($result2['result'])) {
                    foreach ($result2['result'] as $key2 => $val2) {
                        $i++;
                        $arr['result'][$i]['is_topic'] = true;
                        $arr['result'][$i]['chapter'] = 'chapter-' . $val['id'];
                        $arr['result'][$i]['id'] = $val2['id'];
                        $arr['result'][$i]['content_name'] = $val2['content_name'];
                    }
                } else {
                    $arr['result'][$i]['no_records'] = true;
                }
                $i++;
            }
            $data = $arr;
        }
        $data['total'] = count($data['result']);
        return $data;
    }
    public function get_exercise_list($params = array(), $arm = "CONTROL",$content_id)
    {
        extract($params);
     
        $data = array('result' => []);
        $col_sort = array("id", "exercise_title");
        $info_array['fields'] = 'exercises.id as id,exercises.title as exercise_title';
        $order_by = "exercises.position";
        $order = 'ASC';
        $search_array = false;
        $join = false;
        if (isset($params['iSortCol_0'])) {
            $index = $params['iSortCol_0'];
            $order = $params['sSortDir_0'] === 'asc' ? 'asc' : 'desc';
            $order_by = $col_sort[$index];
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
        $info_array['where'] = array('content_id' =>$content_id);
        $info_array['table'] = $this->tables['exercises'];
        $result = $this->db_model->get_data($info_array);
        $arr = array();

        $i = 0;
        $arr['result']=array();
        if (!empty($result['result'])) {
            foreach ($result['result'] as $key => $val) {
                $arr['result'][$i]['is_exercise'] = true;
                $arr['result'][$i]['id'] = $val['id'];
                $arr['result'][$i]['exercise'] = 'exercise-' . $val['id'];
                $arr['result'][$i]['exercise_title'] = $val['exercise_title'];

                $i++;
            }
           
        } 
        // $arr['result']['no_records'] = $i;
        $data = $arr;
        $data['total'] = count($data['result']);
        return $data;
    }

    public function get_resource_list($param)
    {
        extract($param);
        $is_completed = 0;
        $info_array = array('fields' => '*');
        if (!empty($id)) {
            $info_array['where'] = array('resources.id' => $id);
        }
        if (!empty($type)) {
            $info_array['where'] = "resources.type='" . $type . "'";
        }
        $info_array['table'] = $this->tables['resources'];
        $data = $this->db_model->get_data($info_array);
        
        if (isset($data['result'][0])) {
            $data['result'][0]['is_completed'] = $is_completed;
        }
        return $data['result'];
    }

    public function delete_content($content_id)
    {
        $assets_config = $this->config->item('assets_images');
        $info_array['fields'] = 'content.id,content.icon_class,content.content_id';
        $info_array['where'] = array('content.id' => $content_id);
        $info_array['table'] = $this->tables['content'];
        $content_detail = $this->db_model->get_data($info_array);
        if (!empty($content_detail['result'])) {
            if (!empty($content_detail['result'][0]['icon_class'])) {
                $image_path = $assets_config['path'] . '/' . $content_detail['result'][0]['icon_class'];
                unlink($image_path);
            }
            // Delete Recored from content table
            $del_array_content['where'] = array('content.id' => $content_id);
            $del_array_content['table'] = $this->tables['content'];
            $del = $this->db_model->delete_data($del_array_content);
            if ($del) {
                // Check if content detail exist or not
                $info_array['fields'] = 'content_details.id,content_details.image_id';
                $info_array['where'] = array('content_details.content_id' => $content_id);
                $info_array['table'] = $this->tables['content_details'];
                $is_exist_details = $this->db_model->get_data($info_array);
                if (!empty($is_exist_details['result'])) {

                    foreach ($is_exist_details['result'] as $key => $content_details) {
                        if ($content_details['image_id'] != null) {

                            $info_array['fields'] = 'files.id,files.unique_name';
                            $info_array['where'] = array('files.id' => $content_details['image_id']);
                            $info_array['table'] = $this->tables['files'];
                            $is_exist_filedata = $this->db_model->get_data($info_array);
                            if (!empty($is_exist_filedata['result'])) {
                                $del_array_files['where'] = array('files.id' => $is_exist_filedata['result'][0]['id']);
                                $del_array_files['table'] = $this->tables['files'];
                                $del = $this->db_model->delete_data($del_array_files);
                                if ($del) {
                                    if (!empty($is_exist_filedata['result'][0]['unique_name'])) {
                                        $image_path = $assets_config['path'] . '/' . $is_exist_filedata['result'][0]['unique_name'];
                                        unlink($image_path);
                                    }
                                }
                            }
                        }
                        $del_array_details['where'] = array('content_details.id' => $content_details['id']);
                        $del_array_details['table'] = $this->tables['content_details'];
                        $del = $this->db_model->delete_data($del_array_details);
                    }

                }
                $data = array('status' => 'success', 'msg' => $this->lang->line('record_deleted'));
            } else {
                $data = array('status' => 'error', 'msg' => $this->lang->line('record_not_deleted'));
            }

            if ($content_detail['result'][0]['id']) {
                $info_details['fields'] = 'content.id';
                $info_details['where'] = array('content.content_id' => $content_detail['result'][0]['id']);
                $info_details['table'] = $this->tables['content'];
                $content_detail_topics = $this->db_model->get_data($info_details);
                if (!empty($content_detail_topics['result'])) {
                    foreach ($content_detail_topics['result'] as $other_detail) {

                        $this->delete_content($other_detail['id']);
                    }
                }
            }
        } else {
            $data = array('status' => 'error', 'msg' => $this->lang->line('record_not_found'));
        }
    }

    public function assign_resource_to_content($param)
    {
        extract($param);
        // Check if resource is already assigned
        $info_array['fields'] = 'content_has_resources.id';
        $info_array['where'] = array('content_has_resources.content_id' => $content_id, 'content_has_resources.resources_id ' => $resource);
        $info_array['table'] = $this->tables['content_has_resources'];
        $is_exists = $this->db_model->get_data($info_array);
        if (empty($is_exists['result'])) {
            $order_by = "content_has_resources.position";
            $order = 'DESC';
            $get_position['order_by'] = $order_by;
            $get_position['order'] = $order;
            $get_position['limit'] = '1';
            $get_position['fields'] = 'content_has_resources.id, content_has_resources.position';
            $get_position['where'] = array('content_has_resources.content_id' => $content_id);
            $get_position['table'] = $this->tables['content_has_resources'];
            $position = $this->db_model->get_data($get_position);
            if (!empty($position['result'])) {
                $counter = $position['result'][0]['position'] + 1;
            } else {
                $counter = 1;
            }
            $res = $this->db->insert($this->tables['content_has_resources'], array('content_id' => $content_id, 'resources_id' => $resource, 'position' => $counter));
            if ($res) {
                return 'INSERTED';
            } else {
                return 'NOTINSERTED';
            }
        } else {
            return 'EXIST'; // already exist
        }
    }

    public function get_cources_has_resource($params = '')
    {
        extract($params);
        $info_array = array('fields' => '*');
        if ($where) {
            $info_array['where'] = $where;
        }
        $order_by = "content_has_resources.position";
        $order = 'ASC';
        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['table'] = $this->tables['content_has_resources'];
        $data = $this->db_model->get_data($info_array);

        if (!empty($data['result'])) {
            return $data['result'];
        }
		return array();
    }

    public function get_chapter_resources($params = array())
    {
        extract($params);
		$info_array = array('fields' => '*');
		if ($fields) {
            $info_array['fields'] = $fields;
        }

        if ($where) {
            $info_array['where'] = $where;
        }
        $order_by = isset($user_order_by)?$user_order_by:false;
        $order = 'ASC';
        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['table'] = $this->tables[$table];
        $data = $this->db_model->get_data($info_array);

        if (!empty($data['result'])) {
            return $data['result'];
        }
		return array();
    }

    public function reorder_content_has_resources($param)
    {
        extract($param);
        $data_update = array('position' => $order);
        $this->db->update($this->tables['content_has_resources'], $data_update, array('content_id' => $content, 'resources_id' => $resource));
    }

    public function delete_content_has_resource($resource_id, $content_id)
    {
        $del_array_content['where'] = array('content_has_resources.content_id' => $content_id, 'content_has_resources.resources_id' => $resource_id);
        $del_array_content['table'] = $this->tables['content_has_resources'];
		$del = $this->db_model->delete_data($del_array_content);
		$data = array('status' => 'success', 'msg' => $this->lang->line('record_deleted'));
        if (!$del) {
            $data = array('status' => 'error', 'msg' => $this->lang->line('record_not_deleted'));
		}
		return $data;
    }

    public function check_slug_availability($slug)
    {
        $this->db->select('id');
        $this->db->from('content');
        $this->db->group_start();
        $this->db->like('slug', $slug);
        $this->db->group_end();
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return count($query->result_array());
        }
		return false;
    }

    public function reorder_content($param)
    {
        extract($param);
        $table_name=isset($table)?$table:'content';
        // p($param);
        $data_update = array('position' => $order);
        $this->db->update($this->tables[$table_name], $data_update, array('id' => $content_id));
      
    }

    /**
     * save or update event
     */
    public function save_events($params = array())
    {
        extract($params);
        if (isset($events) && isset($weeks_id)) {
            $this->db->trans_start();
            foreach ($events as $key => $event) {
                foreach ($weeks_id as $wKey => $week_id) {
                    if ($key == $wKey) {
                        $data = array('event' => $event);
                        $this->db->update($this->tables['week_info'], $data, array('id' => $week_id));
                    }
                }
            }
            $this->db->trans_complete();
            if ($this->db->trans_status() !== false) {
                return array('status' => 'success', 'msg' => $this->lang->line('event_add_success'));
            }
        }
        return array('status' => 'error', 'msg' => $this->lang->line('event_not_added'));
    }

    /**
     * get all week info
     */
    public function get_all_week($user_id = '')
    {
        $all_week_info = array();
        if (isset($user_id) && $user_id) {
            $all_week_info = $this->db->select("*")->where('users_id', $user_id)->get($this->tables['week_info'])->result_array();
        }
        return $all_week_info;
    }

    public function delete_exercise($exercise_id)
    {
        $info_array['fields'] = 'exercises.id,exercises.content_id,exercises.worksheet_id';
        $info_array['where'] = array('exercises.id' => $exercise_id);
        $info_array['table'] = $this->tables['exercises'];
        $content_detail = $this->db_model->get_data($info_array);
        if (!empty($content_detail['result'])) {
            $worksheet_id=$content_detail['result'][0]['worksheet_id'];
             /**
             * Below code work in 'files' table
             */
            if(!empty($worksheet_id) && $worksheet_id){
                $this->db_model->delete_document('pdf', $worksheet_id);
            }

            // Delete Recored from content table
            $del_array_content['where'] = array('exercises.id' => $exercise_id);
            $del_array_content['table'] = $this->tables['exercises'];
            $del = $this->db_model->delete_data($del_array_content);
            if ($del) {
                // Check if content detail exist or not
                $info_array['fields'] = 'exercise_item.id';
                $info_array['where'] = array('exercise_item.exercises_id' => $exercise_id);
                $info_array['table'] = $this->tables['exercise_item'];
                $is_exist_details = $this->db_model->get_data($info_array);
                if (!empty($is_exist_details['result'])) {
                    foreach ($is_exist_details['result'] as $key => $exercise_details) {
                        $del_array_details['where'] = array('exercise_item.id' => $exercise_details['id']);
                        $del_array_details['table'] = $this->tables['exercise_item'];
                        $del = $this->db_model->delete_data($del_array_details);
                    }

                }
                $data = array('status' => 'success', 'msg' => $this->lang->line('record_deleted'));
            } else {
                $data = array('status' => 'error', 'msg' => $this->lang->line('record_not_deleted'));
            }

            //delete users_has_exercise_item table content
            $this->db_model->delete_data(array('table'=>'users_has_exercise_item','where'=>array('users_has_exercise_item.exercises_id' => $exercise_id )));
            
        } else {
            $data = array('status' => 'error', 'msg' => $this->lang->line('record_not_found'));
        }
        
    }

      /**
     * This method is used to access pdf from database
     * 
     */
    public function get_worksheet($fileId=''){ 
        if(!isset($fileId) || empty($fileId)){
            return 0;
        }
        $file=$this->db->select('name,unique_name,size')->where('id',$fileId)->get($this->tables['files'])->row();
    
        return $file;
    }
}

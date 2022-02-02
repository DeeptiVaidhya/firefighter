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

if ( ! function_exists('p')) { // Temporary Function - Development Purpose
    function p($data){
        echo '<pre>'; print_r($data); echo '</pre>';
    }                    
}

if (!function_exists('generate_log')) {

    /**
     * @desc Used to add logs generated from APIs and user activity
     * @param string $msg
     * @return boolean
     */
    function generate_log($msg = '') {
        $message = '';
        $log_path = APPPATH . 'logs/';
        file_exists($log_path) OR mkdir($log_path, 0755, TRUE);
        if (!is_dir($log_path) OR ! is_really_writable($log_path)) {
            return FALSE;
        }
        $filepath = $log_path . 'system_log.php';

        if (!file_exists($filepath)) {
            $message .= "<" . "?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?" . ">\n\n";
        }
        
        if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE)) {
            return false;
        }
        
        $message .= date('Y-m-d H:i:s')." [".get_real_ip_addr()."] ".$msg."\n";
        flock($fp, LOCK_EX);
        fwrite($fp, $message);
        flock($fp, LOCK_UN);
        fclose($fp);

        @chmod($filepath, FILE_WRITE_MODE);
        return true;
    }

}
if (!function_exists('get_real_ip_addr')) {

    /**
     * @desc Used to get real ip for user activity
     * @return string
     */
    function get_real_ip_addr() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}

if (!function_exists('aes_256_encrypt')) {

    /**
     * @desc Used to encrypt a string in AES 256, to store in Database
     * @param string $str
     */
    function aes_256_encrypt($str = '') {
        $CI = & get_instance();
        $config = $CI->config->item('encryption');
        $CI->encryption->initialize($config);
        if ($str != '') {
            return $CI->encryption->encrypt($str);
        }
        return '';
    }

}

if (!function_exists('current_week_number')) {

    /**
     * @desc Used to get current week number
     * @param string $user's id
     */
    function current_week_number($user_id) {

        if(isset($user_id) && !empty($user_id)){
            $CI = & get_instance();

            $info_array['fields'] = 'COUNT(w.id) as total_q,w.week_number, w.total_time_spent_in_week, w.id as week_info_id';

            $where = array('w.users_id' => $user_id);
            $where['w.week_starts_at <= '] = "'" . date('Y-m-d H:i:s') . "'";
            $where['w.week_ends_at >='] = "'" . date('Y-m-d H:i:s') . "'";

            $info_array['where'] = $where;
            $info_array['group_by'] = array('w.users_id');
            $info_array['table'] = 'week_info w';
            $result=$CI->db_model->get_data($info_array)['result'];
            return isset($result[0]) ? $result[0] :array();
        }
    }

}
if (!function_exists('aes_256_decrypt')) {

    /**
     * @desc Used to decrypt a encrypted AES 256 string, fetched from Database
     * @param string $str
     */
    function aes_256_decrypt($str = '') {
        $CI = & get_instance();
        $config = $CI->config->item('encryption');
        $CI->encryption->initialize($config);
        if ($str != '') {
            return $CI->encryption->decrypt($str);
        }
        return '';
    }

}

if (!function_exists('file_check')) {
    /*
     * file value and type check during validation
     */
    function file_check($file_name,$files_allowed=''){
        $CI = & get_instance();
        $allowed_mime_type_arr=array('application/pdf','image/gif','image/jpeg','image/pjpeg','image/png','image/x-png');

        if(!empty($files_allowed)){
            $files_allowed=explode("|",$files_allowed );
            $allowed_mime_type_arr= $files_allowed;
        } 

        $mime = get_mime_by_extension($_FILES[$file_name]['name']);
        if(isset($_FILES[$file_name]['name']) && $_FILES[$file_name]['name']!=""){
            if(in_array($mime, $allowed_mime_type_arr)){
                return '';
            }else{
                return 'Please select only valid file.';
            }
        }else{
            return 'Please choose a file to upload.';
        }
    }

}


if (!function_exists('send_email')) {

    /**
     * @desc Send email function to use globally from Application
     * @param type $subject
     * @param type $to
     * @param type $msg
     * @param type $attachment
     * @return type
     */
    function send_email($subject = false, $to = false, $msg = false, $attachment = false, $cc_email=false) {
        $CI = & get_instance();
        $CI->load->library('email');
        $CI->email->clear();
        /* Add To Email */
        if ($to != false) {
            $CI->email->to($to);
        }
        /* Add From Email */
		$from = $CI->config->item('email_from_info');

		$CI->email->from($from, $CI->config->item('site_name'));
        if($cc_email){
            $CI->email->cc($cc_email);
        }
		$reply_to = $CI->config->item('email_reply_to_info');
		$reply_to && $CI->email->reply_to($reply_to, $CI->config->item('site_name'));
        /* Add From subject */
        $CI->email->subject($subject);


        /* Add message content */
        $CI->email->message($msg);
        /* Add attachment */
        if ($attachment != false) {
            if (is_array($attachment)) {
                foreach ($attachment as $val) {
                    $CI->email->attach($val, 'attachment');
                }
            } else {
                $CI->email->attach($attachment, 'attachment');
            }
        }
        /* Mail all data */
        $status = ($CI->email->send()) ? true : false;
        return $status;
    }

}

if (!function_exists('get_file')) {

    function get_file($id = '', $url = false, $base_url=false) {
        $CI = & get_instance();
        $info_array = array('where' => array('id' => $id), 'table' => 'files');
        $info_array['fields'] = 'files' . '.*';
        $file_detail = $CI->db_model->get_data($info_array);
        if ($file_detail['result']) {
            $mime_type_or_return = $file_detail['result'][0]['type'];
            $type = explode('/', $mime_type_or_return)[0] . 's';
            $config = $CI->config->item('assets_' . $type);
            $upload_path = check_directory_exists($config['path']);
            $file_path = $base_url ? $base_url.$file_detail['result'][0]['unique_name'] : base_url($upload_path . '/' . $file_detail['result'][0]['unique_name']);
            // Return the image or output it?
            switch ($type) {
                case 'images':
                    $file = '<img class="img-responsive box-image" src="' . $file_path . '" alt="image" title="image">';
                    break;
                case 'audios':
                    $file = '<audio controls class="box-audio"><source src="' . $file_path . '" type="' . $mime_type_or_return . '"></audio>';
                    break;
                case 'videos':
                    $file = '<video class="box-video" width="100%" height="200" controls><source src="' . $file_path . '" type="' . $mime_type_or_return . '"></video>';
                    break;

                default:
                    $file = 'File not found';
                    break;
            }

            if ($url) {
                return array('type' => $type, 'url' => $file_path);
            }
            return $file;
        }

        return "<p>File not found</p>";
        // reads and outputs the file onto the output buffer
    }

}
/**
 * Creating slug from title/name
 *
 * @access  public
 * @param   string
 * @return  string
 */
if (!function_exists('create_slug')) {

    function create_slug($slug) {
        $lettersNumbersSpacesHyphens = '/[^\-\s\pN\pL]+/u';
        $spacesDuplicateHypens = '/[\-\s]+/';

        $slug = preg_replace($lettersNumbersSpacesHyphens, '', $slug);
        $slug = preg_replace($spacesDuplicateHypens, '-', $slug);

        $slug = trim($slug, '-');

        return mb_strtolower($slug, 'UTF-8');
    }

}


if (!function_exists('assets_url')) {

    /**
     * @desc Function to get assets URL
     * @param type $uri
     * @return type
     */
    function assets_url($uri = '') {
        $CI = & get_instance();
        return $CI->config->item('base_url') . $CI->config->item('assets_url') . trim($uri, '/');
    }

}
if (!function_exists('re_arrange_files')) {

    function re_arrange_files($file_post = array(), $name = '') {
        $file_ary = array();
        $file_name = $file_post['name'];
        $file_keys = array_keys($file_post);

        foreach ($file_name as $i => $f_name) {
            foreach ($file_keys as $key) {
                $file_ary[$name . '_' . $i][$key] = $file_post[$key][$i];
            }
        }
        return $file_ary;
    }

}


if (!function_exists('check_directory_exists')) {

    /**
     * @desc Check the file or directory exists or not, if not then create it. Returns dir full path
     * @param type $file_name
     * @return type
     */
    function check_directory_exists($file_name = '') {
        $file_name = str_replace('\\', '/', $file_name);/** Replace for Linux server */
        if (!file_exists($file_name)) {
            @mkdir($file_name, 0777, true);
            $my_file = $file_name . '/index.html';
            $handle = @fopen($my_file, 'w');

            $data = '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>';
            @fwrite($handle, $data);
            @fclose($handle);
        }
        return $file_name;
    }

}

if (!function_exists('get_plugins_in_template')) {

    /**
     * @desc Function to load JS and/or CSS for a plugin
     * @param type $plugin
     */
    function get_plugins_in_template($plugin = '') {
        $CI = & get_instance();
        switch ($plugin) {
            case 'datatable':
                $CI->template->javascript->add('assets/js/jquery.dataTables.min.js');
                $CI->template->javascript->add('assets/js/dataTables.bootstrap.min.js');
                $CI->template->javascript->add('assets/js/dataTables.responsive.min.js');
                // Dynamically add a css stylesheet
                $CI->template->stylesheet->add('assets/css/dataTables.bootstrap.min.css');
                $CI->template->stylesheet->add('assets/css/responsive.bootstrap.min.css');
                break;
            case 'color-picker':
                $CI->template->javascript->add('assets/js/bootstrap-colorpicker.min.js');
                // Dynamically add a css stylesheet
                $CI->template->stylesheet->add('assets/css/bootstrap-colorpicker.min.css');
                break;
            default:
                break;
        }
    }

}

if (!function_exists('create_dynamic_textarea')) {
    /**
     * @desc Function to create dynamic textara
     * @param type $data
     * @return html
     */
    function create_dynamic_textarea($data){
		$htm = '';
		if(!empty($data['text'])){
				$htm .= '<div id="set_1" class="textareaTiny ui-state-default">
                <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Paragraph Text </label>
                    <div class="col-md-7 col-sm-7 col-xs-10">
                        <textarea class="form-control col-md-7 col-xs-12 text-tiny-mce" name="'.$data['name'].'" id="">'.$data['text'].'</textarea>
                        <?php echo form_error("'.$data['name'].'"); ?>
                    </div>
                    <div class="col-md-2 col-sm-2 col-xs-2 icon-move-trash">
                        <i class="fa fa-arrows" aria-hidden="true"></i>
                        <i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                    </div>
                </div>
            </div>';
			return $htm;
		} else {
			return '';
		}
	}
}

if (!function_exists('create_dynamic_fileupload')) {
    /**
     * @desc Function to create dynamic file upload html element
     * @param type $data
     * @return html
     */
    function create_dynamic_fileupload($data){
		$htm = '';
		if(!empty($data['image_id'])){
			$htm .= '<div id="s" class="image_content ui-state-default ui-sortable-handle">';
            $htm .= '<div class="form-group">';
            $htm .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Image</label>';
            $htm .= '<div class="col-md-6 col-sm-6 col-xs-12">';
            $htm .= '<input type="hidden" name="'.$data['hidden_image_name'].'" value="'.$data['name_and_value'].'">';
            $htm .= '<input type="hidden" name="'.$data['hidden_image_id'].'" value="'.$data['image_id'].'">';
            $htm .= '<input class="input-file filebox" type="file" name="'.$data['name_and_value'].'"><p><small>Allowed type ( gif, png, jpg, jpeg )</small></p>'.get_file($data['image_id']);
            $htm .= '</div>';
            $htm .= '<div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash"><i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i></div>';
            $htm .= '</div>';
            $htm .= '<div class="form-group">';
            $htm .= '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image Credit</label>';
            $htm .= '<div class="col-md-6 col-sm-6 col-xs-12">';
            $htm .= '<input type="text" id="'.$data['image_credit_name_id'].'" name="'.$data['image_credit_name_id'].'" placeholder="Image Credit" class="form-control" value="'.$data['image_credit_value'].'" autocomplete="off">';
            $htm .= '</div>';
            $htm .= '</div>';
            $htm .= '</div>';
			return $htm;
		} else {
			return '';
		}
	}
}

if (!function_exists('create_dynamic_topic')) {
    /**
     * @desc Function to create dynamic topic html element
     * @param type $data
     * @return html
     */
    function create_dynamic_topic($data){
        $htm = '';
        $CI = & get_instance();
        $edit_url = $CI->config->item('base_url') .'educational/edit-topic-content/'.$data['arm_type'].'/'.$data['id'];
		if(!empty($data['content_name'])){
            $htm .= '<div id="" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="Topic_title">Topic Title </label>
                            <div class="col-md-6 col-sm-6 col-xs-11">
                                <input type="hidden" name="'.$data['hidden_name'].'" value="'.$data['id'].'">
                                <input id="'.$data['name'].'" name="'.$data['name'].'" class="form-control custom '.$data['name'].' valid-topic" value="'.$data['content_name'].'">
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2 icon-move-trash">
                                <a href="'.$edit_url.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                <i class="fa fa-arrows" aria-hidden="true"></i>
                                <i class="fa fa-trash remove-element pointer is_topic_delete" data-topic-id="'.$data['id'].'" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>';
			return $htm;
		} else {
			return '';
		}
	}
}

if (!function_exists('create_dynamic_option')) {
    /**
     * @desc Function to create dynamic option html element
     * @param type $data
     * @return html
     */
    function create_dynamic_option($option_counter,$type,$label,$data){
        $item_group = (int)$option_counter;
        $htm = '';
        $select_label=array("rd"=>"Number of Options","cb"=>"Number of Options","rate"=>"Number of Rating Items");
        $option_label=array("rd"=>"Select a content options size","cb"=>"Select a content options size","rate"=>"Select a Number of Rating Items");
        $option_size=array("rd"=>10,"cb"=>10,"rate"=>10);
		if(!empty($data)){

            foreach ($data as $option_sno=> $option) {
                $id = $option_counter + $option_sno;
                $htm .= '<div class="form-group" >
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="op_'.$id.'">'.$label." ".$option_sno.'</label>
                <div class="col-md-6 col-sm-6  col-xs-11">
                    <input id="op_'.$id.'"name="option_text_'.$type.'['.$item_group.'][]" class="form-control" value="'.$option["title"].'"/>
                </div>
            </div>';
            }
            return $htm;
		} else {
            $options='';
            for($i=1;$i<=$option_size[$type];$i++) {
                $options.='<option value='.$i.'>'.$i.'</option>';
            }
            return '
            <div class="form-group options"></div>              
                <div class="form-group radio-container">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">'.$select_label[$type].'</label>
                    <div class="col-md-6 col-sm-6 col-xs-6">
                        <select value="" class="form-control option" type='.$type.' label='.$label.' exercise_counter='.((int)$option_counter+1).'>
                            <option value="">'.$option_label[$type].'</option>
                            '. $options.'
                        </select>
                    </div>
            </div>';
		}
	}
}

if (!function_exists('create_dynamic_text_item')) {
    /**
     * @desc Function to create dynamic text_item html element
     * @param type $data
     * @return html
     */
    function create_dynamic_text_item($exerciseCounter,$items){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;
		if(!empty($items)){
            $options='';
            for($i=1;$i<=3;$i++) {
                $is_selected='';
                if($items['text_field_size']=='T_'.$i.'_LINE'){
                    $is_selected='selected';
                }

                $options.='<option value="T_'.$i.'_LINE" '.$is_selected.'>'.$i.' Line</option>';
            }

            $htm .= '<div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_'.$exerciseCounter.'" >Primary Prompt </label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="TEXT_ITEM" class="form-control custom hidden" />
                                        <input id="pp_'.$exerciseCounter.'" name="primary_prompt[]" class="form-control custom" value="'.$items['primary_prompt'].'"/>
                                    </div>
                                <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                    <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sp_'.$exerciseCounter.'" >Seconday Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sp_'.$exerciseCounter.'"  name="secondary_prompt[]" class="form-control custom" value="'.$items['secondary_prompt'].'"/>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Text Field Size</label>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <select name="text_field_size[]" class="form-control">
                                        '.$options.'
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>';
			return $htm;
		} else {
			return '';
		}
	}
}

if (!function_exists('create_dynamic_radio')) {
    /**
     * @desc Function to create dynamic radio html element
     * @param type $data
     * @return html
     */
    function create_dynamic_radio($exerciseCounter,$items){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;

		if(!empty($items)){
            $options='';

            $options=create_dynamic_option($itemCounter,'rd','Option Text',$items['options']);
            
            $htm .= '<div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_'.$exerciseCounter.'">Primary Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="RADIO" class="form-control custom hidden" />
                                    <input id="pp_'.$exerciseCounter.'" name="primary_prompt_rd[]" class="form-control custom" value="'.$items['primary_prompt'].'"/>
                                </div>
                                <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                    <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sp_'.$exerciseCounter.' ">Secondary Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sp_'.$exerciseCounter.'" name="secondary_prompt_rd[]" class="form-control custom" value="'.$items['secondary_prompt'].'"/>
                                </div>
                            </div>
                            '.$options.'
                        </div>     
                    </div>';
			return $htm;
		} else {
			return '';
		}
	}
}

if (!function_exists('create_dynamic_checkbox')) {
    /**
     * @desc Function to create dynamic text_item html element
     * @param type $data
     * @return html
     */
    function create_dynamic_checkbox($exerciseCounter,$items){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;
		if(!empty($items)){
            $options='';

            $options=create_dynamic_option($itemCounter,'cb','Option Text',$items['options']);
            
            $htm .= '<div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_'.$exerciseCounter.'">Primary Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="CHECKBOX" class="form-control custom hidden" />
                                    <input id="pp_'.$exerciseCounter.'" name="primary_prompt_cb[]" class="form-control custom" value="'.$items['primary_prompt'].'"/>
                                </div>
                                <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                    <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sp_'.$exerciseCounter.' ">Secondary Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sp_'.$exerciseCounter.'" name="secondary_prompt_cb[]" class="form-control custom" value="'.$items['secondary_prompt'].'"/>
                                </div>
                            </div>
                        </div>
                        '.$options.'
                    </div>';
			return $htm;
		} else {
			return '';
		}
	}
}
if (!function_exists('create_dynamic_two_col')) {
    /**
     * @desc Function to create dynamic two column html element
     * @param type $data
     * @return html
     */
    function create_dynamic_two_col($exerciseCounter,$items){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;
		if(!empty($items)){
            $options='';
            for($i=1;$i<=5;$i++) {
                $is_selected='';
                if($items['number_of_items']==$i){
                    $is_selected='selected';
                }
                $options.='<option value="'.$i.'" '.$is_selected.'>'.$i.'</option>';
            }
            $htm .= '<div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_'.$exerciseCounter.'">Primary Prompt </label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="TWO_COL" class="form-control custom hidden" />
                                        <input id="pp_'.$exerciseCounter.'" name="primary_prompt_col[]" class="form-control custom" value="'.$items['primary_prompt'].'" />
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fh_'.$exerciseCounter.' ">First Heading </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="fh_'.$exerciseCounter.'" name="first_head_col[]" class="form-control custom" value="'.$items['first_heading'].'"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sh_'.$exerciseCounter.' ">Second Heading </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sh_'.$exerciseCounter.'" name="second_head_col[]" class="form-control custom" value="'.$items['second_heading'].'"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Repeats</label>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <select value="" name="number_repeat_col[]" class="form-control">
                                    <option value="">Select a Number of Repeats</option>
                                        '.$options.'
                                    </select>
                                </div>
                            </div>

                        </div>  
                    </div>';
			return $htm;
		} else {
			return '';
		}
	}
}
if (!function_exists('create_dynamic_rating')) {
    /**
     * @desc Function to create dynamic rating html element
     * @param type $data
     * @return html
     */
    function create_dynamic_rating($exerciseCounter,$items){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;
		if(!empty($items)){
            $options='';
            $options_repeat='';
            for($i=1;$i<=5;$i++) {
                $is_selected='';
                if($items['number_of_items']==$i){
                    $is_selected='selected';
                }
                $options_repeat.='<option value="'.$i.'" '.$is_selected.'>'.$i.'</option>';
            }

            $options=create_dynamic_option($itemCounter,"rate","Rating Item",$items['options']);
            
            $htm .= '<div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fh_'.$exerciseCounter.'"> First Heading</label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="RATING" class="form-control custom hidden" />
                                        <input id="fh_'.$exerciseCounter.'" name="first_head_rate[]" class="form-control custom" value="'.$items['first_heading'].'"/>
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sh_'.$exerciseCounter.' ">Second Heading </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sh_'.$exerciseCounter.'" name="second_head_rate[]" class="form-control custom" value="'.$items['second_heading'].'" />
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Others Items</label>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <select class="form-control" value="" name="number_other_rate[]">
                                        <option value="">Select a Number of Others Items</option>
                                        '.$options_repeat.'
                                    </select>
                                </div>
                            </div>
                            
                            '.$options.'
                        </div>  
                    </div>';
			return $htm;
		} else {
			return '';
		}
	}
}

if(!function_exists('create_dynamic_goal')){
    /**
     * @desc Function to create dynamic goal html element
     * @param type $data
     * @return html
     */
    function create_dynamic_goal($exerciseCounter){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;
        $htm .= ' <div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                    <div class="form-group">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gl_'.$exerciseCounter.'"> Goal</label>
                            <div class="col-md-6 col-sm-6  col-xs-11">
                                <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="GOAL" class="form-control custom hidden" />
                            </div>
                            <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                            <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>  
                </div>';
        return $htm;
		
    }
}

if(!function_exists('create_dynamic_goal_tracking')){
    /**
     * @desc Function to create dynamic goal html element
     * @param type $data
     * @return html
     */
    function create_dynamic_goal_tracking($exerciseCounter){
        $htm = '';
        $itemCounter=(int)$exerciseCounter;
        $exerciseCounter=(int)$exerciseCounter+1;
        $htm .= ' <div id="set_'.$exerciseCounter.'" class="dynamic ui-state-default ui-sortable-handle">
                    <div class="form-group">
                        <div class="form-group">
                            <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gt_'.$exerciseCounter.'"> Goal Tracking</label>
                            <div class="col-md-6 col-sm-6  col-xs-11">
                                <input id="hidden_'.$exerciseCounter.'" name="items['.$itemCounter.']" value="GOAL_TRACKING" class="form-control custom hidden" />
                            </div>
                            <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                            <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>  
                </div>';
        return $htm;
    }
}
if (!function_exists('create_dynamic_resource')) {
    /**
     * @desc Function to create dynamic resources html element
     * @param type $data
     * @return html
     */
    function create_dynamic_resource($data){
		$htm = '';
		if(!empty($data['resources_id'])){
            $CI = & get_instance();
            $info_array = array('where' => array('id' => $data['resources_id']), 'table' => 'resources');
            $info_array['fields'] = 'resources.id,resources.type';
            $res_detail = $CI->db_model->get_data($info_array);
            if(!isset($res_detail['result'][0]['type'])){
				return '';
			}
            $info_array2['fields'] = '*';
            $info_array2['where'] = "resources.type='".$res_detail['result'][0]['type']."'"; 
            $info_array2['table'] = 'resources';
            $res_detail2 = $CI->db_model->get_data($info_array2);

            $reading = ''; $audio = ''; $video = ''; $website = ''; $opt = '';
            if ($res_detail['result'][0]['type'] == 'READING') {$reading = 'selected';}
            if ($res_detail['result'][0]['type'] == 'AUDIO') {$audio = 'selected';}
            if ($res_detail['result'][0]['type'] == 'VIDEO') {$video = 'selected';}
            if ($res_detail['result'][0]['type'] == 'WEBSITE') {$website = 'selected';}

            if(!empty($res_detail2['result'])){
                foreach ($res_detail2['result'] as $key => $options) {
                    $set_selected = '';
                    if($data['resources_id'] == $options['id']){
                        $set_selected = 'selected';
                    }
                    $opt .= '<option value="'.$options['id'].'"  '.$set_selected.'>'.$options['title'].'</option>'; 
                }
            }
            $htm .= '<div class="dynamic ui-state-default ui-sortable-handle">';
            $htm .= '<div class="form-group ui-sortable-handle">';
            $htm .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="Sub_topic_title">Select resource type<span class="required-asterisk">*</span></label>';
            $htm .= '<div class="col-md-6 col-sm-6  col-xs-11">';
            $htm .= '<select id="'.$data['resource_id_name_class'].'" class="form-control change_type valid-resource-type">';
            $htm .= '<option value="" style="display:none" selected="true">Select Resource Type</option>';
            $htm .= '<option value="READING" '.$reading.'>Reading</option>';
            $htm .= '<option value="AUDIO" '.$audio.'>Audio</option>';
            $htm .= '<option value="VIDEO" '.$video.'>Video</option>';
            $htm .= '<option value="WEBSITE" '.$website.'>Website</option>';
            $htm .= '</select></div><div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash"><i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i></div></div>';
            $htm .= '<div class="form-group ui-sortable-handle">';
            $htm .= '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="Sub_topic_title">Add Resource <span class="required-asterisk">*</span></label>';
            $htm .= '<div class="col-md-6 col-sm-6  col-xs-11">';
            $htm .= '<select name="'.$data['resource_id_name_class'].'" class="form-control '.$data['resource_id_name_class'].' valid-resource">'; 
            $htm .= $opt;
            $htm .= '</select></div></div></div>';
			return $htm;
		} else {
			return '';
		}
	}
}


if(!function_exists('image_upload')){
    /**
     * @desc Function upload single image
     * @param type $imgname
     * @return type
     */
	function image_upload($imgname) {
        $CI =& get_instance();
        $config_arr = $CI->config->item('assets_images');
	    $config = array(
	        'upload_path' => check_directory_exists($config_arr['path']),
	        'allowed_types' => "gif|jpg|png|jpeg",
	        'overwrite' => false
	    );
	    $CI->load->library('upload', $config);
	    $CI->upload->initialize($config);
	    if ($CI->upload->do_upload($imgname)) {
			$uploadimg = $CI->upload->data();
	        $uimg = $uploadimg['file_name'];

			$source = $config_arr['path'].'/'.$uimg;
            // optimize image

			$streamHandle = @fopen($source, 'r');
			//create a image resource from the contents of the uploaded image
			$resource = imagecreatefromstring(stream_get_contents($streamHandle));
      
			if ($resource) {
                //close our file stream
				@fclose($streamHandle);
                if ($uploadimg['file_type'] == 'image/jpeg' || $uploadimg['file_type'] == 'image/jpg'){
                    imagejpeg($resource, $source, 72);
                } elseif ($uploadimg['file_type'] == 'image/png') {
                    imagealphablending($resource, false);
                    imagesavealpha($resource,true);
                    imagepng($resource,$source, 7);
                } 
			}				

	        return $uimg;
	    } else {
	        return '';
	    }
	}
}

if ( ! function_exists('get_content_name')) {
    /**
     * @desc Function to get content name through id
     * @param type $id
     * @return type content_name
     */
    function get_content_name($id='')
    {
        $CI = & get_instance();
        $info_array = array('where' => array('id' => $id), 'table' => 'content');
        $info_array['fields'] = 'content.content_name';
        $res_detail = $CI->db_model->get_data($info_array);
        if(!empty($res_detail['result'])){

            return $res_detail['result'][0]['content_name'];
        } else {
            return '';
        }
    }
}

if ( ! function_exists('get_count')) {
     /**
     * @desc Function to get highest position value in records
     * @return type position
     */
    function get_highest_last_value($params=array())
    {
        $CI = & get_instance();
        extract($params);
 
        $info_array=array();
        $order_by  =$order_by??"content.position";
        $order = 'DESC';
        $info_array['limit'] = 1;
        $info_array['order_by'] = $order_by;
        $info_array['order'] = $order;
        $info_array['fields'] = $column??'content.position';
        $info_array['table'] = $table??'content';
        
        if(isset($where) && $where){
            $info_array['where'] = $where;  
            $res_detail = $CI->db_model->get_data($info_array);
        }else{
            $res_detail = $CI->db_model->get_data($info_array);
        }

        if(!empty($res_detail['result'])){
            return $res_detail['result'][0]['position'];
        } else {
            return 0;
        }
    }
}

if ( ! function_exists('get_content_for_link')) {

    function get_content_for_link($id = NULL)
    {
        $CI = & get_instance();
        $array_val = array();
        $info_array['fields']   = 'id,content_name,type,slug';  
        $info_array['table']    = 'content';
        $info_array['where']    = array('content.content_id' => NULL);
        
        $data_content           = $CI->db_model->get_data($info_array);
		$app_url                = $CI->config->item('app_url');
        $link_array_combined    = array();
        $content_arrr           = array();
        $topic_arrr             = array();
    
        // Fetching Chapters
        if(!empty($data_content['result'])){
            $content_menu   = array();
            $topic_menu     = array();
            $sub_topic_menu = array();
            foreach ($data_content['result'] as $key_content => $content_record) {
                $value = $app_url.'#/patient/'.$content_record['slug']; 
                $content_menu[]     = array('title' => $content_record['content_name'], 'value' => $value);

                // Fetching Topics 
                $info_array['where']    = array('content.content_id' => $content_record['id']);
                $data_topic             = $CI->db_model->get_data($info_array);
                if(!empty($data_topic['result'])){
                    foreach ($data_topic['result'] as $key_topic => $topic_record) {
                        $value          = $app_url.'#/patient/'.$content_record['slug'].'/'.$topic_record['slug'];
                        $topic_menu[]   = array('title' => $topic_record['content_name'], 'value' => $value);

                    }
                    $topic_arrr = array('title' => 'Topic', 'menu' => $topic_menu);
                }
            }
            $content_arrr = array('title' => 'Content', 'menu' => $content_menu);
        }
        $link_array_combined = array($content_arrr,$topic_arrr);//,$sub_topic_arrr
        return json_encode($link_array_combined);
    }
}

if ( ! function_exists('trim_video_link')) {
     /**
     * @desc Function to trim video link
     * @return type link
     */
     function trim_video_link($type,$link){
        if($type=='VIDEO'){
            $link=trim(explode('&',$link)[0]);
        }
        return trim($link);
    }
}


/* End of file site_helper.php */
/* Location: ./application/helpers/site_helper.php */

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

class Dashboard extends CI_Controller {
    
    /**
     * @desc Class Constructor
     */ 
    function __construct() {
        parent::__construct();
        if($this->session->userdata('logged_in')==FALSE){
			redirect('auth');
		}
		$this->load->model('Settings_model', 'settings');
		$this->load->model('Educational_model', 'educational');
		$this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }
    

    public function index() {
        // Set the title
        
        $this->template->title = 'Control Panel';
		$this->template->content->view('dashboard');
		// Publish the template
        $this->template->publish();
	}

	public function get_users(){ 
		$this->load->model('User_model', 'user');
		$data = $this->user->get_users_list($this->input->post());
		echo json_encode(array('status'=>'success','options'=>$data['result']));
		exit;
	}
}

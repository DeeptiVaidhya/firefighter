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

class Site extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->load->model('Resource_model', 'resource');
		$this->load->model('Auth_model', 'auth');
		$this->load->model('Settings_model', 'settings');
		$this->template->javascript->add(base_url('assets/js/bootstrap-switch.min.js'));
        $this->template->stylesheet->add(base_url('assets/css/bootstrap-switch.min.css'));
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    // Call list users function by default
    function index(){
       $this->site_setting();
    }

	// get site settings data
    public function site_setting()
	{   $data['settings']= $this->settings->get_site_data();
        $this->template->title = 'Site Setting';
        $this->template->content->view('site/site_setting', $data);
        $this->template->publish();
	}

	// change site settings 	
	public function change_setting(){
		echo json_encode($this->settings->change_setting($this->input->post()));
	}
    
}

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

class Resources extends CI_Controller {

    /**
     * @desc Class Constructor
     */
    function __construct() {
        parent::__construct();
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->load->model('Resource_model', 'resource');
        $this->form_validation->set_error_delimiters('<label class="error">', '</label>');
    }

    // Call list users function by default
    function index(){
        $this->list_resources();
    }

    /**
     * @desc Showing list of all users
     *
     */
    function list_resources() {
        // Set the title
        get_plugins_in_template('datatable');
		$this->template->title = 'List Resources';
        $data['res_type']=$this->input->post('type');
        
        $this->template->javascript->add(base_url('assets/js/bootstrap-switch.min.js'));
        $this->template->stylesheet->add(base_url('assets/css/bootstrap-switch.min.css'));
        $this->template->javascript->add(base_url('assets/js/sweetalert2.min.js'));
        $this->template->stylesheet->add(base_url('assets/css/sweetalert2.css'));
        $this->template->content->view('resources/list_resources',$data);

        // Publish the template
        $this->template->publish();
    }

    /**
     * Change Profile functionality
     */
    public function add_resources() {
        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('auth');
        }
        $this->breadcrumbs->push('Resources', 'resources');
        $this->breadcrumbs->push('Add a resource', ' ');
        $data['breadcrumb'] = $this->breadcrumbs->show();
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("resourcesForm"));
            
            if ($this->form_validation->run() != FALSE) {
				$request = $this->input->post();
                $request['files']=$_FILES;
        
				$result = $this->resource->update_resource($request);
				
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('resources/list-resources');
            }
        }
        $this->template->content->view('resources/add_resource', $data);
        $this->template->publish();
    }

    public function edit_resources($id) {
        
        $this->template->title = 'Edit Resource';
		$data['subheading'] = 'Edit a resource';
		$this->breadcrumbs->push('Resources', 'resources');
		$this->breadcrumbs->push('Edit a Resource', ' ');
		$data['breadcrumb'] = $this->breadcrumbs->show();
        $data['resource_detail'] = $this->resource->get_resource($id);
        if ($this->input->post()) {
            $this->form_validation->set_rules($this->config->item("resourcesForm"));
            if ($this->form_validation->run() != FALSE) {
				$request = $this->input->post();
				$request['files']=$_FILES;
				$result = $this->resource->update_resource($request);
				
                $this->session->set_flashdata($result['status'], $result['msg']);
                redirect('resources/list-resources');
            }
        }
        $this->template->content->view('resources/edit_resource.php',$data);
        
        // Publish the template
        $this->template->publish();
    }
    public function get_resources_data() {
		
		$data = $this->resource->get_resources_list($this->input->get());
        $rowCount = $data['total'];
        $output = array(
            "sEcho" => intval($this->input->get('sEcho')),
            "iTotalRecords" => $rowCount,
            "iTotalDisplayRecords" => $rowCount,
            "aaData" => []
        );
		$i = $this->input->get('iDisplayStart') + 1;
		$level = array('inquiry'=>' (Inquiry)','mood'=>' (Mood scale)');
        foreach ($data['result'] as $val) {
            $link = '<a id="edit" href="' . base_url('resources/edit-resources/' . $val['id']) . '" class="btn btn-tertiary-outline btn-sm" data-toggle="tooltip" data-placement="left" title="Edit">Edit</a>';
            $link .= '<a class="pointer" onclick="return confirmBox(\'Do you want to delete it ?\',\''.base_url("resources/delete-resources/" . $val["id"]).'\');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete"><i class="fa fa-trash"></i></a>';
            $output['aaData'][] = array(
                "DT_RowId" => $val['id'],
                $i++,
                ucfirst($val['title']),
                // ucfirst(strtolower($val['type'])).(isset($level[$val['level']]) ? $level[$val['level']] : ''),
                ucfirst(strtolower($val['type'])),
                $link
            );
        }
        echo json_encode($output);
        die;
    }

    public function delete_resources($id) {

        $result = $this->resource->delete_resources(array('table'=>'resources','where' => array('resources.id' => $id)));
        $this->session->set_flashdata($result['status'], $result['msg']);
        redirect('resources/list-resources');
    }
    
}

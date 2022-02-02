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


class Export extends CI_Controller
{

    public function __construct()
    {
		parent::__construct();
		$this->load->model('Auth_model', 'auth');
		$this->load->model('Educational_model', 'educational');
    }

	public function download_worksheet(){
		// check user for token, if valid token then download
		$token = $this->input->get('token');
		$fileId = $this->input->get('fid');
		$user_id = $this->auth->get_user($token);
		if (!$user_id) {
			show_error("You are not authorized to see the content.", 401, "Token required/expired!");
			exit;
		}
		$file=$this->educational->get_worksheet($fileId);
		
		if (isset($file->unique_name) && $file->unique_name) {
			$config = $this->config->item('assets_pdf');
			$filepath = $config['path'].'/'.$file->unique_name;
			// get file details
			// Process download
			if (file_exists($filepath)) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$file->name.'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($filepath));
				flush(); // Flush system output buffer
				readfile($filepath);
			}
		} else {
			show_error("Worksheet does not found on server.", 404, "File not found!");
		}
		exit;
	}
    
}
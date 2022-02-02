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
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Description of Settings_model
 *
 */
class Settings_model extends CI_Model {

    var $tables = array();

    public function __construct() {
        parent::__construct();
		$this->tables = array('site_settings'=>'site_settings');
    }
	
	
	/**
	 * get site settings data
	 */
	function get_site_data(){
		$query = $this->db->get('site_settings');
		return $query->result_array();
	}

	function change_setting($params = array()) {
		extract($params);
        $id = isset($id) ? $id : FALSE;
        $status = 'error';
        $msg = $this->lang->line('setting_change_error');
		$this->db->trans_start();		
        if ($id) {
            $this->db->update($this->tables['site_settings'], array('value' => $is_active), array('id' => $id));
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== FALSE) {
            $status = 'success';
            $msg = $this->lang->line('setting_change');
		}
        return array('status' => $status, 'msg' => $msg);
	}

}

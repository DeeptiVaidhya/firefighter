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

$config = array(
    'loginForm' => array(
        array(
            'field' => 'username',
            'label' => 'username',
            'rules' => 'required'
        ),
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required'
        ),
    ),
    'registerForm' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'required'
        ),
        array(
            'field' => 'user_type',
            'label' => 'User type',
            'rules' => 'required'
        )
    ),

    'resetPasswordForm' => array(
        array(
            'field' => 'password',
            'label' => 'password',
            'rules' => 'required|matches[confirm_password]|is_valid_password'
        ),
        array(
            'field' => 'confirm_password',
            'label' => 'confirm password',
            'rules' => 'required'
        ),
    ),
   
    'basicInfoForm' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'required'
        ),
	),

	'editParticipant' => array(
        array(
            'field' => 'first_name',
            'label' => 'first name',
            'rules' => 'required'
        ),
        array(
            'field' => 'last_name',
            'label' => 'last name',
            'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'required'
        ),
        array(
            'field' => 'subject_id',
            'label' => 'subject id',
            'rules' => 'required'
        )
    ),
    'contactUsForm' => array(
        array(
            'field' => 'name',
            'label' => 'Name',
            'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => 'email',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'message',
            'label' => 'message',
            'rules' => 'required'
        ),
    ),
    'resourcesForm' => array(
        array(
            'field' => 'title',
            'label' => 'Title',
            'rules' => 'required'
        ),
        array(
            'field' => 'description',
            'label' => 'Description',
            'rules' => 'required'
        )
    ),
    'addChapter' => array(
        array(
            'field' => 'chapter_name',
            'label' => 'Chapter Name',
            'rules' => 'trim|required'
        ),
        array(
            'field' => 'chapter_icon',
            'label' => 'Chapter Image',
            'rules' => 'required'
        )
    ),
    'chapterContent' => array(
        array(
            'field' => 'chapter_title',
            'label' => 'Chapter Title',
            'rules' => 'trim|required|max_length[50]'
        )
    ),
    'exerciseContent' => array(
        array(
            'field' => 'exercise_title',
            'label' => 'Exercise Title',
            'rules' => 'trim|required|max_length[50]'
        )
    ),
    'add_topic_content' => array(
        array(
            'field' => 'topic_title',
            'label' => 'Topic Title',
            'rules' => 'trim|required|max_length[50]'
        )
    ),
    'edit_topic_content' => array(
        array(
            'field' => 'topic_title',
            'label' => 'Topic Title',
            'rules' => 'trim|required|max_length[50]'
        )
    ),
    'addRegisterForm' => array(
        array(
        'field' => 'first_name',
        'label' => 'Name',
        'rules' => 'required'
        ),
        array(
            'field' => 'email',
            'label' => 'Email',
            'rules' => 'required|valid_email'
        ),
        array(
            'field' => 'country',
            'label' => 'Country',
            'rules' => 'required'
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required'
        )
    ) 
);
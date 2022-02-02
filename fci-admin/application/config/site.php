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
// Application URL to call from Emails
$config['app_url'] = 'http://pub1.brightoutcome-dev.com/firefighter/build/';
$config['assets_url'] = 'assets/';
// Default upload directories
$config['assets_images'] = array('path' => 'assets/uploads/images', 'allowed_types' => 'gif|png|jpg|jpeg', 'max_size' => '10000');
$config['assets_pdf'] = array('encrypt_name'=>true, 'path' => 'assets/uploads/pdf', 'allowed_types' => 'pdf', 'max_size' => '51200');
// AES-256 Encryption/Decryption
$config['encryption'] = array('cipher' => 'aes-256', 'mode' => 'CBC', 'driver' => 'openssl', 'key' => 'BE#1Hyp$ivhn$kT3xai1R^9UV!UF4DLk');

// Records Per page in Pagination
$config['pager_limit'] = 10;

$config['site_name']= 'FireFighter';

// Site Email Address
$config['site_contact_us_email'] = "firefighter@northwestern.edu";
// Contact Email Address
$config['contact_us_email'] = "sanjeet.agre@ideavate.com,rohan.verma@ideavate.com";
// From Email
$config['email_from_info'] = 'firefighter@northwestern.edu';
// Reply to Email
$config['email_reply_to_info'] = 'firefighter@northwestern.edu';

// Email send to this id as cc when a participant registers/password change request
$config['cc_email_sign_up'] = 'sanjeet.agre@brightoutcome.com';// 'firefighter@northwestern.edu';

// Auto logout time for login user if site is not accessed
$config['session_logout_time'] = 1800; // In seconds

// Auto Lock Out time for Locked Users on Wrong Password Entered.
$config['lockout_time'] = 10; // In minutes

// Mail templates
$config['expiration_note'] = 'Please note that the link will expire after %s hours. If you did not make this request, then you can safely ignore this email.';
$config['reset_password_subject'] = 'Password Change Request';
$config['reset_password_btn_titte'] = 'Reset password';
$config['reset_password_heading'] = 'Password Reset Request';
$config['reset_password_message'] = "<h2>Hi %s,</h2><p>We received a request to reset the password associated with this e-mail address. Please click the link below to start the password reset process.</p>";


$config['welcome_subject'] = 'Welcome to '.$config['site_name'];
$config['welcome_message'] = "<h2>Hello, %s!</h2><p>Your ".$config['site_name']." account has been created successfully!</p><p>You must now create a password to start using ".$config['site_name'].". Once you have created your password, you may then log in using the email addressed you signed up with.</p>";
$config['create_password_btn_titte'] = 'Create Password';
$config['create_password_heading'] = 'Welcome to '.$config['site_name'];


$config['contact_us_subject'] = 'New Inquiry';
$config['contact_us_message'] = "<p><strong>You have received a new message from the contact us form.</strong></p><p><strong>Name:</strong>  %s</p><p><strong>Email:</strong>  %s</p><p><strong>Telephone:</strong>  %s</p><p><strong>Message:</strong>  %s</p>";

// Completed Questionnaire Button Enable/Disable
$config['show_completed_questionnaire_button'] = FALSE;

$config['sftp_details'] = array('hostname' => 'macaw.brightoutcome.com', 'username' => 'mpguest', 'password' => '&wCU8$2QFx');
$config['sftp_assets_audios'] = array('url' => 'http://macaw.brightoutcome.com/fci/audio-ideavate/', 'path' => '/var/www/html/fci/audio-ideavate/', 'allowed_types' => 'mp3|m4a', 'max_size' => 512000);


$config['survey_message'] = '<h2>Dear, %1$s!</h2><p>Thank you for your participation in our study.  Please fill out our study questionnaires, using this link: <a style="color: #57417f;" href="%2$s">%2$s</a>. These questionnaires are very important, and will help us understand how '.$config['site_name'].' is working, and how we can improve the program. <p style="margin-top:30px;">Feel free to contact our study administrator: <a style="color: #57417f;" href="%3$s">%3$s</a> if you have any questions. Thanks again!</p></p>';
$config['survey_email_heading'] = 'Study Survey Reminder';
$config['survey_email_subject'] = $config['site_name'].' study survey reminder';
$config['survey_disable_contact_email'] = true;

$config['verify_email_btn_titte'] = 'VERIFY EMAIL';
$config['verify_email_subject'] = 'New user registration at '.$config['site_name'].' (pending approval)';
$config['verify_email_btn_titte'] = 'VERIFY EMAIL';
$config['verify_email_message'] = "<h2>Hi %s,</h2>You have successfully created a ".$config['site_name']." Account.There’s just one more step before you get started. Please click the button below to verify your email address and to activate your account.";
$config['verify_email_note'] = "Please note that the link will expire after %s hours. If you did not make this request, then you can safely ignore this email";
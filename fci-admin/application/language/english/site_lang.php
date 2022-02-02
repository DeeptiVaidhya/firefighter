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
/**
 * List of all messages shown in application.
 */
// Auth Model messages
$lang['not_access_application'] ="You are not authorized to access the application";
$lang['login_attempts_exceed']="This account is inactive due to many failed login attempts. Please try again after %s minute(s)"; // expiration time will be replace in %s
$lang['account_not_verified']="Your account is not verified yet. Please check your email inbox to verify your account.";
$lang['account_not_active'] ="Your account is not active";
$lang['account_logged_successfully']="Logged in successfully";
$lang['account_inactive']="This account is inactive due to many failed login attempts. Please try again after ";
$lang['not_valid'] = "Username or password is not valid";
$lang['error_while_register_user']="Error while register user";
$lang['unable_make_request']="Unable to make a request please try again later";
$lang['password_reset_link']="A link to reset your password has been sent. Please check your email.";
$lang['password_reset_successfully']="Password reset successfully.";
$lang['different_previous_password']="Your password must be different from the previous 6 passwords.";
$lang['profile_update_success']="Profile updated successfully";
$lang["user_detail_update_success"]="User detail updated successfully";
$lang['activate_account_error']="Error while activate account";
$lang['unable_send_request']="Unable to send a request please try again later";
$lang['thanks_for_your_message']="Thanks for your message! We will get back to you ASAP!";
$lang['user_not_found']="User not found.";
$lang['account_verified']="Your account has been verified successfully.";
$lang['account_added_success']="account added successfully.";
// Auth Controller messages'
$lang['forgot_password_link_expired']="Forgot password link expired";
$lang['forgot_password_link_invalid']="Forgot passowrd link is invalid";
$lang['emaill_link_invalid']="Verify email link is invalid";
$lang['email_exits']="Email already exits in system.";
$lang['link_generated_sent']="A link generated, and sent to";
$lang['invalid_request']="Invalid request";
// Auth API Controller messages 
$lang['email_not_exits']="Email does not exists in system";
$lang['username_exits']="Username already exits in system.";
$lang['user_update_success']="User updated successfully";
$lang['email_not_exixt']="Email address does not exist in system.";
$lang['logged_out']= "Logged out successfully.";
$lang['code_link_invalid']="Access code or link is invalid";
$lang['password_link_invalid']="Password link is invalid";
$lang['password_link_expired']="Forgot password link has been expired";
$lang['password_link_verified']="Forgot password link is verified";
$lang['week_not_started']="Week not started yet.";
//questionnaire model
$lang['response_save_success']="Response save successfully";
$lang['response_save_error']="Error while saving response";
$lang['week_detail_not_found']="No week details found";
$lang['email_sent_success']="Email is sent to user successfully.";
$lang['email_sent_fail']="Email is not sent to user.";

//questionnaire api controller
$lang['unauthorised_see_content']="You are unauthorised to see the content";

//educational model
$lang['chaptee_content_updated']="Chapter content updated";
$lang['topic_content_added']="Topic content addedd";
$lang['topic_content_updated']="Topic content updated";
$lang['record_deleted']="Record deleted";
$lang['record_not_deleted']="Unable to delete record";
$lang['record_not_found']="Record not found";
$lang['event_add_success']="Event add successfully";
$lang['event_not_added']="Event not added";
$lang['invalid_user']="Invalid user";
$lang['favorite_data_saving_error']="Error while saving favorite data";
$lang['invalid_content']="Invalid content";
$lang['save_content_error']="Unable to save content";
$lang['chapter_updated']="Chapter Updated";
$lang['chapter_name_added']="Chapter Name added";
$lang['chapter_detail_added']="Chapter Detail added";
$lang['status_updated_success']="Status updated";
$lang['status_updated_error']="Status not updated";

//educational api controller
$lang['not_authorized']="You are not authorized";
$lang['select_slug_id']="Please select type slug/id";
$lang['select_chapter_id']="Please select chapter";
$lang['chapter_not_available']="Chapters not available";
$lang['chapter_list']="Chapters list";
$lang['invalid_data']="Invalid data";
$lang['invalid_content']="Invalid content";
$lang['user_not_active']="User is not active!";
//educational  controller
$lang['chapter_content_not_exixt']="Chapter content does not exist";

//user model
$lang['user_info_update_success']="User information updated successfully.";
$lang['week_activated_success']="Weeks activated successfully.";
$lang['week_info_already_added']="Week info already added for user";
$lang['arm_update_error']="Error while updating user\'s arm";
$lang['arm_update_success']="User arm changed successfully.";
$lang['user_update_error']="Error while updating user";
$lang['user_activate_error']="Error while activating user";
//user controller
$lang['user_not_found']="User not found";
$lang['email_exixt']="Email address already exist in system";
$lang['status_updated']="Status updated.";
$lang['status_not_updated']="Status not updated";

$lang['user_unlock_success']="User unlocked successfully";
//user controller api

//resourse model
$lang['resourse_delete_success']="Resource deleted successfully.";
$lang['resourse_delete_error']="Unable to delete resource";
$lang['resourse_add_error']="Unable to add resource";
$lang['resourse_add_success']="Resource added successfully";
$lang['resourse_edit_success']="Resource edited successfully";
$lang['resourse_edit_error']="Unable to edit resource";


//settings model
$lang['setting_change']="Setting change successfully";
$lang['setting_change_error']="Error while changing setting";





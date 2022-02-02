<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3 class="">EDIT <?php echo $details['arm_alloted']; ?> PARTICIPANT</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="editParticipant" class="form-horizontal form-label-left" method="post">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">First Name</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="first_name" class="form-control" value="<?php echo isset($details['first_name'])?$details['first_name'] : set_value('first_name'); ?>">
                                        <?php echo form_error('first_name'); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Last Name</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="last_name" class="form-control" value="<?php echo isset($details['last_name'])?$details['last_name'] : set_value('last_name'); ?>">
                                        <?php echo form_error('last_name'); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Subject Id</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="subject_id" class="form-control" value="<?php echo isset($details['subject_id'])?$details['subject_id'] : set_value('subject_id');?>">
                                        <?php echo form_error('subject_id'); ?> 
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="email" name="email" class="form-control" value="<?php echo isset($details['email'])?trim($details['email']):set_value('email');?>">
                                        <?php echo form_error('email'); ?> 
                                    </div>
								</div>								
								<div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="phone_number" class="form-control" value="<?php echo isset($details['phone_number']) ? $details['phone_number'] : set_value('phone_number');?> "  />
                                        <?php echo form_error('phone_number'); ?> 
                                    </div>
								</div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Activation Date</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input readonly type="text" name="week_starts_at"  autocomplete="off" class="inpt-ui-datepicker form-control"  value="<?php echo isset($details['week_starts_at']) && $details['week_starts_at']?date('Y-m-d', strtotime($details['week_starts_at'])) : set_value('week_starts_at');?> " <?php echo ((!empty($details['week_starts_at'])) ? 'disabled' : ' ');?> />
                                    <?php echo form_error('week_starts_at'); ?> 
                                    </div>
								</div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Completion Date</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                    <input readonly type="text" name="week_complete_at"  autocomplete="off" class="form-control"  value="<?php echo isset($details['week_complete_at']) && $details['week_complete_at']?date('Y-m-d', strtotime($details['week_complete_at'])) : set_value('week_complete_at');?> " <?php echo ((!empty($details['week_complete_at'])) ? 'disabled' : ' ');?> />
                                    <?php echo form_error('week_complete_at'); ?> 
                                    </div>
								</div>

                                <input type="hidden" name="id" value="<?php echo isset($details['id']) ? $details['id'] : set_value('id');?> ">
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                        <button type="submit" class="btn btn-tertiary pull-right">Save</button>

										<a class="btn btn-tertiary pull-right cancel" href="<?php echo base_url('user/list_users/study')?>">Cancel</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>   
           <div class="col-xs-12 link-bg">
				<!-- <a class="small-link" href="<?php echo base_url()."user/participants-detail/".$details['id']."/events";  ?>"> Achievements & events</a>  -->
				<a class="small-link" href="<?php echo base_url()."user/participants-detail/".$details['id']."/time-spent";  ?>"> Time spent on site</a>
				<a class="small-link" href="<?php echo base_url()."user/participants-detail/".$details['id']."/chapters";  ?>"> Visited Pages</a>
				<a class="small-link" href="<?php echo base_url()."user/participants-detail/".$details['id']."/resources/study" ;  ?>">Accessed resources</a>				   
            </div>    
            <?php
                if(isset($type) ){
					// if(isset($type['events'])){
					// 	echo $this->template->partial->view('participant/achievements_events', $type);
					// }
					if(isset($type['chapters'])){
						echo $this->template->partial->view('participant/chapters', $type);
					}
					if(isset($type['exercises'])){
						echo $this->template->partial->view('participant/exercises', $type);
					}
					if(isset($type['resource'])){
						echo $this->template->partial->view('participant/resources', $type);
					}
					if(isset($type['time_spent'])){
						echo $this->template->partial->view('participant/week_session', $type);
					}
			  	}				
			?>
        </div>
    </div>
</div>
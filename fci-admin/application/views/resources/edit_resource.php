<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Edit a Resource</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="resourceForm" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $resource_detail[0]['id'];?>">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Resource Type</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select name="resource_type" class="form-control">
                                            <option value="READING" <?php if($resource_detail[0]['type'] == 'READING'){ echo 'selected';} ?>>Reading</option>
                                            <option value="AUDIO" <?php if($resource_detail[0]['type'] == 'AUDIO'){ echo 'selected';} ?>>Audio</option>
                                            <option value="VIDEO" <?php if($resource_detail[0]['type'] == 'VIDEO'){ echo 'selected';} ?>>Video</option>
                                            <option value="WEBSITE" <?php if($resource_detail[0]['type'] == 'WEBSITE'){ echo 'selected';} ?>>Website</option>
                                        </select>
                                        <!-- <input type="text" name="first_name" class="form-control" value="<?php echo set_value('first_name');?>"> -->
                                        <?php echo form_error('resource_type'); ?> 
                                    </div>
								</div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Title<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="title" class="form-control" value="<?php echo $resource_detail[0]['title'];?>" placeholder="Title" autocomplete="off">
                                        <?php echo form_error('title'); ?> 
                                    </div>
                                </div>
                                <div class="form-group external-link-wrapper <?php echo $resource_detail[0]['type'] == 'AUDIO' ? 'hidden' : ''?>">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">External Link<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="external_link" class="form-control" value="<?php echo $resource_detail[0]['link'];?>" placeholder="Link" autocomplete="off">
                                        <?php echo form_error('external_link'); ?> 
                                    </div>
                                </div>
								<div class="form-group audio-wrapper <?php echo $resource_detail[0]['type'] != 'AUDIO' ? 'hidden' : ''?>">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Upload Audio</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
										<input type="file" class="input-file" name="audio">
										<input type="hidden" name="files_id" value="<?php echo $resource_detail[0]['files_id'];?>" />

										<?php
											$config = $this->config->item('sftp_assets_audios');

											echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
											if($resource_detail[0]['files_id']){
												echo '<div class="col-sm-6 col-xs-12">'.get_file($resource_detail[0]['files_id'],false,$config['url']).'</div>';
											}
										?>
                                        <?php echo form_error('audio'); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Description<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <!-- <input type="text" name="email" class="form-control" value="<?php //echo set_value('email');?>"> -->
                                        <textarea name="description" class="form-control" placeholder="Description"><?php echo $resource_detail[0]['description'];?></textarea>
                                        <?php echo form_error('description'); ?> 
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                        <button type="submit" class="btn btn-tertiary pull-right">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



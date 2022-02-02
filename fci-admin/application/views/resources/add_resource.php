<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Add a Resource</h3>
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
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Resource Type</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <select name="resource_type" class="form-control">
                                            <option value="READING">Reading</option>
                                            <option value="AUDIO">Audio</option>
                                            <option value="VIDEO">Video</option>
                                            <option value="WEBSITE">Website</option>
                                        </select>
                                        <?php echo form_error('resource_type'); ?> 
                                    </div>
								</div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Title<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="title" placeholder="Title" class="form-control" value="<?php echo set_value('title');?>" autocomplete="off">
                                        <?php echo form_error('title'); ?> 
                                    </div>
                                </div>
                                <div class="form-group external-link-wrapper">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">External Link<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="external_link" placeholder="Link" class="form-control" value="<?php echo set_value('external_link');?>" autocomplete="off">
                                        <?php echo form_error('external_link'); ?> 
                                    </div>
								</div>
								<div class="form-group hidden audio-wrapper"> 
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Upload Audio<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
										<input type="file" class="input-file" name="audio">
										<?php
											$config = $this->config->item('sftp_assets_audios');
											echo '<p><small>Allowed type ( ' . str_replace('|', ', ', $config['allowed_types']) . ' )</small></br>';
										?>
                                        <?php echo form_error('audio'); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Description<span class="required-asterisk">*</span></label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <!-- <input type="text" name="email" class="form-control" value="<?php echo set_value('email');?>"> -->
                                        <textarea name="description" class="form-control" placeholder="Description"><?php echo set_value('description');?></textarea>
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



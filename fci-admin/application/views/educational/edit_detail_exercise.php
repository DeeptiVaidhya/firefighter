<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>(<?php echo ucfirst(strtolower($arm_type))?>) Edit an Exercise</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="exerciseContent" class="form-horizontal form-label-left submit_load" method="post" enctype="multipart/form-data">
                                
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Title<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="exercise_title" name="exercise_title" placeholder="Title" class="form-control" value="<?php if(set_value('exercise_title')) echo set_value('exercise_title'); else echo $exercise_content['exercise_title']; ?>" autocomplete="off" maxlength="50">
                                        <?php echo form_error('exercise_title'); ?> 
                                    </div>
                                    <span class="error_remaning_Exercise_title character-remaining-class"></span>
                                </div> 

                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub Header</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="sub_header" name="sub_header" rows="4" class="form-control" placeholder="Intro Text"><?php if(set_value('sub_header')) echo set_value('sub_header'); else echo $exercise_content['sub_header']; ?></textarea>
                                        <?php echo form_error('sub_header'); ?> 
                                    </div>
                                    <span class="error_remaning_sub_header character-remaining-class"></span>
                                </div>

                                <div id="set_0" class="textareaTiny ui-state-default ui-sortable-handle">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Description </label>
                                        <div class="col-md-7 col-sm-7 col-xs-10">
                                            <textarea class="form-control col-md-7 col-xs-12 text-tiny-mce" name="description" id=""><?php if(set_value('description')) echo set_value('description'); else echo $exercise_content['description']; ?></textarea>
                                            <?php echo form_error('description'); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group ui-sortable-handle worksheet_wrapper">
                                    <div class="form-group mb-20">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Worksheet Included </label>
                                        <div class="col-md-7 col-sm-7 col-xs-10">
                                            <div>
                                                <input type='hidden' id="worksheet_id"  value="<?php if(set_value('worksheet_id')) echo set_value('worksheet_id'); else echo $exercise_content['worksheet_id']; ?>" name="worksheet_id"/>
                                                <input type="checkbox" name="is_worksheet" <?php if(set_value('is_worksheet')) echo 'checked'; else echo $exercise_content['is_worksheet']?> /> 
                                                <p class="worksheet_warn <?php echo set_value('is_worksheet')?'hide':($exercise_content['is_worksheet']?'hide':'show') ?>">
                                                    <strong><small class="dark-red"><?php echo $exercise_content['worksheet_detail']['warn']??'' ?></strong></small>
                                                </p>
                                            </div>
                                            <div class="worksheet_file_wrapper <?php echo set_value('is_worksheet')?'show':($exercise_content['is_worksheet']?'show':'hide') ?>">
												<input class="input-file" type="file" name="worksheet_file"/>
												<?php echo form_error('worksheet_file'); ?>
                                                <p>
                                                    <small>
                                                        <?php if($exercise_content['worksheet_detail']) echo $exercise_content['worksheet_detail']['name']; else NULL; ?>
                                                    </small>
                                                </p>
											</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sortable-wrapper-exercise" id="sortable">
                                    <?php print_r($exercise_items_html)?>
                                </div>
                                
                                <div class="form-group dynamic-wrapper ui-sortable-handle">
                                    <div class="col-md-5 col-sm-6 col-xs-6">
                                        <select name="click_to_add" class="form-control click_to_add" data-target=".dynamic-exercise-add">
                                            <option value="">Select a content element type</option>
                                            <option value="TEXT_ITEM">Text Item</option>
                                            <option value="RADIO">RadioButton Item</option>
                                            <option value="CHECKBOX">Checkbox Item</option>
                                            <option value="TWO_COL">Two-Column Text Item</option>
                                            <option value="RATING">Rating Item</option>
                                            <option value="GOAL">Goals Item</option>
                                            <option value="GOAL_TRACKING">Goal Tracking Item</option>
                                        </select> 
                                    </div>
                                     <label class="control-label col-md-3 col-sm-6 col-xs-6 icon-add dynamic-exercise-add pointer" data-target=".sortable-wrapper-exercise" data-source_value=".click_to_add">
                                        <i class="fa fa-plus-circle"></i> <span class="bold">Click To Add</span>
                                     </label>
                                </div>
                                
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
										<input type="hidden" name="content_id" value="<?php echo $content_id;?>" />

                                        <a href="<?php echo base_url().'educational/get-exercise-list/'.strtolower($arm_type).'/'.$content_id?>". id="Exercise_detail_back" class="btn btn-light-primary pull-left btn-lg">Back</a>
                                        <button type="submit" id="Exercise_detail_submit" name="save" class="btn btn-tertiary pull-right btn-lg load">Save</button>
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


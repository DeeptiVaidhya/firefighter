<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>

</style>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>(<?php echo ucfirst(strtolower($arm_type))?>) Add an Exercise</h3>
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
                                        <input type="text" id="exercise_title" name="exercise_title" placeholder="Title" class="form-control" value="<?php echo set_value('exercise_title');?>" autocomplete="off" maxlength="50">
                                        <?php echo form_error('exercise_title'); ?> 
                                    </div>
                                    <span class="error_remaning_Exercise_title character-remaining-class"></span>
                                </div> 

                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Sub Header</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="sub_header" name="sub_header" rows="4" class="form-control" placeholder="Intro Text"><?php echo set_value('sub_header');?></textarea>
                                        <?php echo form_error('sub_header'); ?> 
                                    </div>
                                    <span class="error_remaning_sub_header character-remaining-class"></span>
                                </div>

                                <div id="set_1" class="textareaTiny ui-state-default ui-sortable-handle">
                                    <div class="form-group mb-20">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Description </label>
                                        <div class="col-md-7 col-sm-7 col-xs-10">
                                            <textarea class="form-control col-md-7 col-xs-12 text-tiny-mce" name="description"><?php echo set_value('description') ?></textarea>
                                            <?php echo form_error('description'); ?>
                                        </div>
                                    </div>
                                </div>

								<div class="form-group ui-sortable-handle worksheet_wrapper">
                                    <div class="form-group mb-20">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Worksheet Included </label>
                                        <div class="col-md-7 col-sm-7 col-xs-10">
                                            <div>
                                                <input type="checkbox" id="is_worksheet" name="is_worksheet" <?php echo set_value('is_worksheet')?'checked':'' ?>/>
                                            </div>
                                            <div class="worksheet_file_wrapper <?php echo set_value('is_worksheet')?'show':'hide' ?>">
												<input class="input-file" type="file" name="worksheet_file" id="worksheet_file" />
												<?php echo form_error('worksheet_file'); ?>
											</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sortable-wrapper-exercise" id="sortable"></div>
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

                                        <a href="<?php echo base_url().'educational/get-exercise-list/'.strtolower($arm_type).'/'.$content_id?>" id="Exercise_detail_back" class="btn btn-light-primary pull-left btn-lg">Back</a>
                                        <button type="submit" id="Exercise_detail_submit_addAnother" name="save_another" class="btn btn-tertiary pull-right btn-lg load">Save &amp; add another</button>
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

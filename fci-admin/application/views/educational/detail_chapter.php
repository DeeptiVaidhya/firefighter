<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<style>

</style>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>(<?php echo ucfirst(strtolower($arm_type))?>) Add a Chapter</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="chapterContent" class="form-horizontal form-label-left submit_load" method="post" enctype="multipart/form-data">
                                
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Chapter Title<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="chapter_title" name="chapter_title" placeholder="Title" class="form-control" value="<?php echo set_value('chapter_title');?>" autocomplete="off" maxlength="50">
                                        <?php echo form_error('chapter_title'); ?> 
                                    </div>
                                    <span class="error_remaning_chapter_title character-remaining-class"></span>
                                </div> 
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Icon<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-10">
                                        <input type="hidden" name="chapter_icon_hid" value="chapter_icon">
                                        <input class="input-file filebox" type="file" name="chapter_icon">
                                        <?php $config = $this->config->item('assets_images'); 
                                        echo '<p><small>Allowed type ( '.str_replace('|',', ', $config['allowed_types']).' )</small>';?>
                                    </div>
                                </div>

                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">First Paragraph Text</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="first_paragragh" name="first_paragragh" rows="4" class="form-control" placeholder="Intro Text"><?php echo set_value('first_paragragh');?></textarea>
                                        <?php echo form_error('first_paragragh'); ?> 
                                    </div>
                                    <span class="error_remaning_first_paragragh character-remaining-class"></span>                             
                                </div>
                                <div class="input_fields_wrap_one" id="sortable">
                                <div id="set_1" class="textareaTiny ui-state-default">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Paragraph Text </label>
                                        <div class="col-md-7 col-sm-7 col-xs-10">
                                            <textarea class="form-control col-md-7 col-xs-12 text-tiny-mce" name="paragraph_content_second" id=""><?php echo set_value('paragraph_content_second') ?></textarea>
                                            <?php echo form_error('paragraph_content_second'); ?>
                                        </div>
                                        <div class="col-md-2 col-sm-2 col-xs-2 icon-move-trash">
                                            <i class="fa fa-arrows" aria-hidden="true"></i>
                                            <i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                                <div id="set_2" class="image_content ui-state-default">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Image</label>
                                        <div class="col-md-6 col-sm-6 col-xs-10">
                                            <input type="hidden" name="image_hidden_1" value="image1">
                                            <input class="input-file filebox" type="file" name="image1">
                                            <?php $config = $this->config->item('assets_images'); 
                                            echo '<p><small>Allowed type ( '.str_replace('|',', ', $config['allowed_types']).' )</small>';?>
                                        </div>
                                        <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                            <i class="fa fa-arrows" aria-hidden="true"></i>
                                            <i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Image Credit</label>
                                        <div class="col-md-6 col-sm-6 col-xs-10">
                                            <input type="text" id="image_credit1" name="image_credit1" placeholder="Image Credit" class="form-control" value="<?php echo set_value('image_credit');?>" autocomplete="off">
                                            <?php echo form_error('image_credit'); ?> 
                                        </div>
                                    </div>
                                </div>
                                <div id="set_3" class="textareaTiny form-group ui-state-default">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Paragraph Text </label>
                                    <div class="col-md-7 col-sm-7 col-xs-10">
                                        <textarea class="form-control col-md-7 col-xs-12 text-tiny-mce" name="paragraph_content_third" id=""><?php echo set_value('paragraph_content_third') ?></textarea>
                                        <?php echo form_error('paragraph_content_third'); ?>
                                    </div>
                                    <div class="col-md-2 col-sm-2 col-xs-2 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i>
                                        <i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                                </div>
                                </div> 
                                <div class="form-group">
                                    <div class="col-md-5 col-sm-6 col-xs-6">
                                        <select name="click_to_add" class="form-control click_to_add" data-target=".dynamic-content-add">
                                            <option value="">Select a content element type</option>
                                            <option value="BODY">Body Text</option>
                                            <option value="IMAGE">Image</option>
                                            <option value="TOPIC">Topic</option>
                                        </select> 
                                    </div>
                                     <label class="control-label col-md-3 col-sm-6 col-xs-6 icon-add dynamic-content-add pointer">
                                        <i class="fa fa-plus-circle"></i> <span class="bold">Click To Add</span>
                                     </label>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                        <a href="<?php echo base_url() ?>educational/list-chapters/<?php echo strtolower($arm_type) ?>" id="chapter_detail_back" class="btn btn-light-primary pull-left btn-lg">Back</a>
                                        <button type="submit" id="chapter_detail_submit_addAnother" name="save_another" class="btn btn-tertiary pull-right btn-lg load">Save &amp; add another</button>
                                        <button type="submit" id="chapter_detail_submit" name="save" class="btn btn-tertiary pull-right btn-lg load">Save</button>
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
<div id="loading">
  <img id="loading-image" src="<?php echo assets_url('images/loading.gif') ?>" alt="Loading..." />
</div>
<script type="text/javascript">

</script>

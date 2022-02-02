<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>(<?php echo ucfirst(strtolower($arm_type))?>) Edit topic content<small>&nbsp;&nbsp;in Chapter '<?php echo $sub_heading; ?>'</small></h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="edit_topic_content" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="content_id" value="<?php echo $content_data_topic[0]['id'] ?>">
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Topic title<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="topic_title" name="topic_title" placeholder="Title" class="form-control" value="<?php if(set_value('topic_title')) echo set_value('topic_title'); else echo $content_data_topic[0]['content_name']; ?>" autocomplete="off" maxlength="50">
                                        <?php echo form_error('topic_title'); ?> 
                                    </div> 

                                    <span class="error_remaning_topic_title character-remaining-class"></span>
                                </div>
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">First Paragraph Text</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="first_paragragh" name="first_paragragh" rows="4" class="form-control" placeholder="Intro Text"><?php if(set_value('first_paragragh')) echo set_value('first_paragragh'); else echo $content_data_topic[0]['intro_text']; ?></textarea>
                                        <?php echo form_error('first_paragragh'); ?> 
                                    </div>
                                    <span class="error_remaning_first_paragragh character-remaining-class"></span>
                                </div>
                                <div class="input_fields_wrap_one" id="sortable">

                                <?php 
                                    if(!empty($htm)){
                                        print_r($htm);
                                    }
                                ?> 
                                
                                </div> 
                                <div class="form-group">
                                    <div class="col-md-5 col-sm-6 col-xs-6">
                                        <select name="click_to_add" class="form-control click_to_add" data-target=".dynamic-content-add">
                                            <option value="">Select a content element type</option>
                                            <option value="BODY">Body Text</option>
                                            <option value="IMAGE">Image</option>
                                            <option value="RESOURCE">Resource</option>
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
                                        <button type="submit" id="chapter_detail_submit" class="btn btn-tertiary pull-right btn-lg">Save</button>
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

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>(<?php echo ucfirst(strtolower($arm_type))?>) Edit a Chapter</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="chapterContent" class="form-horizontal form-label-left" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="content_id" value="<?php echo $content_data[0]['id'] ?>">
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Chapter Title<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">                                       
                                        <input type="text" id="chapter_title" name="chapter_title" placeholder="Title" class="form-control" value="<?php if(set_value('chapter_title')) echo set_value('chapter_title'); else echo $content_data[0]['title']; ?>" autocomplete="off" maxlength="50">
                                        <?php echo form_error('chapter_title'); ?> 
                                    </div> 
                                    <span class="error_remaning_chapter_title character-remaining-class"></span>
                                </div>                                 
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Icon<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-10">
                                        <input type="hidden" name="chapter_icon_hid" value="chapter_icon">
                                        <input type="hidden" name="chapter_icon_hid_edit_value" value="<?php echo $content_data[0]['icon_class']; ?>">
                                        <input class="input-file filebox" type="file" name="chapter_icon">
                                        <?php $config = $this->config->item('assets_images'); 
                                        echo '<p><small>Allowed type ( '.str_replace('|',', ', $config['allowed_types']).' )</small>';?>
                                        <?php
                                            $config = $this->config->item('assets_images');
                                            $upload_path = check_directory_exists($config['path']);
                                            // p($content_data);die;
                                            if($content_data[0]['icon_class'] != NULL){
                                        ?>
                                                <img class="img-responsive box-image" src="<?php echo base_url($upload_path . '/' . $content_data[0]['icon_class']) ?>" alt="image" title="image">
                                        <?php
                                            }
                                        ?>
                                    </div> 
                                </div>
                                <div class="form-group ui-sortable-handle">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">First Paragraph Text</label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <textarea id="first_paragragh" name="first_paragragh" rows="4" class="form-control" placeholder="Intro Text"><?php if(set_value('first_paragragh')) echo set_value('first_paragragh'); else echo $content_data[0]['intro_text']; ?></textarea>
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

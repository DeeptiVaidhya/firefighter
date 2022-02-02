<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $sub_heading; ?></h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <form id="addChapter" class="form-horizontal form-label-left" method="post">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Chapter Name<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" id="chapter_name" name="chapter_name" placeholder="Chapter Name" class="form-control" value="<?php echo isset($chapter_detail['content_name']) ? $chapter_detail['content_name'] : set_value('chapter_name'); ?>" autocomplete="off">
                                        <?php echo form_error('chapter_name'); ?> 
                                    </div>
                                    <span class="error_remaning_chapter_name character-remaining-class"></span>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Image<span class="required-asterisk">*</span></label>
                                    <div class="col-md-6 col-sm-6 col-xs-12">
                                        <input type="text" name="chapter_icon" placeholder="Icon Name" class="form-control" value="<?php echo isset($chapter_detail['icon_class']) ? $chapter_detail['icon_class'] : set_value('chapter_icon');?>" autocomplete="off">
                                        <?php echo form_error('chapter_icon'); ?> 
                                    </div>
                                </div>
                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                        <button type="submit" class="btn btn-pink pull-right btn-lg">Next</button>
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



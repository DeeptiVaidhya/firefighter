<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $sub_heading; ?><small>&nbsp;&nbsp;for Chapter <?php echo isset($content_data[0]['content_name']) ? "'".ucfirst($content_data[0]['content_name'])."'" : '--'; ?></small></h3>
                     <a id="resource_assign_link" class="btn btn-primary btn-lg pull-right" title="Assign a Resource" data-toggle="modal" data-target="#add_resource" data-content-id="<?php echo $content_id ?>">&nbsp;&nbsp;&nbsp;<b>Assign a resource</b> &nbsp;&nbsp;&nbsp;</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" >
                            <thead>
                                <tr><th colspan="2">Reading Resources from Content</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody>
								<?php
									$already_added = array_filter($side_resources['reading'],function($value){
										return isset($value['left_content']) && $value['left_content']==true;
									});
                                    if(!empty($already_added)){
										
                                        foreach ($already_added as $key => $reading) {
                                    ?>
                                        <tr>
                                            <td><?= ++$key ?></td>
                                            <td><em><?= !empty($reading['title']) ? $reading['title'] : '' ?></em></td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="2" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" >
                            <thead>
                                <tr><th colspan="2">Video Resources from Content</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['video'],function($value){
										return isset($value['left_content']) && $value['left_content']==true;
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $video) {
                                ?>
                                        <tr>
                                            <td><?= ++$key ?></td>
                                            <td><em><?= !empty($video['title']) ? $video['title'] : '' ?></em></td>
                                        </tr>
                                <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="2" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr><th colspan="2">Audio Resources from Content</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['audio'],function($value){
										return isset($value['left_content']) && $value['left_content']==true;
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $audio) {
                                    ?>
                                        <tr>
                                            <td><?= ++$key ?></td>
                                            <td><em><?= !empty($audio['title']) ? $audio['title'] : '' ?></em></td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="2" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" data-src="<?php echo base_url().'educational/get_content_data';?>" >
                            <thead>
                                <tr><th colspan="2">Website Resources from Content</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['website'],function($value){
										return isset($value['left_content']) && $value['left_content']==true;
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $website) {
                                    ?>
                                        <tr>
                                            <td><?= ++$key ?></td>
                                            <td><em><?= !empty($website['title']) ? $website['title'] : '' ?></em></td>
                                        
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="2" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
				</div>
				
				<div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table id="reading_tbl" class="table table-striped table-bordered" >
                            <thead>
                                <tr><th colspan="3">Additional Reading Resources</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                    <th width="30%">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['reading'],function($value){
										return !isset($value['left_content']) || !$value['left_content'];
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $reading) {
                                    ?>
                                        <tr data-club-id="<?php echo $reading['id'].'-'.$content_id ?>">
                                            <td><?= ++$key ?></td>
                                            <td><?= !empty($reading['title']) ? $reading['title'] : '' ?></td>
                                            <td>
                                                <i class="fa fa-arrows pointer" aria-hidden="true"></i>
                                                <a class="pointer" onclick="return confirmBox('Do you want to delete it ?', '<?php echo base_url("educational/delete-cources-has-res/" . $reading['id']) . '/' .$content_id ?>');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="3" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                    <!-- start table -->
                    <div class="table-responsive">
                        <table id="video_tbl" class="table table-striped table-bordered" >
                            <thead>
                                <tr><th colspan="3">Additional Video Resources</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                    <th width="30%">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['video'],function($value){
										return !isset($value['left_content']) || !$value['left_content'];
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $video) {
                                ?>
                                        <tr data-club-id="<?php echo $video['id'].'-'.$content_id ?>">
                                            <td><?= ++$key ?></td>
                                            <td><?= !empty($video['title']) ? $video['title'] : '' ?></td>
                                            <td>
                                                <i class="fa fa-arrows pointer" aria-hidden="true"></i>
                                                <a class="pointer" onclick="return confirmBox('Do you want to delete it ?', '<?php echo base_url("educational/delete-cources-has-res/" . $video['id']) . '/' . $content_id ?>');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="3" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                    <!-- start table -->
                    <div class="table-responsive">
                        <table id="audio_tbl" class="table table-striped table-bordered">
                            <thead>
                                <tr><th colspan="3">Additional Audio Resources</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                    <th width="30%">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['audio'],function($value){
										return !isset($value['left_content']) || !$value['left_content'];
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $audio) {
                                    ?>
                                        <tr data-club-id="<?php echo $audio['id'].'-'.$content_id ?>">
                                            <td><?= ++$key ?></td>
                                            <td><?= !empty($audio['title']) ? $audio['title'] : '' ?></td>
                                            <td>
                                                <i class="fa fa-arrows pointer" aria-hidden="true"></i>
                                                <a class="pointer" onclick="return confirmBox('Do you want to delete it ?', '<?php echo base_url("educational/delete-cources-has-res/" . $audio['id']) . '/' . $content_id ?>');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="3" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                    <!-- start table -->
                    <div class="table-responsive">
                        <table id="website_tbl" class="table table-striped table-bordered" data-src="<?php echo base_url().'educational/get_content_data';?>" >
                            <thead>
                                <tr><th colspan="3">Additional Website Resources</th></tr>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Title</th>
                                    <th width="30%">Manage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
									$already_added = array_filter($side_resources['website'],function($value){
										return !isset($value['left_content']) || !$value['left_content'];
									});
                                    if(!empty($already_added)){
                                        foreach ($already_added as $key => $website) {
                                    ?>
                                        <tr data-club-id="<?php echo $website['id'].'-'.$content_id ?>">
                                            <td><?= ++$key ?></td>
                                            <td><?= !empty($website['title']) ? $website['title'] : '' ?></td>
                                            <td>
                                                <i class="fa fa-arrows pointer" aria-hidden="true"></i>
                                                <a class="pointer" onclick="return confirmBox('Do you want to delete it ?', '<?php echo base_url("educational/delete-cources-has-res/" . $website['id']) . '/' . $content_id ?>');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                ?>
                                        <tr><td colspan="3" align="center"><i>No record found</i></td></tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Resource popup -->
<div class="modal fade" id="add_resource" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-body">
                <div class="row popup-assign">
                    <button type="button" class="close reload_current_page" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="col-xs-12 text-center">
                        <h3>Assign a resource</h3>
                    </div> 
                    <div class="col-xs-12"> 
                        <form method="post" name="assign-resource" id="assign-resource">
                            <input type="hidden" name="content_id" id="hidden_resource_assign_content_id" value="">
                            <div class="form-group">
                                <label>Resource type</label>
                                <select class="form-control input-lg change_type" id="resource_type" name="resource_type">
                                    <option value="" style="display:none" selected="true">Select Resource Type</option>
                                    <option value="READING">Reading</option>
                                    <option value="AUDIO">Audio</option>
                                    <option value="VIDEO">Video</option>
                                    <option value="WEBSITE">Website</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>List of Resources</label>
                                <select class="form-control input-lg resource_type" name="resource">

                                </select>
                            </div>
                            <button class="btn btn-primary btn-block btn-lg m-b-15 assign_resource" name="save_another">Save + Assign another</button>
                            <button class="btn btn-tertiary btn-block btn-lg assign_resource" name="save">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


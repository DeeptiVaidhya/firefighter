<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3><?php echo $subheading;?></h3>
                     <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('user/add-user/'.(!$arm_type?'researcher':$arm_type))?>">Add <?php echo (!$arm_type?'researcher':$arm_type.' participant') ?></a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" data-src="<?php echo $calling_url; ?>">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Full Name</th>
                                    <?php  if($arm_type){ ?>
										<th>Activation Date</th>
                                    <?php } else { ?>
										<th>Email</th> 
                                    <?php } ?>
                                    <?php  if($arm_type){ ?>
										<th>Subject Id</th>
                                    <?php } else if($is_researcher){  ?>
                                        <th>Phone</th>
                                     <?php } else { ?>
										<th>Role</th> 
                                    <?php } ?>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- start popup -->
<div class="modal fade" id="view_user" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <img class="img-circle" alt="" src="<?php echo assets_url('images/default-avatar.png') ?>">
                <h4 class="modal-title" id="exampleModalLabel"></h4>
            </div>
            <div class="modal-body"></div>
               <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


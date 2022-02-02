<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">

    <div class="row">

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Week Information</h3>
                    <a class="btn btn-default btn-sm pull-right" href="<?php echo site_url('user/list-users') ?>">Back</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive">
                            <thead>
                                <tr>
                                    <th>Week Number</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Email Status</th>
                                    <th>Email Send By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($weeks_detail)) {
                                    foreach ($weeks_detail as $val) {
                                        ?>
                                <tr>
                                    <td><b><?php echo $val['week_number'];?></b></td>
                                    <td><?php echo $val['start_date'];?></td>
                                    <td><?php echo $val['end_date'];?></td>
                                    <td><?php echo $val['is_email_sent'];?></td>
                                    <td><?php echo $val['fullname'];?></td>
                                </tr>

                                        <?php
                                    }
                                }else{?>
                                <tr><td colspan="5"><h5 class="text-center">No Result Found</h5></td></tr> 
                                    
                               <?php  }?>

                            </tbody>
                        </table>
                    </div>
                    <!--end table -->

                </div>
            </div>
        </div>
    </div>

</div>


<!--start popup -->
<div class = "modal fade" id = "view_user" role = "dialog" aria-labelledby = "exampleModalLabel">
    <div class = "modal-dialog" role = "document">
        <div class = "modal-content user-full-detail-popup">
            <div class = "modal-header">
                <button type = "button" class = "close" data-dismiss = "modal" aria-label = "Close"><span aria-hidden = "true">&times;
                    </span></button>
                <img class = "img-circle" alt = "" src = "<?php echo assets_url('images/default-avatar.png') ?>">
                <h4 class = "modal-title" id = "exampleModalLabel"></h4>
            </div>
            <div class = "modal-body">

            </div>
            <div class = "modal-footer">
                <button type = "button" class = "btn btn-default btn-sm" data-dismiss = "modal">Close</button>
            </div>

        </div>
    </div>
</div>


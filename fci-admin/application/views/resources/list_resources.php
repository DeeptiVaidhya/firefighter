<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Global Resources </h3>
                     <a class="btn btn-primary btn-sm pull-right" href="<?php echo site_url('resources/add-resources')?>">Add Resource</a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<!-- start table -->
					<div class="row">
						<div class="col-sm-6 col-xs-12">
							<form class="form-horizontal" method="post">
								<div class="form-group">
									<label>Resource type</label>
									<select class="form-control" name="type" onchange="this.form.submit()">
										<option value="">-- Select --</option>
										<option value="audio" <?php echo ($res_type=='audio'?'selected' : '');?>>Audio</option>
										<option value="reading" <?php echo ($res_type=='reading'?'selected' : '');?>>Reading</option>
										<option value="video" <?php echo ($res_type=='video'?'selected' : '');?>>Video</option>
										<option value="website" <?php echo ($res_type=='website'?'selected' : '');?>>Website</option>
									</select>
								</div>
							</form>
							
						</div>
					</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" data-src="<?php echo base_url().'resources/get-resources-data?type='.$res_type;?>">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Resource title</th>
                                    <th width="20%">Type</th>
                                    <th width="20%">Action</th>
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
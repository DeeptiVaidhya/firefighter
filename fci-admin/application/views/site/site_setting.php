<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Site Setting</h3>
                    <div class="clearfix"></div>
				</div>
				<div class="x-content">
				<div class="main table-responsive">
                        <table class="table table-striped table-bordered settings">
                            <thead>
                                <tr>
                                    <th width="5%">Sno</th>
                                    <th>Feature</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($settings) && !empty($settings)) {
									foreach ($settings as $key => $setting) { 
										$data = array('key'=>$setting['key'], 'id'=>$setting['id']);
										isset($setting['id']);
										?>
                                        <tr>
                                            <td><?php echo $key + 1 ;?></td>
                                            <td><?php echo str_replace("_"," ",$setting['key']);?></td>
											<td>
											<!-- <label class="onoffbtn"> -->
											<input type="checkbox" class="site_switch_btn" <?php echo (isset($setting['value']) && $setting['value']==1) ? 'checked':'' ?> data-params='<?php echo json_encode($data) ?>' data-name="<?php echo (isset($setting['value']) && $setting['value']==1) ? 'Active':'Inactive' ?>" data-url='<?php echo base_url()."site/change-setting" ;?>'/>
											<!-- </label> -->
											</td>
                                    <?php }
                                }else{ ?>
                                    <tr><td colspan="3" align="center">No data found</td></tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    </div> 			
				</div>
			</div>
		</div>
	</div>
</div>

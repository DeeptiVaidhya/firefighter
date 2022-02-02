<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            <?php if (isset($type) && !empty($type)) {
    $tr = '';
    ?>
                <div class="x_panel">
					<div class="x_title">
                        <h3>Accessed resources</h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">

						<?php if (isset($type['resource']) && !empty($type['resource'])) {?>

							<div class="table-responsive additional-participant-details">
								<table class="table table-striped table-bordered">
									<thead>
											<tr>
												<th >#</th>
												<th>Resource</th>
												<th>Exercise Type</th>
												<th>Number of Accesses</th>
												<th>Number of Completions</th>
											</tr>
									</thead>
									<tbody>
										<?php
										$index = 1;
										foreach ($type['resource'] as $resKey => $resources_data) {
											$tr .= '<tr>
																							<td>' . ($index++) . '</td>
																							<td>' . ($resources_data['title']) . '</td>
																							<td>' . ($resources_data['access_count']) . '</td>
																							<td>' . ($resources_data['is_completed']) . '</td>
																							<td></td>
																						</tr>';
										}
										if (empty($tr)) {
											$tr .= '<tr><td colspan="5" align="center">No record found</td></tr>';
										}
										echo $tr;
										?>
									</tbody>
								</table>
							</div>
						<?php }?>
					</div>
                </div>
            <?php }?>
        </div>
    </div>
</div>


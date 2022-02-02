<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">

            <?php if (isset($type) && !empty($type)) {
    $tr = '';
    ?>
                <div class="x_panel">
					<div class="x_title">
                        <h3>Time spent on site</h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">
						<div class="table-responsive additional-participant-details">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th width="5%">#</th>
										<th>Week</th>
										<th>Session start time (mm/dd/yyyy hh:mm)</th>
									</tr>
								</thead>
								<tbody>
									<?php
if (isset($type['time_spent']) && !empty($type['time_spent'])) {
        $index = 1;
        foreach ($type['time_spent'] as $key => $time) {
            $tr .= '<tr>
															<td>' . ($index) . '</td>
															<td> Week ' . ($key + 1) . '</td>
															<td>' . (empty($time['week_session']) ? "N/A" : "") . '</td>';
            if (isset($time['week_session']) && !empty($time['week_session'])) {
                $tr .= '<tr>
																				<th>#</th>
																				<th>Session Start Time</th>
																				<th>Session End Time</th>
																			</tr>';
                foreach ($time['week_session'] as $wKey => $week_session) {
                    $tr .= '<tr>
																					<td>' . ($wKey + 1) . '</td>
																					<td>' . (!empty($week_session['start_time']) ? (date('m/d/Y h:i:s a', strtotime($week_session['start_time']))) : "") . '</td>
																					<td>' . (!empty($week_session['end_time']) ? (date('m/d/Y h:i:s a', strtotime($week_session['end_time']))) : "") . '</td>
																		</tr>';
                }
                $tr .= '<tr>
																			<td>#</td>
																			<td>Total spent time(minutes)</td>
																			<td>' . $time['total_time_spent_in_week'] . ' (approx)</td>
																		</tr>';
            }
            '</tr>';
            $index++;
        }
    }
    if (empty($tr)) {
        $tr .= '<tr><td colspan="3"  align="center">No record found</td></tr>';
    }
    echo $tr;
    ?>
								</tbody>
							</table>
						</div>
					</div>
                </div>
            <?php }?>
        </div>
    </div>
</div>


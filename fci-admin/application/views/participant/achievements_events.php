<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="row">
	<div class="col-md-12 col-sm-12 col-xs-12">
		<?php if (isset($type) && !empty($type)) {
    $tr = '';
    ?>
			<div class="x_panel">
				<div class="x_title">
					<h3>Achievements & events</h3>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<form class="add-event-participant-details" action="<?php echo base_url('user/save-events') ?>" method="post">
						<div class="table-responsive additional-participant-details">
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th width="5%">#</th>
										<th>Week</th>
										<th>Award Level</th>
										<th>Event</th>
									</tr>
								</thead>
								<tbody>
									<?php
if (isset($type['events']) && !empty($type['events'])) {
        $index = 1;
        foreach ($type['events'] as $wKey => $week_award) {
			$tr .= '<tr>
						<td>' . ($index++) . '</td>
						<td>' . (($week_award['is_current_week']) == '1' ? '<strong> Week ' . $week_award['week_number'] . '</strong>' : 'Week ' . ($wKey + 1)) . ' </td>
						<td> Level ' . (($week_award['total_time_spent_in_week'] > 30 && $week_award['total_watched_video_audio'] > 4) ? "3" : ((($week_award['total_time_spent_in_week'] > 30 && $week_award['total_watched_video_audio'] >= 2) ? "2" : (($week_award['total_time_spent_in_week'] > 30) ? "1" : "0")))) . '</td>
						<td>
							<textarea class="form-control" name="events[]" placeholder="Enter Event" row="1">' . (isset($week_award['event']) ? $week_award['event'] : "") . '</textarea>
							<input type="hidden" name="weeks_id[]" value="' . $week_award['id'] . '"/>
						</td>
					</tr>';

        }
    }
    if (empty($tr)) {
        $tr .= '<tr><td colspan="5" align="center">No record found</td></tr>';
    }
    echo $tr;
    ?>
								</tbody>
							</table>
							<input type="hidden" name="user_id" value="<?php echo $details['id'] ?>"/>
							<button type="submit" class="btn btn-tertiary pull-right">Submit</button>
						</div>
					</form>
				</div>
			</div>
		<?php }?>
	</div>
</div>

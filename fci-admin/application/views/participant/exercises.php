<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <?php if (isset($type) && !empty($type)) {
    $tr = '';
    ?>
            <div class="x_panel">
                <div class="x_title">
                    <h3>
                        <?php echo isset($type['exercises']) ? "My Reflections" : ""; ?> </h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x-content">
                    <?php if (isset($type['exercises']) && !empty($type['exercises'])) {?>

                    <div class="table-responsive additional-participant-details">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <!-- <th>Week Number</th> -->
                                    <th>Chapter</th>
                                    <th>Resource</th>
                                    <th>Question</th>
                                    <th>Response</th>
                                    <th>Exercise Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
foreach ($type['exercises'] as $resKey => $resource) {

        $tr .= '<tr>
															<td>' . (isset($resource['current_week'])?($resource['current_week']) :$resKey + 1) . '</td>

															<td>' . (isset($resource['title']) ? $resource['title'] : 'After 8 Week Exercise') . '</td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>';

        if (!empty($resource['resources'])) {

            foreach ($resource['resources'] as $rKey => $resource_question) {

                $tr .= '<tr>
									<td>' . (isset($resource['current_week'])?($resource['current_week']) :$resKey + 1) . "." . ($rKey + 1) . '</td>

									<td></td>
									<td>' . ($resource_question['title']) . '</td>
									<td></td>
									<td></td>
									<td></td>


								</tr>';
                if (!empty($resource_question['questions'])) {

                    foreach ($resource_question['questions'] as $sKey => $resource_questions) {

                        $tr .= '<tr>
											<td>' . (isset($resource['current_week'])?($resource['current_week']) :$resKey + 1) . "." . ($rKey + 1) . "." . ($sKey + 1) . '</td>
											<td></td>
											<td></td>

											<td>' . (isset($resource_questions['short_question']) ? $resource_questions['short_question'] : 'Plan resource') . '</td>
											<td>' . ($resource_question['level'] == 'inquiry' ? $resource_questions['response'] : ($resource_questions['pre_rating'] ? $resource_questions['pre_rating'] : ($resource_questions['post_rating'] ? $resource_questions['post_rating'] : ($resource_questions['skip_question_post_rating_count'] ? 'skipped' : '')))) . '</td>
											<td>' . $resource_questions['created_at'] . '</td>
										</tr>';
                    }

                }

            }
        }

        // }

        // }
    }
        if (empty($tr)) {
            $tr = '<tr><td colspan="5" align="center">No record found</td></tr>';
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

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12"> 
            <?php if(isset($type) && !empty($type)) {
				$tr = '';
				?>
                <div class="x_panel"> 
					<div class="x_title">
                        <h3>Visited Pages</h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">
						<?php if( isset($type['chapters']) && !empty($type['chapters'])){ ?>
							<div class="table-responsive additional-participant-details">
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th width="5%">#</th>
											<th>Week Number</th>
											<th>Chapters Name</th>
											<th>Subtopic Name</th>
											<th>Average Access Time(mins)</th>
											<th>Total Time Spent(mins)</th>
											<th>Number of Access</th>
										</tr>	
									</thead>
									<tbody>
										<?php
											$tr = '';
											$index = 0;
											foreach($type['chapters'] as $key => $week) {
												$tr .= '<tr>
															<td>'.(++$index).'</td>
															<td>week '.($key).'</td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
															<td></td>
														</tr>';	
												$indexw = 0;
												foreach ($week as $key => $chapter) {
													$tr .= '<tr>
															<td>'.($index.".".++$indexw).'</td>
															<td></td>
															<td>'.($chapter['content_name']) .'</td>
															<td></td>
															<td>'.(round($chapter['TotalTimeSpentInMinutes']/$chapter['total'], 2)) .'</td>
															<td>'.($chapter['TotalTimeSpentInMinutes']).'</td>
															<td>'.($chapter['total']) .'</td>
														</tr>';	
													if(isset($chapter['subtopics'])){
														$indexs = 0;
														foreach($chapter['subtopics'] as $skey => $subtopic) {
															
															$tr .= '<tr>
																		<td>'.($index.".".$indexw.".".++$indexs).'</td>
																		<td></td>
																		<td></td>
																		<td>'.($subtopic['content_name']) .'</td>
																		<td>'.(round($subtopic['TotalTimeSpentInMinutes']/$subtopic['total'], 2)) .'</td>
																		<td>'.($subtopic['TotalTimeSpentInMinutes']) .'</td>
																		<td>'.($subtopic['total']) .'</td>
																	</tr>';	
														}	
													}
												}
											} 
											if(empty($tr)){
												$tr .= '<tr><td colspan="3" align="center">No record found</td></tr>';
											}	
											echo $tr;																			
										?>
									</tbody>
								</table>
							</div>		
						<?php	} ?>
							
					</div>	
                </div>  
            <?php }?> 
        </div>
    </div>
</div>


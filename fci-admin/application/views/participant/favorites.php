<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">    
            <?php if(isset($type) && !empty($type)) {
				$tr = '';
				?>
                <div class="x_panel"> 
					<div class="x_title">
                        <h3>My Favorites</h3>
                        <div class="clearfix"></div>
					</div>
					<div class="x-content">						
						<?php if( isset($type['favorites']) && !empty($type['favorites'])){ ?>
							<div class="table-responsive additional-participant-details">
								<table class="table table-striped table-bordered">
									<thead>
										<tr>
											<th width="5%">#</th>
											<th>Content Name</th>
										</tr>
									</thead>
									<tbody>
										<?php
											if(isset($type['favorites']['chapters']) && !empty($type['favorites']['chapters'])) {
												$index = 1;
												foreach( $type['favorites']['chapters'] as $favorite){								
													foreach($favorite['sub_topic'] as $key => $sub_topic) {
														$tr .= '<tr>
																	<td>'.($index++).'</td>
																	<td>'.($sub_topic['content_name']) .'</td>
																</tr>';	
													}
												}	
											} 
											if(empty($tr)){
												$tr .= '<tr><td colspan="2" align="center">No record found</td></tr>';
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


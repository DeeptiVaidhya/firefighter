<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $arm_type = strtolower($arm_type); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of exercises in <?php echo $arm_type?> content</h3>
                     <a class="btn btn-primary btn-lg pull-right" href="<?php echo site_url('educational/detail-exercise/'.$arm_type.'/'.$content_id)?>">Add Exercise &nbsp; <i class="fa fa-plus"></i></a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
						<table id="content_tbl" class="table table-striped table-bordered dt-responsive">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Title</th>
                                    <th width="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
								<?php
									$i=0;
                                    if($total<1){
                                         echo '<tr><td colspan="4" align="center">No content added</td></tr>';
                                    }
									foreach ($result as $val) {
                                       
                                        $li = '';
                                        $is_exercise = isset($val['is_exercise']);

                                        $method = 'edit-detail-exercise';
                                        $flow = '';
                                        
                                        $method.='/'.$arm_type;
                                        // $link = '<a id="edit" href="' . base_url('educational/'.$method.'/' . $val['id'] .'/'.$flow) . '" class="btn btn-tertiary btn-sm" data-toggle="tooltip" data-placement="left" title="Edit Content">Edit</a>';

                                        $link = '<a onclick="return confirmBox(\'Are you sure to edit the exercise detail it will cause participates data loss?\',\''.base_url('educational/'.$method.'/'. $val['id'] .'/'.$flow).'\',\''."Yes, edit it!".'\');" id="edit"  class="btn btn-tertiary btn-sm" data-toggle="tooltip" data-placement="left" title="Edit Content">Edit</a>';

                                        if(isset($val['is_exercise'])){ 
                                            $link .= '<i class="fa fa-arrows pointer" aria-hidden="true"></i>';
                                        }
                                        $link .= '<a class="pointer" onclick="return confirmBox(\'Do you want to delete it ?\',\''.base_url("educational/delete_exercise/".$arm_type ."/".$content_id."/". $val["id"]).'\');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>';
                                    
                                        if($is_exercise){
                                            $count = ++$i;
                                            $j =  0;
                                        } 
                                        else {
                                            $count = '';
                                        }
                                        echo '<tr class="'.($is_exercise ? 'parent' : '').' '.(!$is_exercise ? 'child hidden' : '').'" data-exercise='.$val['exercise'].' data-parent-id='.$val['id'].' data-parent-table="exercises">'.
                                                '<td>'.$count.'</td>'.
                                                '<td class="'.($is_exercise ? ' light-purple' : '').'">'.ucfirst($val['exercise_title']).'</td>'.
                                                '<td>'.$link.'</td>'.
                                            '</tr>';
                                    
									}
									
								?>
                            </tbody>
                        </table>
                    </div>             
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Resource popup -->
<div class="modal fade" id="add_resource" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content user-full-detail-popup">
            <div class="modal-body">
                <div class="row popup-assign">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="col-xs-12 text-center">
                        <h3>Assign a resource</h3>
                    </div> 
                    <div class="col-xs-12"> 
                        <form method="post" name="assign-resource" id="assign-resource">
                            <input type="hidden" name="content_id" id="hidden_resource_assign_content_id" value="">
                            <div class="form-group">
                                <label>Resource type</label>
                                <select class="form-control input-lg change_type" id="resource_type" name="resource_type">
                                    <option value="" style="display:none" selected="true">Select Resource Type</option>
                                    <option value="READING">Reading</option>
                                    <option value="AUDIO">Audio</option>
                                    <option value="VIDEO">Video</option>
                                    <option value="WEBSITE">Website</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>List of Reading Resources</label>
                                <select class="form-control input-lg resource_type" name="resource">

                                </select>
                            </div>
                            <button class="btn btn-pink btn-block btn-lg m-b-15 assign_resource" name="save_another">Save + assign another</button>
                            <button class="btn btn-pink-outline btn-block btn-lg assign_resource" name="save">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


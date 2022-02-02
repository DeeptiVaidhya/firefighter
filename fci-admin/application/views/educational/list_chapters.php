<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $arm_type = strtolower($arm_type); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of <?php echo $arm_type?> content</h3>
                     <a class="btn btn-primary btn-lg pull-right" href="<?php echo site_url('educational/detail-chapter/'.$arm_type)?>">Add Chapter &nbsp; <i class="fa fa-plus"></i></a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
						<table id="content_tbl" class="table table-striped table-bordered dt-responsive">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="40%">Content Name</th>
                                    <!-- <th width="15%">Status</th> -->
                                    <th width="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
								<?php
									$i=0;
									foreach ($result as $val) {
										$li = '';
										$is_chapter = isset($val['is_chapter']);
										$is_topic = isset($val['is_topic']);
										$is_sub_topic = isset($val['is_sub_topic']);

										if($is_topic){
											$method = 'edit-topic-content';
										} else if ($is_sub_topic){
											$method = 'edit-sub-topic-content';
										} else {
											$method = 'edit-detail-chapter';
											$flow = '';
										}
										$method.='/'.$arm_type;
										$link = '<a id="edit" href="' . base_url('educational/'.$method.'/' . $val['id'] .'/'.$flow) . '" class="btn btn-tertiary btn-sm" data-toggle="tooltip" data-placement="left" title="Edit Content">Edit</a>';
                            
										if(isset($val['is_sub_topic']) || isset($val['is_chapter'])){
											$link .= '<a id="resource" href="' . base_url('educational/get-side-resources/' . $val['id']) . '" class="btn btn-tertiary btn-sm pointer" data-placement="left" title="Assign Resource">Resources</a>';
                                        }
                                        
                                        if(isset($val['is_chapter']) && $arm_type!='control'){
                                            $link .= '<a id="exercise" href="' . base_url('educational/get-exercise-list/' .$arm_type.'/'. $val['id']) . '" class="btn btn-tertiary btn-sm pointer" data-placement="left" title="List Exercises">Exercises</a>';
                                        }

                                        if(isset($val['is_chapter'])){ 
                                            $link .= '<i class="fa fa-arrows pointer" aria-hidden="true"></i>';
                                        }
										$link .= '<a class="pointer" onclick="return confirmBox(\'Do you want to delete it ?\',\''.base_url("educational/delete-content/".$arm_type ."/" . $val["id"]).'\');" id="edit" data-toggle="tooltip" data-placement="right" title="Delete Record"><i class="fa fa-trash"></i></a>';
									
										if($is_chapter){
											$count = ++$i;
											$j =  0;
										} else if($is_topic) {
											$count = $i .'.'. ++$j;
											$k = 0;
										} else if($is_sub_topic) {
											$count = $i .'.'. $j.'.'. ++$k;
										} else {
											$count = '';
                                        }
										echo '<tr class="'.($is_chapter ? 'parent' : '').' '.(!$is_chapter ? 'child hidden' : '').'" data-chapter='.$val['chapter'].' data-parent-id='.$val['id'].'>'.
												'<td>'.$count.'</td>'.
												'<td class="'.(!$is_sub_topic?'bold':'').($is_chapter ? ' light-purple' : '').'">'.ucfirst($val['content_name']).($is_chapter && !(isset($val['no_records']) && $val['no_records'])?'<span class="iconToggle pointer fa fa-plus pull-right toggle-chapters"></span>':'').'</td>'.
												'<td>'.$link.'</td>'.
											  '</tr>';
									}
									if(empty($result)){
										echo '<tr><td colspan="4" align="center">No content added</td></tr>';
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


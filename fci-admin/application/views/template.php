<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $this->template->title->default($this->config->item('site_name')); ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo $this->template->description; ?>">
		<link rel="icon" type="image/x-icon" href="<?php echo assets_url('images/favicon.ico'); ?>">
        <meta name="author" content="">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap-select.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/toastr.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/jquery-ui.min.css'); ?>"/>
        <!-- Theme Style -->
        <link href="<?php echo assets_url('css/theme.css')?>" rel="stylesheet"/>
        <link href="<?php echo assets_url('css/admin.css')?>" rel="stylesheet"/>
        
        
        <?php echo $this->template->meta; ?>
        <?php echo $this->template->stylesheet; ?>
    </head>
    <body class="nav-md">
        <div class="main_container">
            <div class="container body">
                <div class="col-md-3 left_col">
                    <?php 
                        // This is an example to show that you can load stuff from inside the template file
                        echo $this->template->widget("leftsection", array('base_url' => base_url()));
                    ?>
                </div>
                <div class="top_nav">
                    <?php 
                        // This is an example to show that you can load stuff from inside the template file
                        echo $this->template->widget("header", isset($breadcrumb) ? $breadcrumb : '');
                    ?>
                </div>
                <div class="right_col" role="main">
                    <?php
                        // This is the main content partial
                        echo $this->template->content;
                    ?>
                </div>
                <footer>
                    <p>
                        All rights reserved - <?php echo $this->config->item('site_name'); ?> &copy; <?php echo date('Y');?>
                    </p>
                </footer>

            </div>
        </div>
        
        <script>var BASE_URL="<?php echo base_url();?>";</script>
        <script>var CONTENT_LINKS =<?php echo get_content_for_link(); ?>;</script>
        
        <script src="<?php echo assets_url('js/jquery-3.4.1.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootstrap-select.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/theme.js'); ?>"></script>
        <script src="<?php echo assets_url('js/toastr.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/bootbox.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/jquery.validate.js'); ?>"></script>
		<script src="<?php echo assets_url('js/form_validation.js'); ?>"></script>
		<script src="<?php echo assets_url('js/jquery-ui.min.js'); ?>"></script>
		<script src="<?php echo assets_url('js/admin.js'); ?>"></script>
		<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@7.1.0/dist/promise.min.js"></script>


        <script type="text/javascript">
            $(document).ready(function(){
                toastr.options = {closeButton: true}
                var success = '<?php echo $this->session->flashdata("success");?>',
                    error = '<?php echo $this->session->flashdata("error");?>';
                if(success!=''){
                    toastr.success(success);
                } else if(error!=''){
                    toastr.error(error); 
                }

				$("body").tooltip({ selector: '[data-toggle=tooltip]' });
				
				$("#sub-topic-sortable").sortable({
                    items: 'div.form-group',
                    cursor: 'pointer',
                    axis: 'y',
                    dropOnEmpty: false,
                    start: function(e, ui) {
                        ui.item.addClass("selected");
                    },
                    stop: function(e, ui) {
                        var arr = [];

                        $(this).find("div.form-group").each(function(index) {
                            if (index >= 0) {
                                if ($(this).find('[name^="hidden_sub_topic_title_edit"]').length) {
                                    var content_id = $(this).find('[name^="hidden_sub_topic_title_edit"]').val();

                                    var param = { 'content_id': content_id, 'order': index }
                                    var htm = '';
                                    $.ajax({
                                        url: BASE_URL + 'educational/reorder-position-chapters',
                                        dataType: 'json',
                                        method: 'POST',
                                        data: param,
                                        success: function(result) {
                                            // no action performed
                                        }
                                    });
                                }
                            }
                        });
                    }
                });
            });

            function confirmBox(msg, url,cfrmBtnTxt='Yes, delete it!',clBtnTxt='Cancel',ajax=false) {

                console.log('url',url);
                Swal.fire({
                    title: msg,
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: cfrmBtnTxt,
                    cancelButtonText: clBtnTxt,
                    closeOnConfirm: false,
                    closeOnCancel: false
                    
                }).then(function(result){
					if (result.value && ajax) {
                        $.ajax({
                            url:  url,
                            method: 'get',
                            success: function (res) {
                                let result = JSON.parse(res);
                                if (result.status == 'success') {
                                    toastr.success("Email has been sent to user.");
                                } else {
                                    toastr.error("Error while geting users.");
                                }
                            }
                        });
                    }else if(result.value){
                        window.location.href = url;
                    } else {
                        return false;
                    }
				})
			}
        </script>
       
        <?php echo $this->template->javascript; ?>
    </body>
</html>



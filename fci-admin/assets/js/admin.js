(function ($) {
    $(function () {
        $('#resourceForm').on('change', '[name="resource_type"]', function () {
            if (this.value == 'AUDIO') {
                $('.external-link-wrapper').addClass('hidden');
                $('.audio-wrapper').removeClass('hidden');
            } else {
                $('.external-link-wrapper').removeClass('hidden');
                $('.audio-wrapper').addClass('hidden');
            }
        });
        $(".inpt-ui-datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: new Date(),
            onClose: function () {
                $(this).valid();
            } // save date format in sql 
        });
        // function for geting participants for specific arm 
        function get_arm_users(self) {
            $.ajax({
                url: $(self).attr('data-url'),
                method: 'POST',
                dataType: 'json',
                data: { 'arm': self.value, 'is_active': true }, // must be json
                success: function (result) {
                    if (result.status == 'success') {
                        $('#participants>tbody').empty();
                        $(result.options).each(function (ind, opt) {
                            $('#participants tbody').append("<tr><td><input type='checkbox' name='participants[]' value= '" + opt.user_id + "'/> </td><td>" + opt.last_name + ", " + opt.first_name + "</td><td>" + opt.email + "</td><td>" + opt.phone_number + "</td></tr>");
                        });
                    } else {
                        toastr.error("Error while geting users.");
                    }
                }
            });
        }

        $(document).ready(function () {
            if ($('.site_switch_btn').length) {
                $('.site_switch_btn').bootstrapSwitch({
                    onText: "Enable",
                    offText: "Disable",
                    size: "mini",
                    onColor: 'primary',
                    offColor: 'danger'
                }).on('switchChange.bootstrapSwitch', function (event, state) {
                    var param = $.parseJSON($(this).attr('data-params'));
                    param.is_active = (state) ? 1 : 0;
                    $.ajax({
                        url: $(this).attr('data-url'),
                        method: 'POST',
                        data: param, // must be json
                        success: function (res) {
                            var result = $.parseJSON(res);
                            var status = result.status;
                            toastr.options = { closeButton: true }
                            if (status == 'success') {
                                toastr.success(result.msg);
                            } else {
                                toastr.error(result.msg);
                            }
                        }
                    });
                });
            }
        });

        if ($.fn.dataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
                oLanguage: {
                    sProcessing: "<div class='loader-center'><img height='50' width='50' src='" + BASE_URL + "assets/images/loading.gif'></div>"
                },
                bProcessing: true,
                bServerSide: true,
                ordering: true,
                iDisplayLength: 30,
                responsive: true,
                bSortCellsTop: true,
                bDestroy: true, //!!!--- for remove data table warning.
                aLengthMenu: [
                    [30, 60, 90, -1],
                    [30, 60, 90, "All"]
                ],
                aoColumnDefs: [

                    {
                        bSortable: false,
                        aTargets: [1],
                    },
                    {
                        bSortable: false,
                        aTargets: [-1],
                    }
                ],
                searching: false,
                paging: true,
                drawCallback: function (settings) {
                }
            });

            if ($('.data-table').length) {
                $('.data-table').each(function () {
                    var opts = {};
                    // var obj = $(this);
                    if ($(this).attr('data-src')) {
                        opts['sAjaxSource'] = $(this).attr('data-src');

                    } else if ($(this).attr('data-opts')) {
                        $.extend(opts, $.parseJSON($(this).attr('data-opts')));
                    }
                    // var classes_id = $(this).attr('data-classes_id');
                    // var course_id = $(this).attr('data-course_id');
                    if ($(this).attr('data-server_params')) {
                        var sparam = $.parseJSON($(this).attr('data-server_params'));
                        opts["fnServerParams"] = function (aoData) {
                            $(sparam).each(function () {
                                aoData.push(this);
                            });
                        }
                    }
                    $(this).DataTable(opts);
                });
            }
        }


        $(document).on('click', '.delete', function () { // Remove more item functionality
            var that = this,
                msg = $(that).attr('data-msg');
            if ($(that).attr('data-url')) { // If want to remove from server
                bootbox.confirm({
                    message: "Are you sure you want to delete this " + msg + "?",
                    callback: function (result) {
                        if (result) {
                            window.location.href = $(that).attr('data-url');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.user_detail', function () {
            $.ajax({
                url: $(this).attr('data-url'),
                dataType: 'json',
                method: 'POST',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function (result) {

                    if (result.status == 'success') {
                        $('#view_user').find('.modal-title').text(result.data.first_name + ' ' + result.data.last_name);
                        if (result.data.profile_picture != null) {
                            $('#view_user').find('.img-circle').attr('src', result.data.profile_picture);
                        }
                        var content = "<div class='row'>";

                        var obj = { first_name: 'First name', last_name: 'Last name', email: 'Email', role: 'Role', gender: 'Gender' };
                        $.each(result.data, function (i, v) {
                            if (obj[i] != undefined && v != null) {
                                content += '<div class="row"><div class="col-md-12"><div class="control-label col-md-3 col-sm-3 col-xs-12">' +
                                    '<h5>' + obj[i] + '</h5></div>';
                                content += '<div class = "col-md-9 col-sm-9 col-xs-12" ><h5>' + v + '</h5></div></div></div>';
                            }
                        });
                        content += "</div>";
                        $('#view_user').find('.modal-body').html(content);
                        // $('body').on('focus',".datepicker_recurring_start", function(){
                        // 	$(this).datepicker();
                        // });
                    }
                }
            });
        });


        $(document).on('click', '.ajax-call', function () {
            var isReload = $(this).attr('data-is-reload');
            $.ajax({
                url: $(this).attr('data-url'),
                dataType: 'json',
                method: 'POST',
                data: $.parseJSON($(this).attr('data-params')), // must be json
                success: function (result) {
                    toastr.options = { closeButton: true }
                    if (result.status == 'success') {
                        toastr.success(result.msg);
                    } else {
                        toastr.error(result.msg);
                    }
                    setTimeout(function () {
                        if (result.status == 'success') {
                            isReload == 'true' && window.location.reload();
                        }
                    }, 2000);
                }
            });
        });

        function initTinymce(elem) {
            if (typeof (tinymce) != "undefined") {
                tinymce.init({
                    selector: elem,
                    menubar: false,
                    statusbar: false,
                    height: "300",
                    plugins: 'hr pagebreak nonbreaking anchor lists textcolor wordcount  imagetools  colorpicker textpattern table link',
                    toolbar1: 'formatselect | bold italic forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | bullist outdent indent  | removeformat | fontsizeselect | image | table',
                    link_list: CONTENT_LINKS,
                    images_upload_url: BASE_URL + '/homework/text-image',
                    fontsize_formats: "8px 10px 12px 14px 18px 24px 36px",
                    automatic_uploads: false,
                    image_advtab: true,
                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,
                    anchor_top: false,
                    anchor_bottom: false,
                    images_upload_handler: function (blobInfo, success, failure) {
                        var xhr, formData;
                        xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', BASE_URL + '/homework/text-image');
                        xhr.onload = function () {
                            var json;
                            if (xhr.status != 200) {
                                failure('HTTP Error: ' + xhr.status);
                                return;
                            }
                            json = JSON.parse(xhr.responseText);
                            success(json.file_path);
                        };
                        formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    },
                    setup: function (editor) {
                        editor.on('change', function (e) {
                            $(this).closest('error').html('');
                            tinymce.triggerSave();
                            $("#" + editor.id).valid();
                        });
                    }
                });
            }
        }

        if ($('.text-tiny-mce').length) {
            initTinymce('.text-tiny-mce');
        }


        var wrapper = $(".input_fields_wrap_one"); //Fields wrapper
        var click_to_add = $(".click_to_add"); //Add button ID
        var click_to_add_line = $(".click_to_add_line"); //Add button ID

        var topicCounter = 1;
        var bodyCounter = 1;
        var imageCounter = 1;
        var overAll = 4;
        var resourceCounter = 1;
        var htm = '';

        $(".dynamic-content-add,.dynamic-exercise-add").prop('disabled', true).addClass('no-drop');

        $(document).on('change', '.click_to_add', function () {
            var target = $($(this).attr('data-target'));
            target.prop('disabled', false).addClass('pointer').removeClass('no-drop');
            target.find('i,span').addClass('c-pink');

        }).on('click', '.dynamic-content-add', function (e) {
            // e.preventDefault();
            value_type = click_to_add_line.val() || click_to_add.val();

            if (value_type == '') {
                Swal.fire({
                    title: 'Please select content type',
                    type: 'error',
                });
                return;
            }

            if (value_type == 'TOPIC') {
                $(wrapper).append('<div id="set_' + overAll + '" class="dynamic ui-state-default ui-sortable-handle"><div class="form-group"><label class="control-label col-md-3 col-sm-3 col-xs-12" for="Topic_title">Topic Title<span class="required-asterisk">*</span></label><div class="col-md-6 col-sm-6  col-xs-11"><input type="hidden" name="hidden_topic_title_' + topicCounter + '" value=""><input id="topic_title_' + topicCounter + '" name="topic_title_' + topicCounter + '" class="form-control custom topic_title_' + parseInt(topicCounter) + ' valid-topic"></div><a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a><div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash"><i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i></div></div></div>'); //add input box
                topicCounter++;
            } else if (value_type == 'BODY') {
                if ($('.textareaTiny').length) {
                    clone = $('.textareaTiny:first').clone();
                    clone.find('[name]').each(function () {
                        var temp = 'custom_paragarph_' + bodyCounter;
                        $(this).attr('name', temp);

                        $(this).attr('id', 'set_' + overAll);
                    });
                    clone.find('div.mce-tinymce').remove();
                } else {
                    clone = $('<div id="set_' + overAll + '" class="textareaTiny ui-state-default"> \
						<div class="form-group"> \
							<label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Paragraph Text </label> \
							<div class="col-md-7 col-sm-7 col-xs-10"> \
								<textarea class="form-control col-md-7 col-xs-12 text-tiny-mce" name="custom_paragarph_' + bodyCounter + '" id=""></textarea>\
							</div>\
							<div class="col-md-2 col-sm-2 col-xs-2 icon-move-trash"> \
								<i class="fa fa-arrows" aria-hidden="true"></i> \
								<i class="fa fa-trash remove-element pointer" aria-hidden="true"></i> \
							</div> \
						</div> \
					</div>');
                }
                var name = clone.find('textarea.text-tiny-mce').show().removeAttr('id').attr('name');
                wrapper.append(clone);
                initTinymce('[name="' + name + '"]');
                tinymce.get(name).setContent('');
                bodyCounter++;
            } else if (value_type == 'IMAGE') {

                imageCounter++;
                htm += '<div id="set_' + overAll + '" class="image_content ui-state-default ui-sortable-handle">';
                htm += '<div class="form-group">';
                htm += '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="content">Image</label>';
                htm += '<div class="col-md-6 col-sm-6 col-xs-12">';
                htm += '<input type="hidden" name="image_hidden_' + imageCounter + '" value="image' + imageCounter + '">';
                htm += '<input class="input-file filebox valid-image" type="file" name="image' + imageCounter + '" ><p><small>Allowed type ( gif, png, jpg, jpeg )</small></p>';
                htm += '</div>';
                htm += '<div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash"><i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i></div>';
                htm += '</div>';
                htm += '<div class="form-group">';
                htm += '<label class="control-label col-md-3 col-sm-3 col-xs-12">Image Credit</label>';
                htm += '<div class="col-md-6 col-sm-6 col-xs-12">';
                htm += '<input type="text" id="image_credit' + imageCounter + '" name="image_credit' + imageCounter + '" placeholder="Image Credit" class="form-control valid-image-credit" value="" autocomplete="off">';
                htm += '</div>';
                htm += '</div>';
                htm += '<div>';
                $(wrapper).append(htm);
                htm = '';
            } else if (value_type == 'RESOURCE') {
                htm = '<div class="dynamic ui-state-default">';
                htm += '<div class="form-group ui-sortable-handle">';
                htm += '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="Sub_topic_title">Select resource type<span class="required-asterisk">*</span></label>';
                htm += '<div class="col-md-6 col-sm-6  col-xs-11">';
                htm += '<select id="resource_' + resourceCounter + '" class="form-control change_type valid-resource-type">';
                htm += '<option value="" style="display:none" selected="true">Select Resource Type</option>';
                htm += '<option value="READING">Reading</option>';
                htm += '<option value="AUDIO">Audio</option>';
                htm += '<option value="VIDEO">Video</option>';
                htm += '<option value="WEBSITE">Website</option>';
                htm += '</select></div><div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash"><i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i></div></div>';
                htm += '<div class="form-group ui-sortable-handle">';
                htm += '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="Sub_topic_title">Add Resource <span class="required-asterisk">*</span></label>';
                htm += '<div class="col-md-6 col-sm-6  col-xs-11">';
                htm += '<select name="resource_' + resourceCounter + '" class="form-control resource_' + resourceCounter + ' valid-resource">';
                htm += '</select></div></div></div>';
                $(wrapper).append(htm);
                htm = '';
                resourceCounter++;
            }

            // name="resource_type_' + resourceCounter + '"
            overAll++;

            $(".dynamic-content-add").prop('disabled', true).addClass('no-drop');
            $('.dynamic-content-add i, .dynamic-content-add span').removeClass('c-pink');
            $(".click_to_add").val("");

        });


        $('#exerciseContent').length && $('#exerciseContent').on('change', '.worksheet_wrapper :checkbox', function () {
            $(this).closest('.worksheet_wrapper').find('.worksheet_file_wrapper').toggleClass('show hide');
            $(this).closest('.worksheet_wrapper').find('.worksheet_warn').toggleClass('hide show');
        });

        $('.dynamic-wrapper').on('click', '.dynamic-exercise-add', function () {
            var mainWrapper = $(this).closest('.dynamic-wrapper'),
                target = $(mainWrapper.find('.dynamic-exercise-add').attr('data-target')),
                source = mainWrapper.find('.click_to_add'),
                value_type = source.val(),
                overAll = target.find('[id^="set_"]').length + 1,
                exerciseCounter = parseInt(overAll);

            let itemCounter = exerciseCounter - 1;

            switch (value_type) {
                case 'TEXT_ITEM': {
                    target.append(`<div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_'` + exerciseCounter + `'">Primary Prompt </label>
                                            <div class="col-md-6 col-sm-6  col-xs-11">
                                                <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="TEXT_ITEM" class="form-control custom hidden" />
                                                <input id="pp_` + exerciseCounter + `" name="primary_prompt[]" class="form-control custom"/>
                                            </div>
                                            <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                            <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                                <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                            </div>
                                    </div>
        
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sp_` + exerciseCounter + ` ">Seconday Prompt </label>
                                        <div class="col-md-6 col-sm-6  col-xs-11">
                                            <input id="sp_` + exerciseCounter + `" name="secondary_prompt[]" class="form-control custom"/>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Text Field Size</label>
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <select name="text_field_size[]" class="form-control">
                                                <option value="T_1_LINE">1 Line</option>
                                                <option value="T_2_LINE">2 Lines</option>
                                                <option value="T_3_LINE">3 Lines</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>  
                            </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                case 'RADIO': {
                    target.append(`<div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_` + exerciseCounter + `">Primary Prompt </label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="RADIO" class="form-control custom hidden" />
                                        <input id="pp_` + exerciseCounter + `" name="primary_prompt_rd[]" class="form-control custom"/>
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                    <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sp_` + exerciseCounter + ` ">Secondary Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sp_` + exerciseCounter + `" name="secondary_prompt_rd[]" class="form-control custom"/>
                                </div>
                            </div>

                            <div class="form-group options"></div>
                           
                            <div class="form-group radio-container">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Options</label>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <select value="" class="form-control option" type="rd" label="Option Text" exercise_counter=` + exerciseCounter + `>
                                        <option value="">Select a content options size</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                            </div>
                        </div>  
                    </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                case 'CHECKBOX': {
                    target.append(`<div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_` + exerciseCounter + `">Primary Prompt </label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="CHECKBOX" class="form-control custom hidden" />
                                        <input id="pp_` + exerciseCounter + `" name="primary_prompt_cb[]" class="form-control custom"/>
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sp_` + exerciseCounter + ` ">Seconday Prompt </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sp_` + exerciseCounter + `" name="secondary_prompt_cb[]" class="form-control custom"/>
                                </div>
                            </div>

                            <div class="form-group options"></div>
                        
                            <div class="form-group radio-container">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Options</label>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <select value="" class="form-control option" type="cb" label="Option Text" exercise_counter=` + exerciseCounter + `>
                                        <option value="">Select a content options size</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        <option value="9">9</option>
                                        <option value="10">10</option>
                                    </select>
                                </div>
                            </div>
                        </div>  
                    </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                case 'TWO_COL': {
                    target.append(`<div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                        <div class="form-group">
                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="pp_` + exerciseCounter + `">Primary Prompt </label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="TWO_COL" class="form-control custom hidden" />
                                        <input id="pp_` + exerciseCounter + `" name="primary_prompt_col[]" class="form-control custom"/>
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fh_` + exerciseCounter + ` ">First Heading </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="fh_` + exerciseCounter + `" name="first_head_col[]" class="form-control custom"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sh_` + exerciseCounter + ` ">Second Heading </label>
                                <div class="col-md-6 col-sm-6  col-xs-11">
                                    <input id="sh_` + exerciseCounter + `" name="second_head_col[]" class="form-control custom"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Repeats</label>
                                <div class="col-md-6 col-sm-6 col-xs-6">
                                    <select value="" name="number_repeat_col[]" class="form-control">
                                        <option value="">Select a Number of Repeats</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                            </div>
                        </div>  
                    </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                case 'RATING': {
                    target.append(`
                        <div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                            <div class="form-group">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="fh_` + exerciseCounter + `"> First Heading</label>
                                        <div class="col-md-6 col-sm-6  col-xs-11">
                                            <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="RATING" class="form-control custom hidden" />
                                            <input id="fh_` + exerciseCounter + `" name="first_head_rate[]" class="form-control custom"/>
                                        </div>
                                        <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                        <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                            <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                        </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="sh_` + exerciseCounter + ` ">Second Heading </label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="sh_` + exerciseCounter + `" name="second_head_rate[]" class="form-control custom"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Others Items</label>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <select class="form-control" value="" name="number_other_rate[]">
                                            <option value="">Select a Number of Others Items</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group options"></div>
                            
                                <div class="form-group radio-container">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Number of Rating Items</label>
                                    <div class="col-md-6 col-sm-6 col-xs-6">
                                        <select value="" class="form-control option" type="rate" label="Rating Item" exercise_counter=` + exerciseCounter + `>
                                            <option value="">Select a Number of Rating Items</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                            <option value="7">7</option>
                                            <option value="8">8</option>
                                            <option value="9">9</option>
                                            <option value="10">10</option>
                                        </select>
                                    </div>
                                </div>
                            </div>  
                        </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                case 'GOAL': {
                    target.append(`
                        <div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                            <div class="form-group">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gl_` + exerciseCounter + `"> Goal</label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="GOAL" class="form-control custom hidden" />
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>  
                        </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                case 'GOAL_TRACKING': {
                    target.append(`
                        <div id="set_` + overAll + `" class="dynamic ui-state-default ui-sortable-handle">
                            <div class="form-group">
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="gt_` + exerciseCounter + `"> Goal Tracking</label>
                                    <div class="col-md-6 col-sm-6  col-xs-11">
                                        <input id="hidden_` + exerciseCounter + `" name="items[` + itemCounter + `]" value="GOAL_TRACKING" class="form-control custom hidden" />
                                    </div>
                                    <a href="#" class="remove_field"><i class="leaf leaf-delete"></i></a>
                                    <div class="col-md-3 col-sm-3 col-xs-3 icon-move-trash">
                                        <i class="fa fa-arrows" aria-hidden="true"></i><i class="fa fa-trash remove-element pointer" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>  
                        </div>`); //add input box
                    exerciseCounter++;
                    break;
                }
                default: {
                    console.log("Item is not selected by the researcher!");
                }
            }

            $(".dynamic-exercise-add").prop('disabled', true).addClass('no-drop');
            $('.dynamic-exercise-add i, .dynamic-exercise-add span').removeClass('c-pink');
            $(".click_to_add").val("");
        });

        //radio option render
        $(document).on('change', '.option', function () {
            let optionCounter = $(this).attr('exercise_counter');
            let item_group = parseInt(optionCounter) - 1;
            let optionSize = $(this).val();
            let type = $(this).attr('type');
            let label = $(this).attr('label');

            for (option_sno = 1; option_sno <= optionSize; option_sno++) {
                let id = optionCounter + option_sno;
                $(this).closest('.radio-container').prev('.options').append(`
                <div class="form-group" >
                    <label class="control-label col-md-3 col-sm-3 col-xs-12" for="op_` + id + ` ">` + label + ' ' + option_sno + ` </label>
                    <div class="col-md-6 col-sm-6  col-xs-11">
                        <input id="op_`  + id + `"name="option_text_` + type + `[` + item_group + `][]" class="form-control"/>
                    </div>
                 </div>`)
                $(this).closest('.radio-container').fadeOut();
            }
        });

        // Sorting / Drag-drop of html element
        $("#sortable").sortable({
            start: function (e, ui) {
                $(ui.item).find('textarea.text-tiny-mce').each(function () {
                    tinymce.execCommand('mceRemoveEditor', false, $(this).attr('id'));
                });
            },
            stop: function (e, ui) {
                $(ui.item).find('textarea.text-tiny-mce').each(function () {
                    tinymce.execCommand('mceAddEditor', true, $(this).attr('id'));
                });
            }
        });
        $("#sortable").disableSelection();

        // Get resource list by its type
        $(document).on('change', '.change_type', function () {
            var param = { 'type': this.value.toString() }
            var htm = '';
            var classname = '.' + this.id;
            $.ajax({
                url: BASE_URL + 'educational/get_resource_list',
                dataType: 'json',
                method: 'POST',
                data: param,
                success: function (result) {

                    if (result != 0) {
                        htm = '<option value="" style="display:none" selected="true">Select Resource</option>';
                        $.each(result, function (i, obj) {
                            htm += '<option value="' + obj.id + '">' + obj.title + '</option>';
                        });
                        $(classname).html(htm);
                    } else {
                        $(classname).html(htm);
                        Swal.fire({
                            title: 'Resource not available',
                            type: 'error',
                        });
                    }
                }
            });
        }).on('click', '.remove-element', function () { // Removing HTML element 
            if ($(this).hasClass("is_topic_delete")) {
                var topic_id = $(this).attr('data-topic-id');
                $(wrapper).append('<input type="hidden" name="delete_topic_' + topic_id + '" value="' + topic_id + '">');
            }
            if ($(this).hasClass("is_sub_topic_delete")) {
                var sub_topic_id = $(this).attr('data-sub-topic-id');
                $(wrapper).append('<input type="hidden" name="delete_sub_topic_' + sub_topic_id + '" value="' + sub_topic_id + '">');
            }
            $(this).closest('.ui-state-default').remove();

        }).on('click', '.toggle-chapters', function () { // Toggel chapters list in 'List of educational content'
            var $tr = $(this).parents('.parent'),
                chapter = $tr.attr('data-chapter');

            if ($(this).hasClass("fa-minus")) {
                $(this).removeClass('fa-minus').addClass('fa-plus');
                $("tr.child").addClass("hidden");
            } else {

                $('tr[data-chapter="' + chapter + '"]').each(function (index) {
                    if ($(this).hasClass("parent")) {
                        $(this).find(".iconToggle").addClass('fa-minus').removeClass('fa-plus');
                    } else {
                        if ($(this).hasClass("child")) {
                            $(this).removeClass("hidden");
                        }
                    }
                });
                $('tr:not([data-chapter="' + chapter + '"])').each(function (index) {
                    if ($(this).hasClass("parent")) {
                        $(this).find(".iconToggle").removeClass('fa-minus').addClass('fa-plus');
                    } else {
                        if ($(this).hasClass("child")) {
                            $(this).addClass("hidden");
                        }
                    }
                });
            }

        }).on('click', '.reload_current_page', function () { // Reload current page 
            location.reload();
        });

        // Remaning character count
        var input_length = 50;
        var textarea_length = 260;
        var input_chapter_name = document.getElementById('chapter_name');
        if (input_chapter_name != null) { remaining_character_count(input_chapter_name, input_length) }
        var input_chapter_title = document.getElementById('chapter_title');
        if (input_chapter_title != null) { remaining_character_count(input_chapter_title, input_length) }
        var input_first_paragragh = document.getElementById('first_paragragh');
        if (input_first_paragragh != null) { remaining_character_count(input_first_paragragh, textarea_length) }
        var input_topic_title = document.getElementById('topic_title');
        if (input_topic_title != null) { remaining_character_count(input_topic_title, input_length) }
        var input_sub_topic_title = document.getElementById('sub_topic_title');
        if (input_sub_topic_title != null) { remaining_character_count(input_sub_topic_title, input_length) }

        function remaining_character_count(input, maxLength) {
            var finalCount = '';
            input.addEventListener('keypress', function (e) {
                if (e.which < 0x20) { return; }
                if (this.value.length == maxLength) { e.preventDefault(); }
                if (input.value.length < maxLength) {
                    finalCount = parseInt(maxLength) - parseInt(input.value.length)
                    finalCount = "Characters remaining: " + finalCount;
                } else {
                    finalCount = '';
                }
                $('.error_remaning_' + input.id).html(finalCount);
            });
        }

        // Resource assign to chapters and sub-topic
        $(document).on('click', '#resource_assign_link', function () {
            $('#hidden_resource_assign_content_id').val($(this).attr("data-content-id"));
        }).on('click', '.assign_resource', function (e) { // Assign resource to content
            var closeData = $(this).attr("name")
            e.preventDefault();
            var formData = $('#assign-resource').serialize();
            $.ajax({
                url: BASE_URL + 'educational/assign_resource',
                dataType: 'json',
                method: 'POST',
                data: formData,
                success: function (result) {
                    var msg = 'Something went wrong !';
                    var typ = 'error';
                    if (result == 1) {
                        msg = "Resource assigned successfully";
                        typ = 'success';
                    } else if (result == 0) {
                        msg = "Resource already assigned";
                    } else if (result == 2) {
                        msg = "Record not assigned";
                    }
                    Swal.fire({
                        title: msg,
                        type: typ,
                    }).then(function () {
                        if (closeData == 'save') {
                            $('#add_resource').modal('toggle');
                            location.reload();
                        }
                    })
                }
            });
        });

        $('.selectpicker').selectpicker();

        // image upload
        // Upload Profile
        $(".upload-button").on('click', function () {
            $(".file-upload").click();
        });

        $(".file-upload").on('change', function () {
            readURL(this);
        });

        var readURL = function (input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('.box-image').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        };




        // Reordering of sidebar resources
        $("#reading_tbl, #video_tbl, #audio_tbl, #website_tbl").sortable({
            items: 'tr',
            cursor: 'pointer',
            axis: 'y',
            dropOnEmpty: false,
            start: function (e, ui) {
                ui.item.addClass("selected");
            },
            stop: function (e, ui) {
                ui.item.removeClass("selected");
                $(this).find("tr").each(function (index) {
                    if (index > 0) {
                        $(this).find("tr").attr('data-club-id');
                        if ($(this).attr('data-club-id') != undefined) {
                            var clubed_id = $(this).attr('data-club-id').split("-");
                            var param = { 'resource': clubed_id[0], 'content': clubed_id[1], 'order': index }
                            $.ajax({
                                url: BASE_URL + 'educational/reorder-position-resources',
                                dataType: 'json',
                                method: 'POST',
                                data: param,
                                success: function (result) {
                                    // no action performed
                                }
                            });
                        }
                    }
                });
            }
        });

        // Reordering of 'List of educational content' : chpaters
        $("#content_tbl").sortable({
            items: 'tr.parent',
            cursor: 'pointer',
            axis: 'y',
            dropOnEmpty: false,
            start: function (e, ui) {
                ui.item.addClass("selected");
            },
            stop: function (e, ui) {
                ui.item.removeClass("selected");
                $(this).find("tr.parent").each(function (index) {
                    if (index >= 0) {
                        if ($(this).attr('data-parent-id') != undefined) {
                            var content_id = $(this).attr('data-parent-id');
                            var table = $(this).attr('data-parent-table') ? $(this).attr('data-parent-table') : 'content';
                            var param = { 'content_id': content_id, 'order': index, 'table': table }
                            $.ajax({
                                url: BASE_URL + 'educational/reorder-position-chapters',
                                dataType: 'json',
                                method: 'POST',
                                data: param,
                                success: function (result) {
                                    // no action performed
                                }
                            });
                        }
                    }
                });
            }
        });
    });
})(jQuery);

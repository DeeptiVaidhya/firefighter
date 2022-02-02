(function ($) {
    // Additional rule for check extension of file
    $.validator.addMethod("extension", function (value, element, param) {
        param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
        return this.optional(element) || value.match(new RegExp("\\.(" + param + ")$", "i"));
    }, $.validator.format("Please enter a valid file."));

    // Additional rule for check color code
    $.validator.addMethod("color", function (value, element) { //^[a-z0-9_\s-']+$
        return this.optional(element) || /^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/.test(value); //
    }, "Invalid color code.");

    /* ------- Basic information form script for validation-------- */
    $("#registerForm").validate({
        rules: {
            first_name: { required: true },
            last_name: { required: true },
            gender: { required: true },
            username: { required: true },
            email: { required: true, email: true },
            password: { required: true },
            confirm_password: {
                equalTo: "#password",
                required: function () {
                    return $("#password").val().length > 0;
                }
            },
            cancer_type: { required: true }
        },
        submitHandler: function (form) {

            $('#loading-image').fadeIn();
            $('#loading').css('display', 'block');
            form.submit();
        },
        errorPlacement: function (error, element) {
            if (element.attr('name') == 'gender') {
                error.insertAfter('#genderLbl');
            } else {
                error.insertAfter(element);
            }
        },
    });

    /** Detail chapter validtion dynamic start */
    $('form#chapterContent').on('submit', function (event) {
        //Add validation rule for dynamically generated name fields
        $('.valid-image').each(function () {
            $(this).rules("add", { required: true, extension: "gif|png|jpg|jpeg" });
        });
        $('.valid-topic').each(function () {
            $(this).rules("add", { required: true, maxlength: 50, normalizer: function (value) { return $.trim(value); } });
        });
        $('.valid-image-credit').each(function () {
            $(this).rules("add", { normalizer: function (value) { return $.trim(value); } });
        });
    });

    $("#chapterContent").validate({
        ignore: "",
        rules: {
            chapter_title: { required: true, normalizer: function (value) { return $.trim(value); } },
            chapter_icon: {
                required: function () {
                    if ($("input[name=chapter_icon_hid_edit_value]").val())
                        return false;
                    return true;
                },
                extension: "mp3|m4a"
                , extension: "gif|png|jpg|jpeg"
            },
            first_paragragh: { required: false, maxlength: 260 },
            image: { extension: "gif|png|jpg|jpeg" }
        },
        submitHandler: function (form) {
            $('#loading-image').fadeIn();
            $('#loading').css('display', 'block');
            form.submit();
        }
    });
    /** * Detail chapter validtion dynamic end */

    /** Topic validtion dynamic start */

    $('form#add_topic_content,form#edit_topic_content').on('submit', function (event) {
        $('.valid-resource-type').each(function () {
            $(this).rules("add", { required: true });
        });
        $('.valid-resource').each(function () {
            $(this).rules("add", { required: true });
        });
    });

    $("#add_topic_content, #edit_topic_content").validate({
        ignore: "",
        rules: {
            topic_title: { required: true, normalizer: function (value) { return $.trim(value); } },
            image1: { required: true },
            image2: { required: true },
            image3: { required: true },
            image4: { required: true },
            image5: { required: true },
            image: { extension: "gif|png|jpg|jpeg" }
        },
        submitHandler: function (form) {
            $('#loading-image').fadeIn();
            $('#loading').css('display', 'block');
            form.submit();
        }
    });

    /* ------- update participants for validation-------- */
    $("#editParticipant").validate({

        rules: {
            first_name: { required: true },
            last_name: { required: true },
            arm_alloted: { required: true },
            email: { required: true },
            subject_id: { required: true }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    $("#resourceForm").validate({
        rules: {
            resource_type: { required: true },
            resource_level: { required: true },
            title: { required: true },
            external_link: {
                required: true,
                url: true
            },
            audio: {
                required: function () {
                    if ($("select[name=resource_type]").val() == 'AUDIO' && $("input[name=files_id]").val())
                        return false;
                    return true;
                },
                extension: "mp3|m4a"
            },
            description: { required: true }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    /* ------- add exercise for validation-------- */
    $("#exerciseContent").validate({
        rules: {
            exercise_title: { required: true, normalizer: function (value) { return $.trim(value); } },
            worksheet_file: {
                required: function () {
                    if ($("input[name=worksheet_id]").val() > 0)
                        return false;
                    return true;
                }, extension: "pdf"
            },
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    /* ------- End Script-------- */

    /* -----------  Clear Error message and all input in  form ------------*/
    $(document).on('click', '.btn-reset', function () {
        var formId = $(this).parents('form').attr('id');
        var validator = $("#" + formId).validate();
        $("#" + formId)[0].reset();
        validator.resetForm();
    });
})(jQuery);
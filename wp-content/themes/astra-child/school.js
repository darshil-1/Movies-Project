
jQuery(document).ready(function ($) {


    function resetForm(formId, submitBtnId, submitBtnText = 'Submit') {
        $(formId)[0].reset();
        $(formId + ' #user_id').val('');
        $(submitBtnId).text(submitBtnText);
        $(formId + ' #cancelEditBtn').hide();
    }


    function loadStudentData() {
        $.post(ajax_object.ajaxurl, { action: 'show_user_data', nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                $('#studentTable').html(response.data);
            } else {
                $('#studentTable').html('<tr><td colspan="8">No data found.</td></tr>');
            }
        }).fail(function () {
            alert('Error loading student data.');
        });
    }


    function loadSchoolData() {
        $.post(ajax_object.ajaxurl, { action: 'show_school_data', nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                $('#schoolTable tbody').html(response.data);
            } else {
                $('#schoolTable tbody').html('<tr><td colspan="4">No data found.</td></tr>');
            }
        }).fail(function () {
            alert('Error loading school data.');
        });
    }


    $(document).on('click', '.editBtn', function () {
        var row = $(this).closest('tr');

        $('#user_id').val(row.find('.col-id').text().trim());
        $('#fname').val(row.find('.col-fname').text().trim());
        $('#lname').val(row.find('.col-lname').text().trim());

        var gender = row.find('.col-gender').text().trim().toLowerCase();
        $("input[name='gender'][value='" + gender + "']").prop("checked", true);

        $('#age').val(row.find('.col-age').text().trim());
        $('#address').val(row.find('.col-address').text().trim());
        $('#email').val(row.find('.col-email').text().trim());

        $('#submitBtn').text('Update Student');
    });


    $(document).on('click', '.editSchoolBtn', function () {
        var row = $(this).closest('tr');

        $('#schoolid').val(row.find('.col-id').text().trim());
        $('#schoolname').val(row.find('.col-schoolname').text().trim());
        $('#address').val(row.find('.col-address').text().trim());

        $('#saveSchoolBtn').text('Update');
        $('#cancelEditBtn').show();
    });


    $('#cancelEditBtn').click(function () {
        resetForm('#schoolForm', '#saveSchoolBtn', 'Save');
    });


    $('#mySchool').submit(function (e) {
    e.preventDefault();
    var isValid = true;
    
    var firstname = $('#fname').val();
    if (firstname.trim() === '') {
        $('#fnameErr').html("Please Enter Firstname");
        isValid = false;
    }else{
         $('#fnameErr').html("");
    }

    var lastname = $('#lname').val();
    if (lastname.trim() === '') {
        $('#lnameErr').html("Please Enter Lastname");
        isValid = false;
    }else{
         $('#lnameErr').html("");
    }

    var gender = $('input[name="gender"]:checked').val();
    if (!gender) {
        $("#genErr").html("Please Select gender");
        isValid = false;
    }else{
         $("#genErr").html("");
    }

    var email = $('#email').val();
    if (email.trim() === '') {
        $('#emailErr').html("Please Enter Email");
        isValid = false;
    }else{
         $('#emailErr').html("");
    }

    var age = $('#age').val();
    if (age.trim() === '') {
        $('#ageErr').html("Please Enter Your Age");
        isValid = false;
    }else{
         $('#ageErr').html("");
    }

    var sclid = $('#scoolid').val();
    if(!sclid) {
        $('#schoolErr').html("Please Select School");
    }else{
         $('#schoolErr').html("");
    }
    if (!isValid) {
        e.preventDefault();
    }else{
        var formData = $(this).serializeArray();
        var userId = $('#user_id').val();
        formData.push({ name: 'action', value: userId ? 'update_user_data' : 'insert_user_data' });
        formData.push({ name: 'nonce', value: ajax_object.nonce });

        $.post(ajax_object.ajaxurl, formData, function (response) {
            if (response.success) {
                alert(response.data);
                loadStudentData();
                resetForm('#mySchool', '#submitBtn', 'Add Student');
            } else {
                alert(response.data || 'An error occurred.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    }
    });


    $('#schoolForm').submit(function (e) {
        e.preventDefault();
         
        var isValid = true;

        var schoolname = $("#schoolname").val();    
        if (schoolname.trim() === '') {
            $('#scnameErr').html("Please Enter School name");
            isValid = false;
        }else{
            $('#scnameErr').html("");
        }

         var schooladd = $("#address").val();    
        if (schooladd.trim() === '') {
            $('#scaddErr').html("Please Enter Address");
            isValid = false;
        }else{
            $('#scaddErr').html("");
        }
    
        if (!isValid) {
        e.preventDefault();
        }else{
        var formData = $(this).serializeArray();
        var schoolId = $('#schoolid').val();
        var action = schoolId ? 'update_school_data' : 'insert_school_data';

        formData.push({ name: 'action', value: action });
        formData.push({ name: 'nonce', value: ajax_object.nonce });

        $.post(ajax_object.ajaxurl, formData, function (response) {
            if (response.success) {
                alert(response.data);
                resetForm('#schoolForm', '#saveSchoolBtn', 'Save');
                loadSchoolData();
            } else {
                alert(response.data || 'An error occurred.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    }
    });

    $(document).on('click', '.deleteSchoolBtn', function () {
        if (!confirm('Are you sure you want to delete this user?')) return;

        var id = $(this).data('id');

        $.post(ajax_object.ajaxurl, { action: 'delete_school_data', schoolid: id, nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                alert(response.data);
                loadSchoolData();
            } else {
                alert(response.data || 'Failed to delete user.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    });


    $(document).on('click', '.deleteBtn', function () {
        if (!confirm('Are you sure you want to delete this user?')) return;

        var id = $(this).data('id');

        $.post(ajax_object.ajaxurl, { action: 'delete_user_data', id: id, nonce: ajax_object.nonce }, function (response) {
            if (response.success) {
                alert(response.data);
                loadStudentData();
                resetForm('#mySchool', '#submitBtn', 'Add Student');
            } else {
                alert(response.data || 'Failed to delete user.');
            }
        }).fail(function () {
            alert('Failed to communicate with server.');
        });
    });


    loadStudentData();
    loadSchoolData();

});

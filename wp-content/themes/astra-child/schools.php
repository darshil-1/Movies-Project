<?php
/*
Template Name: School Form
*/
get_header(); 


$user_id = isset($_GET['id']) ? intval($_GET['id']) : '';
$fname = isset($_GET['fname']) ? sanitize_text_field($_GET['fname']) : '';
$lname = isset($_GET['lname']) ? sanitize_text_field($_GET['lname']) : '';
$gender = isset($_GET['gender']) ? sanitize_text_field($_GET['gender']) : '';
$age = isset($_GET['age']) ? intval($_GET['age']) : '';
$address = isset($_GET['address']) ? sanitize_textarea_field($_GET['address']) : '';
$email = isset($_GET['email']) ? sanitize_email($_GET['email']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/school.css'?>">
</head>
<body>
<div class="modal-dialog modal-fullscreen-sm-down">
  <div class="form shadow rounded">
    <h2>Student Data</h2> 
    <form id="mySchool" method="post" >
    <input type="hidden" id="user_id" name="id" value="<?php echo esc_attr($user_id); ?>">

    <label class="form-label" for="fname">First Name</label>
    <input type="text" class="fname" name="fname" id="fname" placeholder="Enter your firstname" value="<?php echo esc_attr($fname); ?>">
    <span class="error" id="fnameErr" ></span><br>

    <label class="form-label" for="lname">Last Name</label>
    <input type="text" name="lname" id="lname" placeholder="Enter your lastname" value="<?php echo esc_attr($lname); ?>">
    <span class="error" id="lnameErr" ></span><br>

    <label class="form-label" for="gender">Gender</label><br>
    <label class="form-label" for="male">Male</label>
    <input type="radio" name="gender" value="male" id="male" <?php echo ($gender === 'male') ? 'checked' : ''; ?> >
    <label class="form-label" for="female">Female</label>
    <input type="radio" name="gender" value="female" id="female" <?php echo ($gender === 'female') ? 'checked' : ''; ?> ><br>
    <span class="error" id="genErr"></span><br>

    <label class="form-label" for="age">Age</label>
    <input type="number" name="age" id="age" placeholder="Enter your age" value="<?php echo esc_attr($age); ?>"><br>
    <span class="error" id="ageErr"></span><br>

    <label class="form-label" for="address" >Address</label>
    <textarea name="address" id="address" placeholder="Address"><?php echo esc_textarea($address); ?></textarea>

    <label class="form-label" for="email">Email</label>
    <input type="email" name="email" id="email" placeholder="Enter your email" value="<?php echo esc_attr($email); ?>"><br>
    <span class="error" id="emailErr"></span><br>

    <label class="form-label" for="schoolid">Select School:</label>
    <select class="form-select" id="schoolid" name="schoolid">
    <option value="">-- Select School --</option>
    <?php
    global $wpdb;
    $schools = $wpdb->get_results("SELECT schoolid, schoolname FROM school_tbl");
    foreach ($schools as $school) {
        echo '<option value="' . esc_attr($school->schoolid) . '">' . esc_html($school->schoolname) . '</option>';
    }
    ?>
    </select>
    <span class="error" id="schoolErr"></span><br>

   <button class="mt-3 button" name="add" id="addBtn"><?php echo $id ? 'Update' : 'Submit'; ?></button>
    </form>
    </div>
</div>
    
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</html>

<?php get_footer(); ?>

    
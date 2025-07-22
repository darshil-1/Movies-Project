<?php
/*
    Template Name: Add School
*/
get_header(); 

$schoolid   = isset($_GET['schoolid']) ? intval($_GET['schoolid']) : '';
$schoolname = isset($_GET['schoolname']) ? sanitize_text_field($_GET['schoolname']) : '';
$address    = isset($_GET['address']) ? sanitize_textarea_field($_GET['address']) : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $schoolid ? 'Edit School' : 'Add School'; ?></title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/school.css'?>">
</head>
<body>
<div id="schoolFormContainer"  class="mt-4 form shadow">
    <h3><?php echo $schoolid ? 'Edit School' : 'Add New School'; ?></h3>
    <form id="schoolForm">
        <input type="hidden" id="schoolid" name="schoolid" value="<?php echo esc_attr($schoolid); ?>" />
        
        <label class="form-label" for="schoolname">School Name:</label><br>
        <input type="text" id="schoolname" name="schoolname" value="<?php echo esc_attr($schoolname); ?>" /><br>
        <span class="error" id="scnameErr"></span><br>

        <label class="form-label" for="address">Address:</label><br>
        <textarea id="address" name="address"><?php echo esc_textarea($address); ?></textarea><br>
        <span class="error" id="scaddErr"></span><br>

        <button type="submit" class="button small" id="saveSchoolBtn"><?php echo $schoolid ? 'Update' : 'Add'; ?></button>
        <button type="button" id="cancelEditBtn" style="display:none;">Cancel</button>
    </form>
</div>

</body>
</html>

<?php get_footer(); ?>

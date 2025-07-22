
<?php
/*
Template Name: School Table
*/
get_header(); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student and School Master Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() . '/school.css'?>">
</head>
<body>
    <div class="showTable mt-3 ">
        <div class="header">
            <h2>All Student</h2>
             <a href="<?php echo esc_url(site_url('/school')); ?>" class="text-decoration-none button small">Add Student</a>
        </div>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Firstname</th>
                    <th>Lastname</th>
                    <th>Gender</th>
                    <th>Age</th>
                    <th>Email</th>
                    <th>Edit/Delete</th>
                </tr>
            </thead>
            <tbody id="studentTable"></tbody>
        </table>
    </div>

    <div class="showTable mt-5">
        <div class="header">
            <h2>School Master</h2>
            <a href="<?php echo esc_url(site_url('/add-school')); ?>" class="text-decoration-none button small">Add School</a>
        </div>
        <table class="table table-striped table-bordered " id="schoolTable" >
            <thead>
                <tr>
                    <th>School ID</th>
                    <th>School Name</th>
                    <th>Address</th>
                    <th>Edit/Delete</th>
                </tr>
            </thead>
            <tbody id="schoolTable"></tbody>
        </table>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</html>

<?php get_footer(); ?>


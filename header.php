<?php 
include 'authentication/functions.php';
include 'authentication/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo get_option('system_description')?>">
    <title>TripTrip Project</title>
    <?php render_styles()?>
    <!-- <link rel="stylesheet" href="../assets/csss/users.css"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var base_url = '<?php echo base_url() ?>';
    </script>
    <?php render_scripts() ?>
</head>
    <body class="bg-light-300">
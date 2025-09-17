<?php include "../../header.php"; ?>

<?php include 'nav-head.php'; ?>

<title><?php echo get_option('system_title'); ?></title>
<div class="d-flex d-justify-between">
    <?php include 'nav-sidebar.php'; ?>
    <div class="w-100 mt-2">
        <?php include 'content.php'; ?>
    </div>
</div>
<?php include '../../footer.php' ?>
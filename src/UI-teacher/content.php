<div id="view-panel" class="container-fluid" style="height: calc(100vh - 100px);">
    <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home'; ?>
    <?php include $page . '.php' ?>
</div>
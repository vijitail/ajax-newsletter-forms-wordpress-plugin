
<div class="wrap">
<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
<h2></h2>

<?php 

    require_once plugin_dir_path( __FILE__ ).'/../inc/AdminTable.php';

    $listTable = new AdminTable();
    $listTable->prepare_items();
    ?>
    <form method="POST">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />  
    <?php
    $listTable->display();
?>
    </form>
</div>


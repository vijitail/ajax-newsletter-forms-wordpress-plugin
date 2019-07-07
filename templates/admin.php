
<div class="wrap">
<h1><?php echo esc_html( get_admin_page_title() ); ?> <a href="<?php get_admin_url('/admin.php?page=new_ajax_newsletter_form') ?>" class="page-title-action">Add New Form</a> </h1>
<h2></h2>

<?php 
    if(null !== $_GET['action'] && $_GET['action'] == 'delete' && null !== $_GET['form']) {
        $db_update = new DBUpdate();
        $id = $_GET['form'];
        $db_update->delete_anf($id);
    }

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


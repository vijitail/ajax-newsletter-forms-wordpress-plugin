<?php

global $wpdb;

$id = $_GET['form'];

$table_name = $wpdb->prefix.'ajax_newsletter_forms';

$form_data = (array) $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id=$id", ''));

?>
<div class="wrap">
    <h1>Edit Ajax Form</h1>
    <hr>
    <form method="post" action="<?php echo esc_html( admin_url('admin-post.php') ); ?>">
        <input type="hidden" name="action" value="edit_anf">
        <input type="hidden" name="formId" value=<?php echo $form_data['id']; ?>>
        <div class="option">
            <input type="text" placeholder="Form Name" name="formName" value='<?php echo $form_data['name']; ?>'>
        </div>
        <div class="option">
            <input type="number" placeholder="Newsletter List Number" name="listNumber" max="40" min="1" value='<?php echo $form_data['list_num']; ?>'>
        </div>
        <div class="option">
            <label for="hasName">Add Name field in the newsletter form </label> <input type="checkbox" name="hasName" id="hasName" <?php echo $form_data['has_name_field'] == 1 ? "checked" : ''; ?>>
        </div>
        <div class="option">
            <input type="text" placeholder="Ajax URL (optional)" name="ajaxUrl">            
        </div>
        <div class="option">
            <label>Custom jQuery for form success</label>
            <textarea class="code-editor" name="onsuccessJQuery"><?php echo stripcslashes($form_data['onsuccess_jquery']); ?></textarea>
        </div>
        <div class="option">
            <label>Custom jQuery for form error</label>
            <textarea class="code-editor" name="onerrorJQuery"><?php echo stripcslashes($form_data['onerror_jquery']); ?></textarea>
        </div>

        <?php
            wp_nonce_field( 'anf-form-save', 'anf-custom-message' );
            submit_button("Edit Form");
        ?>
    
    </form>
</div>
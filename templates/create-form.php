<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <hr>
    <form method="post" action="<?php echo esc_html( admin_url('admin-post.php') ); ?>">
        <input type="hidden" name="action" value="save_anf">
        <div class="option">
            <input type="text" placeholder="Form Name" name="formName" required>
        </div>
        <div class="option">
            <input type="number" placeholder="Newsletter List Number" name="listNumber" max="40" min="1">
        </div>
        <div class="option">
            <label for="hasName">Add Name field in the newsletter form </label> <input type="checkbox" name="hasName" id="hasName">
        </div>
        <div class="option">
            <input type="number" placeholder="Ajax URL (optional)" name="ajaxUrl">            
        </option>
        <div class="option">
            <label>Custom jQuery for form success</label>
            <textarea class="code-editor" name="onsuccessJQuery"></textarea>
        </div>
        <div class="option">
            <label>Custom jQuery for form error</label>
            <textarea class="code-editor" name="onerrorJQuery"></textarea>
        </div>

        <?php
            wp_nonce_field( 'anf-form-save', 'anf-custom-message' );
            submit_button("Create Form");
        ?>
    
    </form>
</div>
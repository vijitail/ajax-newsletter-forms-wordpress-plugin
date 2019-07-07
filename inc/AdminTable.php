<?php

/**
 * @package ANFPlugin
*/

if(!class_exists('AdminTable')) {

    if( ! class_exists( 'WP_List_Table' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-screen.php' );
        require_once( ABSPATH . 'wp-admin/includes/screen.php' );
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
        require_once( ABSPATH . 'wp-admin/includes/template.php' );
    }   

    class AdminTable extends WP_List_Table
    {
        private $db_update = null;
        private $serializer = null;
        function __construct() 
        {
            $this->db_update = new DBUpdate();
            $this->serializer = new Serializer();
            $this->screen = get_current_screen();
            parent::__construct( [
                'singular' => __( 'Form', 'anf' ), //singular name of the listed records
                'plural'   => __( 'Forms', 'anf' ), //plural name of the listed records
                'ajax'     => false //should this table support ajax?
		    ] );
        }

        function prepare_items() 
        {
            $columns = $this->get_columns();
            $hidden = $this->get_hidden_columns();
            $sortable = $this->get_sortable_columns();
            $data = $this->table_data();
            usort( $data, array( &$this, 'sort_data' ) );
            $perPage = 10;
            $currentPage = $this->get_pagenum();
            $totalItems = count($data);
            $this->set_pagination_args( array(
                'total_items' => $totalItems,
                'per_page'    => $perPage
            ) );
            $data = array_slice($data,(($currentPage-1)*$perPage),$perPage);
            $this->_column_headers = array($columns, $hidden, $sortable);
            $this->items = $data;
            $this->process_bulk_action();
        }

        public function get_columns()
        {
            $columns = array(
                'cb'    => '<input type="checkbox" />',
                'id'          => 'ID',
                'name'       => 'Name',
                'shortcode' => 'Shortcode',
            );
            return $columns;
        }

        function column_name($item) {
            $actions = array(
                'edit' => sprintf('<a href="?page=%s&action=%s& form=%s">Edit</a>','edit_ajax_newsletter_form','edit',$item['id']),
                'delete' => sprintf('<a href="?page=%s&action=%s&form=%s">Delete</a>','ajax_newsletter_forms','delete',$item['id']),
            );
            return sprintf('%1$s %2$s', $item['name'], $this->row_actions($actions) );
        }

        public function get_hidden_columns()
        {
            return array();
        }

        public function get_sortable_columns()
        {
            return array('name' => array('name', false));
        }

        private function table_data() {
            // return $this->db->get_anfs();
            global $wpdb;
            $results = $wpdb->get_results( 
                $wpdb->prepare("SELECT * FROM wp_ajax_newsletter_forms;", '')
             );
             $data = array();
             foreach($results as $result) {
                $result = (array)$result;
                $data[]= array(
                    'id' => $result['id'],
                    'name' => $result['name'],
                    'shortcode' => $result['shortcode']
                );
             }
             return $data;
        }

        public function column_cb( $item ) {
            return sprintf( '<input type="checkbox" class="bulk-item-selection" name="bulk-item-selection[]" value="%s" />', $item['id'] );
        }

        public function column_default( $item, $column_name )
        {
            switch( $column_name ) {
                case 'cb':
                    return $column_name;
                    break;
                case 'id':
                case 'name':
                case 'shortcode':
                default:
                    return $item[$column_name] ;
            }
        }

        private function sort_data( $a, $b )
        {
            // Set defaults
            $orderby = 'id';
            $order = 'asc';
            // If orderby is set, use this as the sort column
            if(!empty($_GET['orderby']))
            {
                $orderby = $_GET['orderby'];
            }
            // If order is set use this as the order
            if(!empty($_GET['order']))
            {
                $order = $_GET['order'];
            }
            $result = strcmp( $a[$orderby], $b[$orderby] );
            if($order === 'asc')
            {
                return $result;
            }
            return -$result;
        }

        public function get_bulk_actions() {

            return array(
                    'delete' => 'Delete',
            );
    
        }
    
        public function process_bulk_action() {
    
            // security check!
            if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
    
                $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
                $action = 'bulk-' . $this->_args['plural'];
    
                if ( ! wp_verify_nonce( $nonce, $action ) )
                    wp_die( 'Nope! Security check failed!' );
    
            }
    
            $action = $this->current_action();
    
            switch ( $action ) {
    
                case 'delete':
                    foreach($_POST['bulk-item-selection'] as $id) {
                        $this->db_update->delete_anf($id);
                    }
                    break;
    
                default:
                    // do nothing or something else
                    return;
                    break;
            }
            /* Hacky but works... following script was added because the list table was not updating after bulk action */
            echo "<script> window.location.href = '".$_SERVER['HTTP_REFERER']."'</script>";
            
            return;
        }

    }

}
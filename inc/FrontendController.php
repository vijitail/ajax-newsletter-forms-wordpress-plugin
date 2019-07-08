<?php

/**
 * @package ANFPlugin
*/

if(!class_exists('FrontendController')) {

    class FrontendController 
    {
        private $db = null;
        function __construct()
        {
            global $wpdb;
            $this->db = $wpdb;
        }

        function register() 
        {
            add_shortcode( 'anf', function($attr) {
                $this->display_form($attr['id']);
            } );

            add_action( 'wp_enqueue_scripts', function() {
                $this->add_frontend_scripts();
            } );

            add_action( 'wp_ajax_anf_submit', array($this, 'form_submit') );
            add_action( 'wp_ajax_nopriv_anf_submit', array($this, 'form_submit') );
        }

        private function display_form($id) 
        {
            $opts = get_option('newsletter');
            // print_r($opts);
            // wp_die();
            $results = $this->db->get_row( 
                $this->db->prepare("SELECT * FROM wp_ajax_newsletter_forms WHERE id=%s", $id)
            );
            $data = (array)$results;
            ?>
            <form class="anf" role="form" method="post">
                <input type="hidden" name="anf_hr" value="<?php echo get_bloginfo( 'url' ) ?>">
                <input type="email" name="anf_email" class="anf-input" placeholder="E-mail address">
                <input value="Subscribe" type="submit" class="anf-submit">
            </form>
            <?php
        }

        private function add_frontend_scripts() 
        {
            wp_enqueue_script('anf_script', plugins_url('../assets/frontend/script.js', __FILE__), array('jquery'));
            if(!is_admin()) {
                wp_localize_script( 'anf_script', 'anf', array(
                    'url' => admin_url('admin-ajax.php'),
                    'ajax_nonce' => wp_create_nonce( 'noncy_nonce' ),
                    'assets_url' => get_stylesheet_directory_uri(),
                ) );   
            }
        }

        public function form_submit()
        {   
            check_ajax_referer( 'noncy_nonce', 'nonce' );
            $data = urldecode( $_POST['data'] );

            if ( !empty( $data ) ) {
                $data_array = explode( "&", $data );
                $fields = [];
                foreach( $data_array as $array ) {
                    $array = explode( "=", $array );
                    $fields[ $array[0] ] = $array[1];
                }
            }

            if ( !empty( $fields ) ) :
                global $wpdb;
                
                // check if already exists
                
                /** @var int $count **/
                $count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter WHERE email = %s", $fields['ne'] ) );
                
                if( $count > 0 ) {
                    $output = array(
                        'status'    => 'error',
                        'msg'       => __( 'Already in a database.')
                    );
                } elseif( !defined( 'NEWSLETTER_VERSION' ) ) {
                    $output = array(
                        'status'    => 'error',
                        'msg'       => __( 'Please install & activate newsletter plugin.')
                    );           
                } else {
                    $status = 'C';
                    $opts = get_option('newsletter');
                    $opt_in = (int) $opts['noconfirmation'];
                    // This means that double opt in is enabled
                    // so we need to send activation e-mail
                    if ($opt_in == 0) {
                        // $newsletter = Newsletter::instance();
                        // $user = NewsletterUsers::instance()->get_user( $wpdb->insert_id );
                        // NewsletterSubscription::instance()->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
                        $status = 'S';
                    }

                    /**
                     * Generate token
                     */
                    
                    /** @var string $token */
                    $token =  wp_generate_password( rand( 10, 50 ), false );
                    $wpdb->insert( $wpdb->prefix . 'newsletter', array(
                            'email'         => $fields['anf_email'],
                            'status'        => $status,
                            'http_referer'  => $fields['anf_hr'],
                            'token'         => $token,
                        )
                    );
                    
                    $output = array(
                        'status'    => 'success',
                        'msg'       => __( 'Thank you!')
                    );	
                }
                
            else :
                $output = array(
                    'status'    => 'error',
                    'msg'       => __( 'An Error occurred. Please try again later.' )
                );
            endif;
            
            wp_send_json( $output );
        }
    }

}
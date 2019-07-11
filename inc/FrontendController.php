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

            $results = $this->db->get_row( 
                $this->db->prepare("SELECT * FROM wp_ajax_newsletter_forms WHERE id=%s", $id)
            );
            $data = (array)$results;

            ?>
            <form class="anf" role="form" method="post" data-id="anf_form_<?php echo $data['id']; ?>">
                <input type="hidden" name="anf_list_num" value="<?php echo $data['list_num'];?>">
                <input type="hidden" name="anf_hr" value="<?php echo get_bloginfo( 'url' ) ?>">
                <?php 
                    if($data['has_name_field'] == '1') {

                ?>
                <div class="anf-input-group">
                <input type="text" name="anf_name" class="anf-input" placeholder="Name" required>
                </div>
                <?php
                    }
                ?>
                <div class="anf-input-group">
                <input type="email" name="anf_email" class="anf-input" placeholder="E-mail address" required>
                </div>
                <input value="Subscribe" type="submit" class="anf-submit">
            </form>
            <div class="success message-box">
                Form submitted successfully
            </div>
            <div class="error message-box">
                There ws an error
            </div>
            <script>
                // (function($) {
                    var anf_form_<?php echo $data['id']; ?> = {
                        <?php if(null !== $data['onsuccess_jquery'] && $data['onsuccess_jquery'] != '') { ?>
                        onsuccess: function($) {
                            <?php echo stripcslashes($data['onsuccess_jquery']); ?>
                        },  
                        <?php } ?>
                        <?php if(null !== $data['onerror_jquery'] && $data['onerror_jquery'] != '') { ?>
                        onerror: function($) {
                            <?php echo stripcslashes($data['onerror_jquery']); ?>
                        },  
                        <?php } ?>

                    }
                // })(jQuery)
            </script>
            <?php
        }

        private function add_frontend_scripts() 
        {
            wp_enqueue_script('anf_script', plugins_url('../assets/frontend/script.js', __FILE__), array('jquery'));
            wp_enqueue_style( 'anf_style', plugins_url('../assets/frontend/style.css', __FILE__));
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
            $count = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter WHERE email = %s AND list_{$fields['anf_list_num']} = 1", $fields['anf_email'] ) );
                
                if( $count > 0 ) {
                    $output = array(
                        'status'    => 'success',
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
                        $newsletter = Newsletter::instance();
                        $user = NewsletterUsers::instance()->get_user( $wpdb->insert_id );
                        NewsletterSubscription::instance()->mail($user->email, $newsletter->replace($opts['confirmation_subject'], $user), $newsletter->replace($opts['confirmation_message'], $user));
                        $status = 'S';
                    }

                    $count_email = $wpdb->get_var( $wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}newsletter WHERE email = %s", $fields['anf_email'] ) );
                    
                    $token =  wp_generate_password( rand( 10, 50 ), false );
                    
                    if($count_email > 0) {
                        $wpdb->update($wpdb->prefix . 'newsletter', array(
                                "list_{$fields['anf_list_num']}"        => !!$fields['anf_list_num'],
                            ), 
                            array( 
                                'email' => $fields['anf_email']
                            )
                        );
                    } else {
                        $wpdb->insert( $wpdb->prefix . 'newsletter', array(
                                'email'         => $fields['anf_email'],
                                'name'          => $fields['anf_name'] ? $fields['anf_name'] : '',
                                'surname'       => $fields['anf_lname'] ? $fields['anf_lname'] : '',
                                "list_{$fields['anf_list_num']}"        => !!$fields['anf_list_num'],
                                'status'        => $status,
                                'http_referer'  => $fields['anf_hr'],
                                'token'         => $token,
                            )
                        );
                    }
                    
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
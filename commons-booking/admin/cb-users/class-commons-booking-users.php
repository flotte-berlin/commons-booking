<?php
/*
 * Handles the user registration & login process. 
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */
class Commons_Booking_Users extends Commons_Booking {


  public function __construct( ) {

    $this->settings = new CB_Admin_Settings;
    $this->termsservices_url = $this->settings->get_settings('pages', 'termsservices_url');

    $this->registration_fields = array ( 
      'username', 
      'password', 
      'email', 
      'first_name', 
      'last_name', 
      'phone', 
      'address', 
      'terms_accepted' 
      );    

    $this->extra_profile_fields = array (       
       'first_name' => array ( 
          'field_name' => 'first_name', 
          'title' => __( 'First Name', Commons_Booking::$plugin_slug ), 
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your first name', Commons_Booking::$plugin_slug ) 
          ),       
       'last_name' => array ( 
          'field_name' => 'last_name',
          'title' => __( 'Last Name', Commons_Booking::$plugin_slug ),  
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your last name', Commons_Booking::$plugin_slug ) 
          ),       
       'phone' => array ( 
          'field_name' => 'phone', 
          'title' => __( 'Phone Number', Commons_Booking::$plugin_slug ), 
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your phone number', Commons_Booking::$plugin_slug ) 
          ),       
       'address' => array ( 
          'field_name' => 'address', 
          'title' => __( 'Addresss', Commons_Booking::$plugin_slug ), 
          'type' => 'input', 
          'description' => '', 
          'errormessage' => __('Please enter your Address', Commons_Booking::$plugin_slug ) 
          ),       
      'terms_accepted' => array ( 
          'title' => __( 'Terms and Conditions', Commons_Booking::$plugin_slug ), 
          'field_name' => 'terms_accepted', 
          'type' => 'checkbox', 
          'description' => __( 'I accept the terms & conditions' ),
          'errormessage' => __('Please accept the terms & conditions', Commons_Booking::$plugin_slug ) 
          )
      );
    $this->mail_vars = array();
 
    $this->registration_fields_required = $this->registration_fields;

    $this->r_vars = array();
    
    $this->user_fields = $this->get_extra_profile_fields();

    }

    /*
    *   Adds the fields to the wordpress registration
    *
    * @since    0.6
    *
    */
    public function registration_add_fields() {


      foreach ($this->user_fields as $field) {

            $row = ( ! empty( $_POST[ $field['field_name'] ] ) ) ? trim( $_POST[ $field['field_name'] ] ) : '';
            ?>
            <p>
                <?php if ( $field['type'] == 'checkbox' ) { ?>
                    <label for="<?php _e( $field['field_name'] ) ?>"><?php _e( $field['title'], Commons_Booking::$plugin_slug ) ?><br />
                        <input type="checkbox" name="<?php _e( $field['field_name'] ) ?>" id="<?php _e( $field['field_name'] ) ?>" value="yes" <?php if ( $_POST[ $field['field_name'] ]  == "yes") echo "checked"; ?> /><?php _e( $field['description'], Commons_Booking::$plugin_slug ) ?><br />
                    </label>
                    <?php echo $this->get_termsservices_string() ?>
                <?php } else { ?>
                    <label for="<?php _e( $field['field_name'] ) ?>"><?php _e( $field['title'], Commons_Booking::$plugin_slug ) ?><br />
                        <input type="text" name="<?php _e( $field['field_name'] ) ?>" id="<?php _e( $field['field_name'] ) ?>" class="input" value="<?php echo esc_attr( wp_unslash( $_POST[ $field['field_name'] ] ) ); ?>" size="25" /><?php _e( $field['description'], Commons_Booking::$plugin_slug ) ?>
                    </label>
                <? } ?>
            </p>
            <?php
         }
    }

    /*
    *   Adds error handling
    *
    * @since    0.6
    *
    * @return    object 
    */
    public function registration_set_errors( $errors, $username, $email) {

        foreach ($this->user_fields as $field) {

            if ( $field['type'] == 'checkbox' ) {
                if ( !isset( $_POST[ $field[ 'field_name' ]]) ) {
                    $errors->add( $field[ 'field_name' ] . '_error', $field[ 'errormessage' ]);
                }
            } else {
                if ( empty( $_POST[ $field[ 'field_name' ] ] ) || ! empty( $_POST[ $field[ 'field_name' ] ] ) && trim( $_POST[ $field[ 'field_name' ] ] ) == '' ) {
                    $errors->add( $field[ 'field_name' ] . '_error', $field[ 'errormessage' ]);
                }
            }
        }
        return $errors;
    }

   /*
   *   Write user meta 
   *
   * @since    0.6
   *
   */
   public function registration_add_meta( $user_id ) {

       foreach ($this->user_fields as $field) {
           if ( !empty( $_POST[ $field[ 'field_name' ]] ) ) {
               update_user_meta( $user_id, $field[ 'field_name' ], trim( $_POST[ $field[ 'field_name' ]] ) );
               }
       }
   }

  /**
   * Registration Form: Set terms & services String (Wrapped in URL)
   *
   * @since    0.6
   * 
   * @return string
   */
  public function get_termsservices_string() {
    if ( !empty ( $this->termsservices_url ) ) {
      $string = sprintf( __( '<a href="%s" target=_blank">Read the terms and services</a>', Commons_Booking::$plugin_slug ), 
        $this->termsservices_url );
    } else {
      $string = "";      
    }
    return $string;
  }

    /*
    * CUSTOMIZE - change link of logo on login page
    *
    * @since    0.6
    *
    */ 
    ////   
  public function cb_login_custom_site_url($url) {  
    return get_bloginfo('url'); //return the current wp blog url  
    }  

    /*
    * CUSTOMIZE - Change the title
    *
    * @since    0.6
    *
    */  
    public function cb_login_header_title($message) {  
      return False; /*return the description of current blog */  
    }  
    /*
    * CUSTOMIZE - Set custom Logo
    *
    * @since    0.6
    *
    */
  public function cb_login_logo() {
    $logo_url = $this->settings->get_settings('customize', 'customize_logofile');

    if (!empty( $logo_url ) ) {
      printf ('<style type="text/css">
      h1 a { background-image: url(%s) !important; }
      </style>', $logo_url );
    }
  }
    /*
    * CUSTOMIZE - Inject custom css (set up under the customize-tab)
    *
    * @since    0.6
    *
    */
  public function cb_login_custom_css() {
    $css = $this->settings->get_settings('customize', 'customize_css');
    printf ('<style type="text/css">%s</style>', $css);
  }
    /*
    * CUSTOMIZE - Redirects Non-Admin Users after Login
    *
    * @since    0.6
    *
    */
  public function cb_login_redirect( $redirect_to, $request, $user ) {
      //is there a user to check?
      global $user;
      if ( isset( $user->roles ) && is_array( $user->roles ) ) {
          //check for admins
          if ( in_array( 'administrator', $user->roles ) ) {
              // redirect them to the default place
              return $redirect_to;
          } 
        }
        return home_url();
  }

    /*
    * CUSTOMIZE - Redirects User after Profile update
    *
    * @since    0.6
    *
    */
    public function cb_user_profile_redirect() {
        
        wp_redirect( trailingslashit( home_url() ) );
        exit;
        
    }    
    /*
    * CUSTOMIZE - Prevents Subscribers from accessing the Dashboard, redirect to Profile
    *
    * @since    0.6
    *
    */
    public function cb_redirect_to_profile() {
 
    if ( ! defined( 'DOING_AJAX' ) ) {
 
      $current_user   = wp_get_current_user();
      $role_name      = $current_user->roles[0];
      global $pagenow;
      
      if( 'subscriber' === $role_name && $pagenow == 'index.php'){ // if subscriber & trying to access the dashboard
          wp_redirect(admin_url('/profile.php', 'http'), 301);
          exit;
      }
 
      } // if DOING_AJAX
 
    }


  /**
   * Get the additional User fields
   *
   * @since    0.6.
   * 
   * @return array
   */
  public function get_extra_profile_fields() {
    return $this->extra_profile_fields;
  }
  
  /**
   * Sets a flat array of user field/value pairs
   *
   * @since    0.6
   * 
   */
  public function set_basic_user_vars( $user_id ) {
    
      $user_basic = get_user_by( 'id', $user_id );
      $user_meta = get_user_meta( $user_id );

      // transform from object to an array that the replace_template_tags functions expects
      $user_basic_array =  object_to_array ($user_basic);
      
      $user_meta_array = array();
      foreach ($user_meta as $key => $value) {
          $user_meta_array[$key] = $value[0];
      }

      // merge the arrays
      $this->user_vars = array_merge($user_basic_array['data'], $user_meta_array);
  }

  /**
   * Add addiotinal key/value pairs to the user_vars array  
   *
   * @since    0.5.3
   * 
   */
  public function add_user_vars( $key, $value ) {
      
      $this->user_vars[$key] = $value;
  }

  /**
   * Get the user_vars array  
   *
   * @since    0.5.3
   * 
   */
  public function get_user_vars( ) {

      return $this->user_vars;
  }

  /**
   * Set the activation url   
   *
   * @since    0.5.3
   * 
   */
  public function set_activation_url($key, $login) {

      $activation_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($login), 'login');
  }


  /**
   * Backend: Show the extra profile fields
   *
   * @since    0.2
   *
   */
  public function show_extra_profile_fields( $user ) { ?>

        <h3><?php _e ( 'Extra Fields', Commons_Booking::$plugin_slug ); ?> </h3>

        <table class="form-table">
            <tr>
                <th><label for="phone"><?php _e ( 'Phone number', Commons_Booking::$plugin_slug ); ?></label></th>
                <td>
                    <input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
                </td>
            </tr>               
            <tr>
                <th><label for="address"><?php _e ( 'Address', Commons_Booking::$plugin_slug ); ?></label></th>
                <td>
                    <input type="textarea" name="address" id="address" value="<?php echo esc_attr( get_the_author_meta( 'address', $user->ID ) ); ?>" class="regular-text" /><br />
                </td>
            </tr>            
            <tr>
                <th><label for="terms_accepted"><?php _e ( 'Terms and conditions', Commons_Booking::$plugin_slug ); ?></label></th>
                <td>
                    <input type="checkbox" name="terms_accepted" id=" terms_accepted " disabled value="yes" <?php if (esc_attr( get_the_author_meta( "terms_accepted", $user->ID )) == "yes") echo "checked"; ?> /><?php __( 'Accepted Terms & Conditions', Commons_Booking::$plugin_slug); ?><br />
                </td>
            </tr>
        </table>
    <?php }

  /**
  * Backend: Update dte extra profile fields
  *
  * @since    0.2
  *
  */
  public function save_extra_profile_fields( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
      return false;

    update_user_meta( $user_id, 'phone', $_POST['phone'] );
    update_user_meta( $user_id, 'address', $_POST['address'] );
    // update_user_meta( $user_id, 'terms_accepted', $_POST['terms_accepted'] );
    update_user_meta( $user_id, 'confirmed', $_POST['confirmed'] );
  }


  /**
  * Frontend: User Page
  *
  * @since    0.2
  *
  */
  public function render_user_bookings_page() {
    
    if ( is_user_logged_in() ) {

        $current_user = wp_get_current_user();
        printf(__('Welcome, %s! ', Commons_Booking::$plugin_slug ), $current_user->user_firstname);
        echo ('<span class="align-right">');
          printf(__('<a href="%s">Profile</a>', Commons_Booking::$plugin_slug ),  get_edit_profile_url() );
          echo ( ' | ');
          printf(__('<a href="%s">Logout</a>', Commons_Booking::$plugin_slug ), wp_logout_url( home_url() ) );
        echo ('</span>');

        $user_bookings = $this->get_user_bookings( $current_user->ID );

        if ( !empty ($user_bookings) ) {

          $review_page_id = $this->settings->get_settings('pages', 'bookingconfirm_page_select');
          include (commons_booking_get_template_part( 'user', 'bookings', FALSE )); 

        } else {
          echo __( 'You haven´t booked anything yet.', Commons_Booking::$plugin_slug); 
        }

    } else { // Message and Login/Registration Links. 

      printf (__( 'You need to be logged in to book items. <br>Please <a href="%s">Log in</a> or <a href="%s">register</a>.', Commons_Booking::$plugin_slug), wp_login_url(), wp_registration_url());     
    }
 }

/**
 * Get all booking-data as array
 *
 * @return array
 */   
 public function get_user_bookings( $user_id) {
   
   global $wpdb;
   $table_bookings = $wpdb->prefix . 'cb_bookings';

   $sqlresult = $wpdb->get_results("SELECT * FROM $table_bookings WHERE user_id = $user_id", ARRAY_A);          

   return $sqlresult;
 }

  /**
   * Sends the registration email.
   *
   * @since    0.2
   *
   * @param $to email adress 
   */   
  public function send_registration_mail( ) {

    $this->email_messages = $this->settings->get_settings( 'mail' ); // get email templates from settings page
    $body_template = ( $this->email_messages['mail_registration_body'] );  // get template
    $subject_template = ( $this->email_messages['mail_registration_subject'] );  // get template

    $vars = $this->get_user_vars();
    $headers = array('Content-Type: text/html; charset=UTF-8'); 

    $to = $vars['user_email'];
    $body = replace_template_tags( $body_template, $vars );
    $subject = replace_template_tags( $subject_template, $vars );

    wp_mail( $to, $subject, $body, $headers ); 

  }

}



// Overwrite the user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {

      if ( $deprecated !== null ) {
        _deprecated_argument( __FUNCTION__, '4.3.1' );
      }        

      $user = new WP_User( $user_id );
      $cb_user = new Commons_Booking_Users();

      global $wpdb, $wp_hasher;

      // The blogname option is escaped with esc_html on the way into the database in sanitize_option
      // we want to reverse this for the plain text arena of emails.
      $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

      // Admin Message

      $user_login = stripslashes($user->user_login);
      $user_email = stripslashes($user->user_email);

      $message  = sprintf(__('New user registration on your blog %s:'), get_option('blogname')) . "<br>";
      $message .= sprintf(__('Username: %s'), $user_login) . "<br>";
      $message .= sprintf(__('E-mail: %s'), $user_email) . "<br>";

      @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), get_option('blogname')), $message);

      // if notification disabled, return.
      if ( 'admin' === $notify || empty( $notify ) ) {
          return;
        }

      // Generate something random for a password reset key.
      $key = wp_generate_password( 20, false );

      /** This action is documented in wp-login.php */
      do_action( 'retrieve_password_key', $user->user_login, $key );

      // Now insert the key, hashed, into the DB.
      if ( empty( $wp_hasher ) ) {
        require_once ABSPATH . WPINC . '/class-phpass.php';
        $wp_hasher = new PasswordHash( 8, true );
      }
      $hashed = time() . ':' . $wp_hasher->HashPassword( $key );
      $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );


      // User Message 

      $cb_user->set_basic_user_vars( $user_id );
      $activation_url = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');

      $cb_user->add_user_vars( 'ACTIVATION_URL', $activation_url );
      $cb_user->add_user_vars( 'USER_NAME', $user_login );

      // var_dump($registered_user );

      $cb_user->send_registration_mail( );

 
    }
}



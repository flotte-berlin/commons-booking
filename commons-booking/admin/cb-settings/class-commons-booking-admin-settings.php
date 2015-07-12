<?php 
/**
 *
 * @package   Commons_Booking
 * @author    Florian Egermann <florian@wielebenwir.de>
 * @license   GPL-2.0+
 * @link      http://www.wielebenwir.de
 * @copyright 2015 wielebenwir
 */

/**
 * Get the admin defined settings from backend.  
 *
 * @package Commons_Booking_Admin_Settings
 * @author  Florian Egermann <florian@wielebenwir.de>
 */

class Commons_Booking_Admin_Settings {

  public $prefix;

  public $setting_page;
  public $setting_name;


/**
 * Constructor.
 */
  public function __construct() {

    $this->prefix = 'commons-booking';
    $p =  $this->prefix; 

}

  /**
  * Set the default values for the settings. 
  * Loop through each setting, if set, keep it otherwise write defaults
  */
  public function set_defaults($defaults) {
    
    foreach ($defaults as $d_page => $d_contents) { // get the option d_page / array
      $option = get_option( $d_page );
      foreach ($d_contents as $d_key => $d_value) {
        if ( empty( $option[$d_key] ) ) { // ignore if already set
          $option[$d_key] = $d_value; 
        } 
      }

      update_option( $d_page, $option );
  }

}


/**
 * Get settings from backend. Return either full array or specified setting
 * If array, remove the prefix for easier retrieval
 *
 *@param setting_page: name of the page (cmb metabox name)
 *@param (optional) name of the setting
 *@param (optional) key/value pairs of the template tags
 *
 *@return string / array
 */
  public function get( $setting_page, $setting_name = "") {
    global $wpdb;
    $page = get_option( $this->prefix . '-settings-' .$setting_page ); 

    if ( $setting_name ) {
      return $page [ $this->prefix . '_'. $setting_name ];
    } else { // grabbing all entries     
      foreach($page as $key => $value) { // remove the prefix 
            $clean = str_replace( $this->prefix. '_', '', $key); 
            $clean_array[$clean] = $value; 
        }
      return $clean_array;
    }
  }

}

?>
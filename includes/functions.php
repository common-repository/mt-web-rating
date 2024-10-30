<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

//Load Star Rating builder componnents
function add_sr_builder() {

    wp_register_script('vue', plugin_dir_url( __FILE__ ).'js/vue.min.js' );
    wp_register_script('resize-sensor', plugin_dir_url( __FILE__ ).'js/resize-sensor.js' );
    wp_enqueue_script( 'mt-sr-builder', plugin_dir_url( __FILE__ ).'js/mt-sr-builder.js', array('vue', 'resize-sensor'), false, true);

    /*wp_register_script(
        'mt-sr',
        plugin_dir_url( __FILE__ ).'js/mt-sr/mt-sr.min.js',
        array( 'jquery' ),
        false,
        true
    );
    wp_enqueue_script( 'mt-sr' );*/

    wp_enqueue_style( 'mt-sr-default-style', plugin_dir_url( __FILE__ ).'css/mt-sr-default-style.css' );

}

add_action( 'wp_enqueue_scripts' , 'add_sr_builder' );

// Add a Shortcode
function mt_star_rating_shortcode( $atts ) {

    $defaults = array(
        'google_id' => get_option( PLUGIN_PREFIX . 'google_id' ),
        'facebook_id' =>  get_option( PLUGIN_PREFIX . 'facebook_id' ),
        'tripadvisor_id' => get_option( PLUGIN_PREFIX . 'tripadvisor_id' ),
    );

    $atts = shortcode_atts(
        array(
            'type' => 'google',
            'id' => '',
            'title' => 'undefined',
        ), $atts, 'mt_star_rating' );

    if ($atts['id'] == ''){
        $id = $defaults[$atts['type'].'_id'];
    }
    else {
        $id = $atts['id'];
    }

    if ($atts['title'] == 'undefined'){
        $title = ucwords($atts['type']);
    }
    else {
        $title = $atts['title'];
    }

    return create_star_rating( $atts['type'].'?id='.$id, $title);

}

add_shortcode( 'mt_star_rating', 'mt_star_rating_shortcode' );

// Create Appropriate HTML String
function create_star_rating( $note_request, $title, $max_note_value = "5" ){
    $request_url = APP_URL.$note_request.'&max='.$max_note_value;
    $htmlString = '<div id="'.uniqid('mt_star_rating_').'" class="mt_star_rating"><mt_star_rating request_url="' . $request_url . '" title="' . $title . '" v-bind:max="' . $max_note_value . '" ></mt_star_rating></div>';
    return $htmlString;
}

/*
 * GET NOTE VALUE
 */

    // gets google place rating from mt-star-rating web app
    function getGooglePlaceRating($googlePlaceID){

        $requestURL = APP_URL."google?id=".$googlePlaceID;

        $appResponse = file_get_contents($requestURL);

        $noteValue = $appResponse;

        return $noteValue;
    }

    // gets facebook page rating from mt-star-rating web app
    function getFacebookPageRating($facebookPageSlug){
        $requestURL = APP_URL."facebook?id=".$facebookPageSlug;
        $appResponse = file_get_contents($requestURL);
        $noteValue = $appResponse;
        return $noteValue;
    }

    // gets tripadvisor page rating from mt-star-rating web app
    function getTripadvisorPageRating($tripadvisorPageSlug){
        $requestURL = APP_URL."tripadvisor?id=".$tripadvisorPageSlug;
        $appResponse = file_get_contents($requestURL);
        $noteValue = $appResponse;
        return $noteValue;
    }


/*
 * Add my new menu to the Admin Control Panel
 */

class plugin_settings_page
{

    public $page_title;// = 'MT Star Rating Settings Page';
    public $menu_title;// = 'MT Star Rating';
    public $slug;// 'mt_sr_settings';
    public $icon;
    public $position;
    public $capability = 'manage_options';
    public $fields;

    public function __construct($sections, $fields , $page_title, $menu_title, $slug, $icon = 'dashicons-admin-plugins', $position = 100 ) {
        $this->page_title = $page_title;
        $this->menu_title = $menu_title;
        $this->slug = $slug;
        $this->icon = $icon;
        $this->position = $position;

        $this->sections = $sections;
        $this->fields = $fields;

        add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
        add_action( 'admin_init', array( $this, 'setup_sections' ) );
        add_action( 'admin_init', array( $this, 'setup_fields' ) );
    }

    public function plugin_settings_page_generator() { ?>
        <div class="wrap">
            <h2><?php echo $this->page_title ?></h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields( $this->slug );
                    do_settings_sections( $this->slug );
                    submit_button();
                ?>
            </form>
        </div>
    <?php }

    public function create_plugin_settings_page() {
        $generator = array( $this, 'plugin_settings_page_generator' );
        add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->slug, $generator, $this->icon, $this->position );
    }

    public function setup_sections() {
        //add_settings_section( 'rating_ids', 'Identifiants Plateformes', false, $this->slug );
        $sections = $this->sections;
        foreach( $sections as $section ){
            add_settings_section( $section['id'], $section['title'], false, $this->slug );
        }
    }

    public function setup_fields() {

        $fields = $this->fields;

        foreach( $fields as $field ){
            add_settings_field( $field['uid'], $field['label'], array( $this, 'field_generator' ), $this->slug, $field['section'], $field );
            register_setting( $this->slug, $field['uid'] );
        }
    }

    public function field_generator( $arguments ) {
        $value = get_option( $arguments['uid'] ); // Get the current value, if there is one
        if( ! $value ) { // If no value exists
            $value = $arguments['default']; // Set to our default
        }

        // Check which type of field we want
        switch( $arguments['type'] ){
            case 'text': // If it is a text field
                printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
                break;
        }

        // If there is help text
        if( $helper = $arguments['helper'] ){
            printf( '<span class="helper"> %s</span>', $helper ); // Show it
        }

        // If there is supplemental text
        if( $supplimental = $arguments['supplemental'] ){
            printf( '<p class="description">%s</p>', $supplimental ); // Show it
        }
    }

}

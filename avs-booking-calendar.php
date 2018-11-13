<?php
/**
 * @package AVSrent_Booking_Calendar
 * @version 0.9.6
 */
/*
Plugin Name: AVS.rent Booking Calendar
Plugin URI: https://phcom.de
Description: Connect AVS.rent booking services with Wordpress. Please modify all texts in customizer. News: v.0.9.6 remove iFrame integration to improve user experience.<br><small><strong>Roadmap:</strong> v.1.0 using AVS-token to get all rental vehicle and locations automaticly.</small>
Author: Andy Gellermann for PHCOM
Version: 0.9.6
Author URI: https://phcom.de

*/
if ( $_SERVER['SCRIPT_FILENAME'] == __FILE__ )
	die( 'Access denied.' );

$version = "0.9.6";

register_activation_hook(__FILE__,  'install_avsbookingcal_plugin',999);
register_deactivation_hook(__FILE__, 'remove_avsbookingcal_plugin_setups');

add_action('admin_enqueue_scripts', 'avsbookingcal_scripts');
//add_action('plugins_loaded', 'avsbookingcal_version_check',999);

function avsbookingcal_scripts() {
    if ( is_page() && is_front_page() ){
	    wp_enqueue_script( 'momentjs', plugin_dir_url( __FILE__ ) . 'js/moment.min.js', array(), '1.0.0', true );
	    wp_enqueue_script( 'avsbookingcalscript', plugin_dir_url( __FILE__ ) . 'js/caleran.min.js', array('jquery'), '1.0.1', true );
	    $datepicker_init = "

			jQuery('#avs_booking_date').caleran({
          		showFooter: false,
          		locale: 'de',
          		showButtons: true,
          		startOnMonday: true,
          		rangeLabel: 'Zeitraum',
          		cancelLabel: 'Abbruch',
          		applyLabel: 'OK',
          		dateSeparator: ' > ',
          		monthSwitcherFormat: 'MMMM',
          		disabledRanges: [{
					'start': moment('01.01.1970','DD.MM.YYYY'),
					'end': moment('" . date("d.m.Y") . "','DD.MM.YYYY')
				}]
      		});	
	";

	    wp_add_inline_script( 'avsbookingcalscript', $datepicker_init, 'after' );
    }
}
add_action( 'wp_enqueue_scripts', 'avsbookingcal_scripts' );

wp_register_style( 'fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'screen' );
wp_register_style( 'avsbookingcalenderstyle', plugin_dir_url( __FILE__ ) . 'css/caleran.min.css', array(), '1', 'screen' );

function add_calendar_style() {
	if ( is_front_page() ){
		wp_enqueue_style('fontawesome');	
		wp_enqueue_style('avsbookingcalenderstyle');	
	}
}
add_action( 'wp_enqueue_scripts', 'add_calendar_style' );



// Installation:
function install_avsbookingcal_plugin(){	
  global $wpdb;
	add_option('avsbookingcal_version', $version);
}


function add_calendar_shortcode(){
	add_shortcode( 'avs_booking_cal', 'avsbookingcal_shortcode' );
}
add_action('wp_loaded','add_calendar_shortcode');

function avsbookingcal_shortcode() {

	?>
    <style>
    	/*#avs_booking_cal {
    		position: absolute;
    		display: block;
    		left: 19px;
    		top: 340px;
    		z-index:6;
    	}
    	.avs_booking_cal form {
    		display: inline;
    	}*/
    	select.rental_object {
		  -moz-appearance: none;
		  -webkit-appearance: none;
		  appearance: none;
		}
		/*
		select.object::-ms-expand {
		  display: none;
		}*/
    	input.datepicker, select.rental_object {
			font-family: 'Montserrat', 'sans-serif', 'Arial';
			font-size: 1.5rem;
			font-weight: 600;
			height: 40px;
			padding: 0;
    		padding-left: 20px;
            position: relative;
            border-radius: 3px;
            border: 1px solid #dfe5e8;
            color: #3c3950;
            background-color: #fff;
            max-width: 100%;
    		vertical-align: middle;
        	background-position: 93% 50%;
        	background-size: 10%;
        	background-repeat: no-repeat;
        	padding-right: 20px;
        	/*background-blend-mode: difference;*/
        	margin: 25px 0px 25px 25px;
            width: 250px;
    	}
    	select.rental_object {
 			display: inline-block;
            width: 300px;
    	}
    	input.datepicker, select.rental_object, input.booking_button{
        	box-shadow: 0px 0px 5px 1px rgba(0,0,0,0.3);
    	}
    	input.booking_button {
    		background-color: rgb(221, 27, 60);
    	}
        .datepicker {
        	background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/PjwhRE9DVFlQRSBzdmcgIFBVQkxJQyAnLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4nICAnaHR0cDovL3d3dy53My5vcmcvR3JhcGhpY3MvU1ZHLzEuMS9EVEQvc3ZnMTEuZHRkJz48c3ZnIGhlaWdodD0iMzJweCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMzIgMzI7IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAzMiAzMiIgd2lkdGg9IjMycHgiIHhtbDpzcGFjZT0icHJlc2VydmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPjxnIGlkPSJMYXllcl8xIi8+PGcgaWQ9ImNhbGVuZGFyX3g1Rl9hbHRfeDVGX2ZpbGwiPjxnPjxwYXRoIGQ9Ik0yNiw0djIuMDQ3YzAsMi4yMTEtMS43ODksNC00LDRzLTQtMS43ODktNC00VjRoLTR2Mi4wNDdjMCwyLjIxMS0xLjc4OSw0LTQsNHMtNC0xLjc4OS00LTRWNEgwdjI4ICAgIGgzMlY0SDI2eiBNMTAsMjhINnYtNGg0VjI4eiBNMTAsMjBINnYtNGg0VjIweiBNMTgsMjhoLTR2LTRoNFYyOHogTTE4LDIwLjAwOGgtNHYtNGg0VjIwLjAwOHogTTIyLDI4di00aDRMMjIsMjh6IE0yNiwyMGgtNHYtNGg0ICAgIFYyMHoiIHN0eWxlPSJmaWxsOiM0RTRFNTA7Ii8+PHBhdGggZD0iTTgsNlYyYzAtMS4xMDUsMC44OTUtMiwyLTJzMiwwLjg5NSwyLDJ2NGMwLDEuMTA1LTAuODk1LDItMiwyUzgsNy4xMDUsOCw2eiIgc3R5bGU9ImZpbGw6IzRFNEU1MDsiLz48cGF0aCBkPSJNMjAsNlYyYzAtMS4xMDUsMC44OTUtMiwyLTJzMiwwLjg5NSwyLDJ2NGMwLDEuMTA1LTAuODk1LDItMiwyUzIwLDcuMTA1LDIwLDZ6IiBzdHlsZT0iZmlsbDojNEU0RTUwOyIvPjwvZz48L2c+PC9zdmc+');
        }
        .rental_object {
        	background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiA/Pjxzdmcgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgMjQgMjQ7IiB2ZXJzaW9uPSIxLjEiIHZpZXdCb3g9IjAgMCAyNCAyNCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayI+PGcgaWQ9ImluZm8iLz48ZyBpZD0iaWNvbnMiPjxnIGlkPSJtZW51Ij48cGF0aCBkPSJNMjAsMTBINGMtMS4xLDAtMiwwLjktMiwyYzAsMS4xLDAuOSwyLDIsMmgxNmMxLjEsMCwyLTAuOSwyLTJDMjIsMTAuOSwyMS4xLDEwLDIwLDEweiIvPjxwYXRoIGQ9Ik00LDhoMTJjMS4xLDAsMi0wLjksMi0yYzAtMS4xLTAuOS0yLTItMkg0QzIuOSw0LDIsNC45LDIsNkMyLDcuMSwyLjksOCw0LDh6Ii8+PHBhdGggZD0iTTE2LDE2SDRjLTEuMSwwLTIsMC45LTIsMmMwLDEuMSwwLjksMiwyLDJoMTJjMS4xLDAsMi0wLjksMi0yQzE4LDE2LjksMTcuMSwxNiwxNiwxNnoiLz48L2c+PC9nPjwvc3ZnPg==');
        }
    	.avsbookingcal_container {
    		top: 400px;
    	}
    	.caleran-container-mobile .caleran-input .caleran-header {
    		padding: 25px 12px 12px;
    	}
        @media only screen and (max-width: 600px) {
			input.datepicker, select.rental_object {
	        	background-position: 95% 50%;
	        	background-size: 25px;
	        	margin: 0px 10px 10px;
	            width: 95%;

			}
	    	input.booking_button {
	        	margin: 0px 10px 10px;
	            width: 95%;
	    	}
	    	.avsbookingcal_container {
	    		top: 50% !important;
	    	}
		}
    </style>
    <div class="tp-parallax-wrap avsbookingcal_container" style="position: absolute; display: block; visibility: visible; width: 100%; top: 450px; z-index: 6;">
    	<form method="post" style="text-align: center;">
    		<label class="datepicker_wrapper">
    			<input id="avs_booking_date" name="avs_booking_date" placeholder="<?php echo strtoupper(get_theme_mod('avsbookingcal_text_pleasechoose')) ?>" type="text" class="datepicker " autocomplete="off" required="required"">
    		</label>
    		<label class="object_wrapper">
	    		<select name="object" class="rental_object" required="required" />
					<option value=""><?php echo strtoupper(get_theme_mod('avsbookingcal_text_choose_rental_object')) ?></option>
					<?php 
					$rental_objects = nl2br(trim(get_theme_mod( 'avsbookingcal_rental_objects' ))); 
					$rental_arr = explode('<br />', $rental_objects);
					foreach ( (ARRAY) $rental_arr as $rent_id => $rent_str ){
						list( $id, $name ) = explode(',', $rent_str);
						echo '					<option value="' . $id . '">' . $name . '</option>
';
					}

					?>
				</select>
			</label>
    		<input type="submit" name="check_availabilities" value="<?php echo get_theme_mod('avsbookingcal_text_submit') ?>" class="booking_button">
    	</form></div>
	<?php
}

function avsbookingcal_customize_register( $wp_customize ) {
	
	$customizer_sections = array(
		array(
			'id' => 'avsbookingcal',
			'title' => __( 'AVS Integration', 'avsbookingcal' ),
			'priority' => 110,
			'fields' => array(
				array(
					'id' => 'avsbookingcal_url_to_avs',
					'label' => __( 'AVS-URL (No trailing "/")', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_rental_objects',
					'label' => __( 'Rental objects: "id","Title for selectbox"', 'avsbookingcal' ),
					'type' => 'textarea'
				),
				array(
					'id' => 'avsbookingcal_text_choose_time_range',
					'label' => __( 'Choose dates', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_text_pickup',
					'label' => __( 'Pickup text', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_text_bringback',
					'label' => __( 'Bring back text', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_text_pleasechoose',
					'label' => __( 'Please choose', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_text_pleasechoose',
					'label' => __( 'Please choose', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_text_choose_rental_object',
					'label' => __( 'Choose rental object', 'avsbookingcal' ),
					'type' => 'text'
				),
				array(
					'id' => 'avsbookingcal_text_submit',
					'label' => __( 'Submit button text', 'avsbookingcal' ),
					'type' => 'text'
				)
			)
		)
	); 

	foreach( $customizer_sections as $customizer_section ) : 
	
		$wp_customize->add_section( $customizer_section[ 'id' ] , array(
		    'title'    => $customizer_section[ 'title' ],
		    'priority' => $customizer_section[ 'priority' ]
		) ); 
	
		foreach( $customizer_section[ 'fields' ] as $customizer_section_field ) : 
		
			$wp_customize->add_setting( $customizer_section_field[ 'id' ] , array(
			    'transport' => 'refresh',
			) );
			
			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, $customizer_section_field[ 'id' ], array(
			    'label'    	=> $customizer_section_field[ 'label' ],
			    'section'  	=> $customizer_section[ 'id' ],
			    'settings' 	=> $customizer_section_field[ 'id' ],
			    'type' 		=> $customizer_section_field[ 'type' ],
			) ) );
			
		endforeach; 
	
	endforeach;
	
}
add_action( 'customize_register', 'avsbookingcal_customize_register' );


/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function avsbookingcal_customize_preview_js() {
	wp_enqueue_script( 'avsbookingcal-customizer', plugin_dir_url( __FILE__ ) . 'js/customizer.js', array( 'customize-preview' ), '20151215', true );
	flush_rewrite_rules();
}
add_action( 'customize_preview_init', 'avsbookingcal_customize_preview_js' );

function avsbookingcal_url_request_builder( $avs_booking_date ){

	if ( strlen( esc_html( $avs_booking_date ) ) > 10 ){

		list( $from, $to ) = explode( " > ", strip_tags($avs_booking_date) );

		list( $f['d'], $f['m'], $f['y'] ) = explode( ".", $from );
		list( $t['d'], $t['m'], $t['y'] ) = explode( ".", $to );

		$start = mktime( 6, 0, 0, $f['m'], $f['d'], $f['y'] );
		$stop = mktime( 6, 0, 0, $t['m'], $t['d'], $t['y'] );

	} else {

		$start = mktime( 6, 0, 0, $date('m'), $date('d')+2, $date('y') );
		$stop = mktime( 6, 0, 0, $date('m'), $date('d')+5, $date('y') );

	}
	$avs_link = get_theme_mod('avsbookingcal_url_to_avs') . '?from=linkgenerator&lang=de&referer=https://kuehlwerk.ch&fahrzeugsubgruppe_id=' . trim($_POST['object']) . '&start=' . $start . '&stop=' . $stop . '&station_id=1';
	return $avs_link;

}

/*

	Redirect booking-form action to call avs.rent client-booking page

*/

if ( $_POST['avs_booking_date'] ){

	function avsbookingcal_open_avs_direct() {

		$avs_link = avsbookingcal_url_request_builder($_POST['avs_booking_date']);
		header('Location: ' . nl2br($avs_link) );
		exit;

	}
	add_action( 'wp_loaded', 'avsbookingcal_open_avs_direct' );

}

function upgrade_avsbookingcal_plugin($version){
	global $wpdb;
	
	// $option['avsbookingcal_integrator_script'] = str_replace(array("https://","http://"),"",get_option('avsbookingcal_integrator_script'));
	
	// Installation durchführen
	install_avsbookingcal_plugin();
	
	// Options-Backup zurückspielen
	/*

	foreach($option as $option_key => $option_value){
		update_option($option_key, $option_value);
	}
	 */
	update_option('avsbookingcal_version', $version);
	
	// WPDB Aufräumen
	$wpdb->query("OPTIMIZE TABLE $wpdb->options");
		
}

// Deinstallation
function remove_avsbookingcal_plugin_setups() {
	
  global $wpdb;
	
	delete_option('avsbookingcal_version');
  
  // Abschluss der Deinstallation: WPDB Aufräumen
	$wpdb->query("OPTIMIZE TABLE $wpdb->options");
}
?>
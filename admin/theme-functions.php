<?php

/* These are functions specific to the included option settings and this theme */

// Commented out what might not be necessary for WPFolio 

/*-----------------------------------------------------------------------------------*/
/* Theme Header Output - wp_head() */
/*-----------------------------------------------------------------------------------*/

// This sets up the layouts and styles selected from the options panel

if (!function_exists('optionsframework_wp_head')) {
	function optionsframework_wp_head() { 
		$shortname =  get_option('of_shortname');
	    
		//Styles
		 if(!isset($_REQUEST['style']))
		 	$style = ''; 
		 else 
	     	$style = $_REQUEST['style'];
	     if ($style != '') {
			  $GLOBALS['stylesheet'] = $style;
	          echo '<link href="'. OF_DIRECTORY .'/styles/'. $GLOBALS['stylesheet'] . '.css" rel="stylesheet" type="text/css" />'."\n"; 
	     } else { 
	          $GLOBALS['stylesheet'] = get_option('of_alt_stylesheet');
	          if($GLOBALS['stylesheet'] != '')
	               echo '<link href="'. OF_DIRECTORY .'/styles/'. $GLOBALS['stylesheet'] .'" rel="stylesheet" type="text/css" />'."\n";         
	          else
	               echo '<link href="'. OF_DIRECTORY .'/styles/default.css" rel="stylesheet" type="text/css" />'."\n";         		  
	     }       
			
		// This prints out the custom css and specific styling options
		of_options_output_css();
		of_head_css();
		
		// Get options for WebFont and default font and put into vars. Might be better to put vars in another location so you only call the function here, but fine now.
		$hdr_gfont = get_option($shortname . '_google_hdr_font' );
		$hdr_dfont = get_option($shortname . '_default_hdr_font' );
		
		gfonts_api($hdr_gfont, $hdr_dfont);
	
	}
}

add_action('wp_head', 'optionsframework_wp_head');



/*-----------------------------------------------------------------------------------*/
/* Output CSS from standarized options */
/*-----------------------------------------------------------------------------------*/

// Load Custom CSS 
function of_head_css() {

		$shortname =  get_option('of_shortname'); 
		$output = '';
		
		$custom_css = get_option('of_custom_css');
		
		if ($custom_css <> '') {
			$output .= $custom_css . "\n";
		}
		
		// Output styles
		if ($output <> '') {
			$output = "<!-- Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
			echo $output;
		}	
}


//	Load Color Options
function of_options_output_css() { 
?>
	<style type="text/css">
		/* <![CDATA[ */
	<?php $of_css_options_output = dirname( __FILE__ ) . '/style-output.php'; if( is_file( $of_css_options_output ) ) require $of_css_options_output; ?>
	
		/* ]]> */
	</style>
<?php }


// Load WebFont, called in optionsframework_wp_head
function gfonts_api($gf1, $df1) {
	global $shortname;

	$addfont = <<<ADDFONTS

<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js'></script>
<script type='text/javascript'>WebFont.load({ google: {families: [ '$gf1' ]}})</script>
<style type='text/css'>.wf-inactive {font-family: Gill Sans;} #blog-title {font-family: '$gf1';}</style>

ADDFONTS;

	echo $addfont;
} 
//color: <?php echo $title_color.';'.'font-size:'.$title_size.'em' 	


/*-----------------------------------------------------------------------------------*/
/* Blog Category Option
/*-----------------------------------------------------------------------------------*/

// Get all posts from category specified in the Blog Category option. This is the only category displayed on the blog page. 

function get_blog_cat()	{
	
	$shortname = get_option('of_shortname');
	$cat_option = get_option($shortname.'_cats_in_blog');
	$cat = get_cat_ID($cat_option);
	query_posts( "cat=$cat" );
		
}
add_filter('thematic_above_indexloop', 'get_blog_cat');


/*-----------------------------------------------------------------------------------*/
/* Add Body Classes for Layout
/*-----------------------------------------------------------------------------------*/

// Adds a body class to indicate sidebar position - uncomment the filter and the option in theme-options.php to use it (you'll probably have to do some troubleshooting).

// add_filter('thematic_body_class','of_body_class');
 
function of_body_class($classes) {
	$shortname =  get_option('of_shortname');
	$layout = get_option($shortname .'_layout');
	
	if ($layout == '') {
		$layout = 'sidebar-r';
	}
	$classes[] = $layout;
	return $classes;
}


/*-----------------------------------------------------------------------------------*/
/* Add Favicon
/*-----------------------------------------------------------------------------------*/

function childtheme_favicon() {
		$shortname =  get_option('of_shortname'); 
		if (get_option($shortname . '_custom_favicon') != '') {
	        echo '<link rel="shortcut icon" href="'.  get_option('of_custom_favicon')  .'"/>'."\n";
	    }
		else { ?>
			<link rel="shortcut icon" href="<?php echo bloginfo('stylesheet_directory') ?>/admin/images/favicon.ico" />
<?php }
}

add_action('wp_head', 'childtheme_favicon');

/*-----------------------------------------------------------------------------------*/
/* Replace Blog Title With Logo
/*-----------------------------------------------------------------------------------*/

// If a logo is uploaded, unhook the page title and description

function add_childtheme_logo() {
	$shortname =  get_option('of_shortname');
	$logo = get_option($shortname . '_logo');
	if (!empty($logo)) {
		remove_action('thematic_header','thematic_blogtitle', 3);
		remove_action('thematic_header','thematic_blogdescription',5);
		add_action('thematic_header','childtheme_logo', 3);
	}
}
add_action('init','add_childtheme_logo');

// Displays the logo
function childtheme_logo() {
	$shortname =  get_option('of_shortname');
	$logo = get_option($shortname . '_logo');
    $heading_tag = ( is_home() || is_front_page() ) ? 'h1' : 'div';?>
    <<?php echo $heading_tag; ?> id="site-title">
	<a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('description'); ?>">
    <img src="<?php echo $logo; ?>" alt="<?php bloginfo('name'); ?>"/>
	</a>
    </<?php echo $heading_tag; ?>>
<?php }


/*-----------------------------------------------------------------------------------*/
/* Show analytics code in footer */
/*-----------------------------------------------------------------------------------*/

/*function childtheme_analytics(){
	$shortname =  get_option('of_shortname');
	$output = get_option($shortname . '_google_analytics');
	if ( $output <> "" ) 
		echo stripslashes($output) . "\n";
}
add_action('wp_footer','childtheme_analytics');*/



?>

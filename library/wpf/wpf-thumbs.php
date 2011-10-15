<?php


// http://net.tutsplus.com/tutorials/wordpress/creating-custom-fields-for-attachments-in-wordpress/

// Add custom fields to image uploader. To be displayed in attachments.
function wpf_fields_edit( $form_fields, $post ) {

	$post->post_type == 'attachment';

	$form_fields[ 'wpf_title' ] = array(
		'label' => __( 'Title' ),
		'input' => 'text',
		'value' => get_post_meta( $post->ID, '_wpf_title', true )
	);
	$form_fields[ 'wpf_title' ][ 'label' ] = __( 'WPF FIELD' );
	$form_fields[ 'wpf_title' ][ 'input' ] = 'text';
	$form_fields[ 'wpf_title' ][ 'value' ] = get_post_meta( $post->ID, '_wpf_title', true );


    $form_fields[ 'wpf_medium' ] = array(
        'label' => __( 'Medium' ),
        'input' => 'text',
        'value' => get_post_meta( $post->ID, '_wpf_medium', true )
    );
    $form_fields[ 'wpf_medium' ][ 'label' ] = __( 'Medium' );
    $form_fields[ 'wpf_medium' ][ 'input' ] = 'text';
    $form_fields[ 'wpf_medium' ][ 'value' ] = get_post_meta( $post->ID, '_wpf_medium', true );

    unset($form_fields['post_content']);
	return $form_fields;
}

add_filter( 'attachment_fields_to_edit', 'wpf_fields_edit', NULL, 2 );


function wpf_fields_save( $post, $attachment ) {
    $fields = array('wpf_title', 'wpf_medium');
    foreach( $fields as $field ) {
        $_field = '_' . $field;
        if( isset( $attachment[ $field ] ) ) {
            if( trim( $attachment[ $field ] ) == '' ) $post[ 'errors' ][ $field ][ 'errors' ][] = __( 'Error! Something went wrong.' );
            else update_post_meta( $post[ 'ID' ], $_field, $attachment[ $field ] );
        
        }    
    }
    
	return $post;

}

add_filter( 'attachment_fields_to_save', 'wpf_fields_save', NULL, 2 );

function get_artwork_fields_info() {
	global $post;
    $fields = array('wpf_title', 'wpf_medium');
   
    $title = $post->post_title;
    $medium = get_post_meta( $post->ID, '_wpf_medium', true );

    if( is_array($fields) ) {

        echo '<div id="artwork-meta"><em>' . $title . '</em><br/>';

        foreach ( $fields as $field ) {
            
            $_field = '_' . $field;
            $meta = get_post_meta( $post->ID, $_field, true );

            if ( $field != '' ) {
                echo $meta . '<br/>';
            } 
        }

         echo '</div>';

    }

    echo '<br><br><strong>AN ATTACHMENT</strong>';
    echo '<pre>';
    print_r($post);
    echo '</pre>';
}


////////////////
// THUMBNAILS //
////////////////


// Add support for post thumbnails of 250px square
// Add custom image size for cat thumbnails
if ( function_exists( 'add_theme_support' ) ) {
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 270, 270, true );
    add_image_size('wpf-thumb', 270, 270, true);
}

// http://www.kingrosales.com/how-to-display-your-posts-first-image-thumbnail-automatically-in-wordpress/ -- (although this link is now dead, and function has been significantly hacked, it's worth a credit.)

// Get post attachments
function wpf_get_attachments() {
	global $post;
	return get_posts( 
		array(
			'post_parent' => get_the_ID(), 
			'post_type' => 'attachment', 
			'post_mime_type' => 'image') 
		);
}


// Get the URL of the first attachment image - called in wpf-category.php. If no attachments, display default-thumb.png
function wpf_get_first_thumb_url() {

	$attr = array( 
		'class'	=> "attachment-post-thumbnail wp-post-image");

	$imgs = wpf_get_attachments();
	if ($imgs) {
		$keys = array_reverse($imgs);
		$num = $keys[0];
		$url = wp_get_attachment_image($num->ID, 'wpf-thumb', true,$attr);
		print $url;
	} else {
		echo '<img src=http://notlaura.com/default-thumb.png alt="no attachments here!" title="default thumb" class="attachment-post-thumbnail wp-post-image">';
	}
}

// END - get attachment function

// Make featured image thumbnail a permalink
add_filter( 'post_thumbnail_html', 'my_post_image_html', 10, 3 );
function my_post_image_html( $html, $post_id, $post_image_id ) {
	$html = '<a href="' . get_permalink( $post_id ) . '" title="' . esc_attr( get_post_field( 'post_title', $post_id ) ) . '">' . $html . '</a>';
	return $html;
}


// Filter the gallery shortcode defaults
// http://wordpress.stackexchange.com/questions/4343/how-to-customise-the-output-of-the-wp-image-gallery-shortcode-from-a-plugin

add_filter( 'post_gallery', 'my_post_gallery', 10, 2 );
function my_post_gallery( $output, $attr) {
    global $post, $wp_locale;

    static $instance = 0;
    $instance++;

    extract(shortcode_atts(array(
        'order'      => 'ASC',
        'orderby'    => 'menu_order ID',
        'id'         => $post->ID,
        'itemtag'    => 'dl',
        'icontag'    => 'dt',
        'captiontag' => 'dd',
        'columns'    => 4,
        'size'       => 'wpf-thumb', // Making the thumbnail size my custom one
        'include'    => '',
        'exclude'    => ''
    ), $attr));

    $id = intval($id);
    if ( 'RAND' == $order )
        $orderby = 'none';

    if ( !empty($include) ) {
        $include = preg_replace( '/[^0-9,]+/', '', $include );
        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

        $attachments = array();
        foreach ( $_attachments as $key => $val ) {
            $attachments[$val->ID] = $_attachments[$key];
        }
    } elseif ( !empty($exclude) ) {
        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    } else {
        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
    }

    if ( empty($attachments) )
        return '';

    if ( is_feed() ) {
        $output = "\n";
        foreach ( $attachments as $att_id => $attachment )
            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
        return $output;
    }

    $itemtag = tag_escape($itemtag);
    $captiontag = tag_escape($captiontag);
    $columns = intval($columns);
    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
    $float = is_rtl() ? 'right' : 'left';

    $selector = "gallery-{$instance}";

    $output = apply_filters('gallery_style', "
        <style type='text/css'>
            #{$selector} {
                margin: auto;
            }
            #{$selector} .gallery-item {
                float: {$float};
                margin-top: 10px;
                text-align: center;
                width: {$itemwidth}%;           }
            #{$selector} img {
                border: 2px solid #cfcfcf;
            }
            #{$selector} .gallery-caption {
                margin-left: 0;
            }
        </style>
        <!-- see gallery_shortcode() in wp-includes/media.php -->
        <div id='$selector' class='gallery galleryid-{$id}'>");

    $i = 0;
    foreach ( $attachments as $id => $attachment ) {
        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

        $output .= "<{$itemtag} class='gallery-item'>";
        $output .= "
            <{$icontag} class='gallery-icon'>
                $link
            </{$icontag}>";
        if ( $captiontag && trim($attachment->post_excerpt) ) {
            $output .= "
                <{$captiontag} class='gallery-caption'>
                " . wptexturize($attachment->post_excerpt) . "
                </{$captiontag}>";
        }
        $output .= "</{$itemtag}>";
        if ( $columns > 0 && ++$i % $columns == 0 )
            $output .= '<br style="clear: both" />';
    }

    $output .= "
            <br style='clear: both;' />
        </div>\n";

    return $output;
}

?>
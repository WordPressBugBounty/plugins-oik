<?php // (C) Copyright Bobbing Wide 2011-2017

/**
 * Implement [bw_qrcode] shortcode to display the QR code file with a link if required
 *
 * Notes: the attribute defaulting needs to be improved 
*/ 
function bw_qrcode( $atts ) {
  $link = bw_array_get( $atts, 'link', null );
  $company = bw_get_option( "company" );
  $text = bw_array_get( $atts, 'text', $company );
  $width = bw_array_get( $atts, 'width', null );
  $height = bw_array_get( $atts, 'height', null );


  $upload_dir = wp_upload_dir();
  $baseurl = $upload_dir['baseurl'];
  
  $logo_image = bw_get_option( "qrcode-image" );
  $image_url = $baseurl . $logo_image;

  /* translators: %s: company name */
  $image = retimage( NULL, $image_url, sprintf( __( 'QR code for %1$s', "oik" ), $text ) , $width, $height );
  if ( $link ) {
    BW_::alink( NULL, $link, $image, $company );
  }  
  else {
    e( $image );  
  }  
  return( bw_ret());
    
}

/**
 * Syntax for [bw_qrcode] shortcode
 */
function bw_qrcode__syntax( $shortcode="bw_qrcode" ) {
  $syntax = array( "link" => BW_::bw_skv( null, "<i>" . __( "URL", "oik" ) . "</i>", __( "Link URL for QR code image", "oik" ) )
                 , "text" => BW_::bw_skv( bw_get_option( "company" ), "<i>" . __( "string", "oik" ) . "</i>", __( "from oik options - company", "oik" ) )
                 , "width" => BW_::bw_skv( null, "<i>" . __( "width", "oik" ) . "</i>", __( "width of QR code image, if required", "oik" ) )
                 , "height" => BW_::bw_skv( null, "<i>" . __( "height", "oik" ) . "</i>", __( "height of QR code image, if required", "oik" ) )
                 );
  return( $syntax );
}

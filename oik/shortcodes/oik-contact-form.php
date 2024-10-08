<?php
/**
 * @copyright (C) Copyright Bobbing Wide 2013-2022
 * @package oik
 *
*/

/** 
 * Returns a unique contact form ID.
 *
 * This should allow more than one contact form on a page.
 *
 * @param bool $set - increment the ID if true
 * @return string - the contact form ID  - format oiku_contact-$bw_contact_form_id
 */
function bw_contact_form_id( $set=false ) {
	static $bw_contact_form_id = 0;
	if ( $set ) {
		$bw_contact_form_id++;
	}
	return( "oiku_contact-$bw_contact_form_id" );
}

/**
 * Implements the [bw_contact_form] shortcode
 *
 * Creates/processes an inline contact form for the user.
 *
 * @param array $atts - shortcode parameters
 * @param string $content - not yet expected 
 * @param string $tag - shortcode name
 * @return string Generated HTML for the contact form
 */
function bw_contact_form( $atts=null, $content=null, $tag=null ) {

	$email_to = bw_get_option_arr( "email", null, $atts );
	if ( $email_to ) { 
		$atts['email'] = $email_to;
        bw_display_contact_form($atts, null, $content);
	} else {
		/*
		* If no email address can be found, because it's not passed and not set in the oik-options,
        * then you'll get this message.
		*/
		e( __( "Cannot produce contact form for unknown user.", "oik" ) );
	}  
	return( bw_ret() );
}

/**
 * Create the submit button for the contact form 
 *
 * @param array $atts - containing "contact" or "me" or defaults
 */  
function bw_contact_form_submit_button( $atts ) {
	$text = bw_array_get( $atts, "contact", null );
	if ( !$text ) {
		$me = bw_get_me( $atts );
		/* translators: %s: name to contact */
		$text = sprintf( __( "Contact %s" ), $me ); 
	}
	e( isubmit( bw_contact_form_id(), $text, null ) );
}

/**
 * Show the "oik" contact form
 * 
 * This is a simple contact form which contains: Name, Email, Subject, Message and a submit button.
 * 
 * - Note: The * indicates Required field.
 * - If you want to make the fields responsive then try some CSS such as:
 *
 * `
 * textarea { max-width: 100%; }
 * `
 * 
 * @param array $atts - shortcode parameters 
 */
function _bw_show_contact_form_oik( $atts, $user=null, $content=null  ) {
	oik_require_lib( "bobbforms", "3.4.0" );
	$class = bw_array_get( $atts, "class", "bw_contact_form" );
	sdiv( $class );
	bw_form();
    if ( function_exists( 'bw_is_table')) {
        bw_table_or_grid_start( empty($content) ); // Start a grid if fields are defined
    } else {
        stag( 'table' );
    }
    if ( $content ) {
        _bw_show_contact_form_fields($atts, $content);
    } else {
        BW_::bw_textfield( bw_contact_field_full_name( "name" ), 30, __("Name *", "oik"), null, "textBox", "required");
        BW_::bw_emailfield( bw_contact_field_full_name( "email" ), 30, __("Email *", "oik"), null, "textBox", "required");
        BW_::bw_textfield( bw_contact_field_full_name( "subject" ), 30, __("Subject", "oik"), null, "textBox");
        BW_::bw_textarea( bw_contact_field_full_name( "message" ), 30, __("Message", "oik"), null, 10);
    }
	if ( function_exists( 'bw_is_table')) {
        bw_table_or_grid_end();
		bw_is_table( true );
    } else {
        etag( 'table');
    }

	e( wp_nonce_field( "_oik_contact_form", "_oik_contact_nonce", false, false ));
	oik_require_lib( "oik-honeypot" );
	do_action( "oik_add_honeypot" );
	bw_contact_form_submit_button( $atts );
	etag( "form" );
	ediv();
}

/**
 * Show/process a contact form using oik
 * 
 * @param array $atts
 * @param string $user
 * @param string $content
 */
function bw_display_contact_form( $atts, $user=null, $content=null ) {
    oik_require( 'shortcodes/oik-contact-field.php');
	$contact_form_id = bw_contact_form_id( true );
    bw_contact_form_register_fields( $atts, $content );
	$contact = bw_array_get( $_REQUEST, $contact_form_id, null );
	if ( $contact ) {
		oik_require_lib( "bobbforms" );
		oik_require_lib( "oik-honeypot" );
		do_action( "oik_check_honeypot", "Human check failed." );
		$contact = bw_verify_nonce( "_oik_contact_form", "_oik_contact_nonce" );
        //if ( $content ) {

        //}
		if ( $contact ) {
			$contact = _bw_process_contact_form_oik( $atts['email'] );
		}
	}
	if ( !$contact ) { 
		_bw_show_contact_form_oik( $atts, $user, $content );
	}
}

/**
 * Return the sanitized message subject
 *  
 * @return string - sanitized value of the message subject ( oiku_contact-n_subject )
 */ 
function bw_get_subject() {
    $field_name = bw_contact_field_full_name( 'subject');
	$subject = bw_array_get( $_REQUEST, $field_name, null );
	// $subject = stripslashes( $subject );
	$subject = sanitize_text_field( $subject );
	$subject = stripslashes( $subject );
	return( $subject );
}

/**
 * Return the sanitized message text
 * 
 * Don't allow HTML, remove any unwanted slashes and remove % signs to prevent variable substitution from taking place unexpectedly.
 * 
 * @return string - sanitized value of the message text field ( oiku_contact-n_message )
 */
function bw_get_message() {
    $field_name = bw_contact_field_full_name( 'message');
	$message = bw_array_get( $_REQUEST, $field_name, null );
	$message = sanitize_text_field( $message );
	$message = stripslashes( $message );
	$message = str_replace( "%", "", $message );
	return( $message );
}

/**
 * Returns the contact field value given the field name.
 *
 * @param $field_name
 * @return array|mixed|string|string[]|null
 */
function bw_get_contact_field_value( $field_name ) {
    $message = bw_array_get( $_REQUEST, $field_name, null );
    $message = sanitize_text_field( $message );
    $message = stripslashes( $message );
    $message = str_replace( "%", "", $message );
    return( $message );
}

/**
 * Gets the contact fields for the email.
 *
 * @return string
 */
function bw_get_contact_fields() {
    global $bw_contact_fields;
    $field_values = '';
    foreach ( $bw_contact_fields as $field ) {
        $field_value = bw_get_contact_field_value( $field );
        $field_values .= '<br />';
        $field_values .= bw_query_field_label( $field );
        $field_values .= ': ';
        $field_values .= $field_value;
    }
    return $field_values;
}

function bw_validate_contact_fields() {
    global $bw_contact_fields;
    $valid = true;
    foreach ( $bw_contact_fields as $field ) {
        $field_value = bw_get_contact_field_value( $field);
        $valid = bw_validate_required_field( $field, $field_value );
        if ( !$valid ) {
            $label = bw_query_field_label( $field );
            /* translators: %s Label of the required field. eg Text */
            $text = sprintf( __( "Required field not set: %s", 'oik' ), $label );
            bw_contact_issue_message( $field, "bw_field_required", $text);
            break;
        }
    }
    return $valid;
}

/**
 * Validates a required field to be non-empty.
 *
 * @param $field_name
 * @param $field_value
 * @return bool - false if required field isn't set.
 */
function bw_validate_required_field( $field_name, $field_value ) {
    $field_value = trim( $field_value );
    global $bw_fields;
    $valid = false;
    $field = bw_array_get( $bw_fields, $field_name, null );
    if ( null === $field ) {
        return $valid;
    }
    // A checkbox value of '0' means it's not checked.
    if ( 'checkbox' === $field['#field_type']) {
        $field_value = str_replace( '0', '', $field_value );
    }
    $required = bw_array_get( $field['#args'], 'required', null );
    $valid = ( 'y' === $required ) ? strlen( $field_value) > 0 : true;
    return $valid;
}

/**
 * Perform an Akismet check on the message, if it's activated
 * 
 * @param array - name value pairs of fields
 * @return bool - whether or not to send the email message
 */
function bw_akismet_check( $fields ) {
	if ( class_exists( "Akismet") || function_exists( 'akismet_http_post' ) ) {
		$query_string = bw_build_akismet_query_string( $fields );
		$send = bw_call_akismet( $query_string );
	} else {
		bw_trace2( "Akismet not loaded." );
		$send = bw_basic_spam_check( $fields );
	}
	return( $send );
}

/**
 * Performs a very basic spam check
 *
 * If there's any evidence of an attempt to include an URL then treat it as spam.
 * The code supports http or https and is case insensitive.
 *
 * Fields checked now include the author, email and content.
 *
 * @param array $fields
 * @return bool
 */
function bw_basic_spam_check( $fields ) {

	//bw_trace2();
	$fields_to_check = [ "comment_author", "comment_author_email", "comment_content", "subject" ];
	foreach ( $fields_to_check as $field ) {
		$content=bw_array_get( $fields, $field, '' );
		$content=strtolower( $content );
		if ( false !== strpos( $content, 'http' ) ) {
			bw_trace2( "Spam check found http" );
			return false;
		}
	}
	return true;
}

/**
 * Return true if the akismet call says the message is not spam
 * 
 * @param string $query_string - query string to pass to akismet
 * @return bool - true is the message is not spam 
 */
function bw_call_akismet( $query_string ) {
	global $akismet_api_host, $akismet_api_port;
	if ( class_exists( "Akismet" ) ) {
		$response = Akismet::http_post( $query_string, 'comment-check' );
	} else {
		$response = akismet_http_post( $query_string, $akismet_api_host, '/1.1/comment-check', $akismet_api_port  );
	}  
	bw_trace2( $response, "akismet response" );
	$result = false;
	$send = 'false' == trim( $response[1] ); // 'true' is spam, 'false' is not spam
	return( $send );
}

/**
 * Return the query_string to pass to Akismet given the fields in $fields and $_SERVER
 * 
 * @link https://akismet.com/development/api/#comment-check
 * blog (required) -The front page or home URL of the instance making the request. 
 *                  For a blog or wiki this would be the front page. Note: Must be a full URI, including http://.
 * user_ip (required) - IP address of the comment submitter.
 * user_agent (required) - User agent string of the web browser submitting the comment - typically the HTTP_USER_AGENT cgi variable. 
 *                          Not to be confused with the user agent of your Akismet library.
 * referrer (note spelling) - The content of the HTTP_REFERER header should be sent here.
 * permalink - The permanent location of the entry the comment was submitted to.
 * comment_type - May be blank, comment, trackback, pingback, or a made up value like "registration".
 * comment_author - Name submitted with the comment
 * Use akismet-guaranteed-spam to always get a spam response
 * comment_author_email - Email address submitted with the comment
 * Use akismet-guaranteed-spam@example.com
 * comment_author_url - URL submitted with comment
 * comment_content - The content that was submitted. 
 * Note: $fields['comment_content'] is the sanitized version of the user's input
 * 
 * @param array $fields array of fields 
 */
function bw_build_akismet_query_string( $fields ) {
	bw_trace2();
	//bw_backtrace();
	$form = $_SERVER;
	$form['blog'] = get_option( 'home' );
	$form['user_ip'] = preg_replace( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
	$form['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	$form['referrer'] = $_SERVER['HTTP_REFERER'];
	$form['permalink'] =  get_permalink();
	$form['comment_type'] = $fields['comment_type']; // 'oik-contact-form';
	$form['comment_author'] = bw_array_get( $fields, 'comment_author', null );
	$form['comment_author_email'] = bw_array_get( $fields, 'comment_author_email', null );
	$form['comment_author_url'] = bw_array_get( $fields, 'comment_author_url', null );
	$form['comment_content'] = bw_array_get( $fields, 'comment_content', null );  
	unset( $form['HTTP_COOKIE'] ); 
	$query_string = http_build_query( $form );
	return( $query_string );
}

/**
 * Display a "thank you" message
 * 
 * @param array $fields - in case we need them
 * @param bool $send - whether or not we were going to send the email / insert the post
 * @param bool $sent - whether or not the email was sent / post inserted
 */
function bw_thankyou_message( $fields, $send, $sent ) {
	if ( $send ) {
		if ( $sent ) {
			BW_::p( __( "Thank you for your submission.", "oik" ) );
		} else {
			BW_::p( __( "Thank you for your submission. Something went wrong. Please try again.", "oik" ) );
		}
	} else { 
		BW_::p( __( "We would like to thank you for your submission.", "oik" ) ); // spammer
	}
}

/**
 * Process a contact form submission
 *
 * Handle the contact form submission
 * 1. Check for required fields
 * 2. Perform spam checking
 * 3. Send email, copying user if required
 * 4. Display "thank you" message
 * 
 */
function _bw_process_contact_form_oik( $email_to ) {
	$message = bw_get_message();
    $valid = bw_validate_contact_fields();
	if ( $email_to && $valid ) {
		oik_require( "includes/oik-contact-form-email.php" );
		$fields = array();
		$subject = bw_get_subject();
		$fields['comment_content'] = $message;
		$fields['comment_author'] = bw_array_get( $_REQUEST, bw_contact_field_full_name( 'name'), null );
		$fields['comment_author_email'] = bw_array_get( $_REQUEST, bw_contact_field_full_name( 'email'), null );
		$fields['comment_author_url'] = null;
		$fields['comment_type'] = 'oik-contact-form';
		$fields['subject'] = $subject;

		$send = bw_akismet_check( $fields );
		if ( $send ) {
            // We only need the Message field once.
            $message = bw_get_contact_fields();
			$message .= "<br />\r\n";
			$message .= retlink( null, get_permalink() );
			$fields['message'] = $message;
			$fields['contact'] =  $fields['comment_author'];
			$fields['from'] = $fields['comment_author_email']; 
			$sent = bw_send_email( $email_to, $subject, $message, null, $fields );
		} else {
			$sent = true; // Pretend we sent it.
		}
		bw_thankyou_message( $fields, $send, $sent );
	} else {
		$sent = false;

		$text = __( "Invalid. Please correct and retry.", "oik" );
		bw_contact_issue_message( null, "bw_field_required", $text );
		$displayed = bw_display_messages();
		if ( !$displayed ) {
			p_( $text );
		}  
	}
	return( $sent );
}

function bw_contact_issue_message( $field, $code, $text, $type='error' ) {
    if ( !function_exists( "bw_issue_message" ) ) {
        oik_require( "includes/bw_messages.php" );
    }
    bw_issue_message( $field, $code, $text, $type );
}

/**
 * Implement help hook for bw_contact_form
 */
function bw_contact_form__help( $shortcode="bw_contact_form" ) {
	return( __( "Display a contact form for the specific user", "oik" ) );
}

/**
 * Syntax hook for [bw_contact_form] shortcode
 */
function bw_contact_form__syntax( $shortcode="bw_contact_form" ) {
	$syntax = array( "user" =>  BW_::bw_skv( bw_default_user(), "<i>" . __( "id", "oik" ) . "</i>|<i>" . __( "email", "oik" ) . "</i>|<i>" . __( "slug", "oik" ) . "</i>|<i>" . __( "login", "oik" ) . "</i>", __( "Value to identify the user", "oik" ) )  
								 , "contact" => BW_::bw_skv( null, "<i>" . __( "text", "oik" ) . "</i>", __( "Text for submit button", "oik" ) )
								 , "email" => BW_::bw_skv( null, "<i>" . __( "email", "oik" ) . "</i>", __( "Email address for submission", "oik" ) ) 
								 );
	$syntax += _sc_classes( false );
	return( $syntax );
}

/**
 * Implement example hook for [bw_contact_form] 
 *
 */
function bw_contact_form__example( $shortcode="bw_contact_form" ) {
	$id = bw_default_user( true ); 
	$example = "user=$id";
	/* translators: %s: User ID */
	$text = sprintf( __( 'Display a contact form for user: %1$s', "oik" ), $id );
	bw_invoke_shortcode( $shortcode, $example, $text );
}

/**
 * Implement snippet hook for [bw_contact_form]
 */
function bw_contact_form__snippet( $shortcode="bw_contact_form" ) {
	$contact = bw_array_get( $_REQUEST, "oiku_contact", null );
	if ( $contact ) {
		BW_::p( __( "Note: If the form is submitted from Shortcode help then two emails would be sent.", "oik" ) );
		BW_::p( __( "So the normal snippet code is not invoked in this case.", "oik" ) );
	} else {  
		//oik_require( "shortcodes/oik-user.php", "oik-user" );
		$id = bw_default_user( true ); 
		$example = "user=$id"; 
		_sc__snippet( $shortcode, $example );
	}
}

/**
 * Registers fields for the contact form.
 *
 * @param $atts
 * @param $content
 */
function bw_contact_form_register_fields( $atts, $content ) {
    global $bw_contact_fields;
    $bw_contact_fields = [];
    if ( !$content ) {
        $content = "[bw_contact_field 'Name *'][bw_contact_field 'Email *'][bw_contact_field 'Subject'][bw_contact_field 'Message']" ;
    }
    $content = do_shortcode( $content );
    //print_r( $bw_contact_fields );
    /*
    $message_field = bw_array_get( $bw_contact_fields, bw_contact_field_full_name('message'), null );
    if ( !$message_field ) {
        $content = do_shortcode( "[bw_contact_field 'Message']" );
    }
    */
}

/**
 * Displays the contact form's fields.
 *
 * @TODO Ensure required fields have the 'required' attribute.
 * @TODO Set default field lengths and textarea height.
 */
function _bw_show_contact_form_fields() {
    global $bw_contact_fields;
    global $bw_fields;
    //print_r( $bw_contact_fields );
    foreach ( $bw_contact_fields as $full_name ) {
        //echo $full_name;
        $field = bw_array_get( $bw_fields, $full_name, null );
        bw_trace2( $field, "Field", false, BW_TRACE_DEBUG );
        if ( $field ) {
            $value = bw_array_get( $_REQUEST, $full_name );
            bw_form_field( $full_name, $field['#field_type'], $field['#title'], $value , $bw_fields[ $full_name ]['#args']);
        }
    }
    $bw_contact_fields = [];
}

/**
 * Implements the oik/contact-form block
 *
 * Creates/processes an inline contact form for the user.
 *
 * @param array $atts - block parameters
 * @param string $content - Nested HTML
 * @param object WP_Block - Block object including InnerBlocks
 * @return string Generated HTML for the contact form
 */
function bw_contact_form_block( $atts=null, $content=null, $block=null ) {
    $email_to = bw_get_option_arr( "email", null, $atts );
    if ( $email_to ) {
        $atts['email'] = $email_to;
        $content = bw_contact_form_inner_blocks( $block->parsed_block['innerBlocks']);
        bw_display_contact_form($atts, null, $content);
    } else {
        /*
        * If no email address can be found, because it's not passed and not set in the oik-options,
        * then you'll get this message.
        */
        e( __( "Cannot produce contact form for unknown user.", "oik" ) );
    }
    return( bw_ret() );
}

/**
 * Handles oik/contact-field inner blocks.
 *
 * @param $innerBlocks
 * @return string
 */
function bw_contact_form_inner_blocks( $innerBlocks ) {
    $content = '';
    //bw_trace2();
    foreach ( $innerBlocks as $innerBlock ) {

        $content .= bw_contact_field_to_shortcode( $innerBlock['attrs'] );
    }
    return $content;
}

/**
 * Converts an oik/contact-field block's attributes to a bw_contact_field shortcode.
 *
 * @param $attrs
 * @return string
 */
function bw_contact_field_to_shortcode( $attrs ) {
    $content = '[bw_contact_field label="';
    $content .= bw_array_get( $attrs, 'label', "field" );
    $content .= '" type="';
    $content .= bw_array_get( $attrs, 'type', 'text' );
    $content .= '"';
    $required = bw_array_get( $attrs, 'required', false );
    if ( $required ) {
        $content .= ' required=y';
        $requiredIndicator = bw_array_get( $attrs, 'requiredIndicator', null );
        if ( null !== $requiredIndicator ) {
            $content .= ' requiredindicator="' . $requiredIndicator . '"';
        }
    }
    $class = bw_array_get( $attrs, "className", null );
    if ( $class ) {
        $content .= ' class="' . $class . '"';
    }
    $content .= "]";
    return $content;
}
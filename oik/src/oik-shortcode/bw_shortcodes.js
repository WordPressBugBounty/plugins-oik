/** 
 * Defines generic oik shortcodes
 *
 * The generic oik shortcode block allows the user to select the shortcode from a list of shortcodes.
 * After selection the set of parameters for the shortcode is defined.
 * And the appropriate fields created.
 * 
 * I stil have very little idea how this should work!
 * 
 * 
 * @copyright (C) Copyright Bobbing Wide 2018,2021
 * @author Herb Miller @bobbingwide
 
 */

import { __ } from '@wordpress/i18n';
 
const postTypeParmValues =  
{ post: "Post",
  page: "Page",
	attachment: "Attachment",
	custom: "Custom",
};

const bw_post_attrs = {};
const bw_page_attrs = {};

const bw_posts_attrs = 
{ 
	post_type: postTypeParmValues,
};

const bw_pages_attrs = 
{ 
	post_type: postTypeParmValues,
};

const bw_shortcodes_inline =
{
	bw_post: "bw_post",
	bw_posts: "bw_posts",
	bw_page: "bw_page",
	bw_pages: "bw_pages",
	oik: "Spells out the oik backronym",
};

//const bw_shortcode_description =

/**
 * Here I'm going to filter out all of the shortcodes that return JavaScript / jQuery that would not work properly when
 * Server Side Rendered or already have a block associated with them.
 *
 */

const bw_shortcodes = {

	//,"api":"Simple API link","apiref":"&nbsp;"
	//,"apis":"Link to API definitions"
	//,"artisteer":"Styled form of Artisteer"
	//,"audio":"Displays uploaded audio file as an audio player"
	//,"bbboing":"Obfuscate some text but leave it readable, apparently"
	//,"bing":"Styled form of bing - simplified"
	//,"blocks":"Create links to related blocks"
	//,"bob":"Styled form of bob - simplified"
	//,"bp":"Styled form of BuddyPress"
	//,"bw":"Expand to the logo for Bobbing Wide"
	//,"bw_abbr":"Format an abbreviation"
	//,"bw_accordion":"Display posts in an accordion"
	//,"bw_acronym":"Format an acronym"
	//,"bw_action":"bw_shortcode_event"
	//"bw_address":"Display the address"
	"bw_admin":__("Display the Admin contact name","oik")
	,"bw_alt_slogan":__("Alternative slogan","oik")
	//,"bw_api":__("Dynamic API syntax help"
	//,"bw_archive":__("Display category archives"
	//,"bw_attachments":__("List attachments with links"
	//,"bw_autop":"Dynamically re-enable\/disable automatic paragraph generation"
	//,"bw_background":"Use attached image as the background"
	//,"bw_block":"Format an Artisteer block"
	//,"bw_blockquote":"Format a blockquote"
	//,"bw_blog":"Select blog to process"
	//,"bw_blogs":"List blogs using bw_pages style display"
	//,"bw_bookmarks":"List bookmarks"
	,"bw_business":__("Display your Business name","oik")
	//,"bw_button":__("Show a link as a button","oik")
	//,"bw_cite":"Cite a blockquote"
	//,"bw_code":"Display the help and syntax for a shortcode"
	//,"bw_codes":"Display the currently available shortcodes"
	,"bw_company":__("Company name","oik")
	,"bw_contact":__("Primary contact name","oik")
	,"bw_contact_button":__("Contact form button","oik")
	//,"bw_contact_form":"Display a contact form for the specific user"
	,"bw_copyright":__("Format a Copyright statement","oik")
	//,"bw_count":"Count posts for the selected post type"
	//,"bw_countdown":"Countdown timer"
	// ,"bw_crumbs":"Display breadcrumbs"
	// ,"bw_css":"Add internal CSS styling"
	// ,"bw_csv":"Display CSV data in a table or list"
	// ,"bw_cycle":"Display pages using jQuery cycle"
	// ,"bw_dash":"Display a dash icon"
	,"bw_directions":__("Display a 'Google directions' button.","oik")
	,"bw_domain":__("Display the domain name","oik")
	//,"bw_eblock":"end a [bw_block]"
	// ,"bw_editcss":"Edit Custom CSS file button"
	,"bw_email":__("Email primary contact (formal)","oik")
	,"bw_emergency":__("Emergency telephone number","oik")
	//,"bw_facebook":"Facebook link"
	,"bw_fax":__("Fax number","oik")
	//,"bw_field":"Format custom fields without labels"
	// ,"bw_fields":"Format custom fields, with labels"
	// ,"bw_flickr":"Flickr link"
	// ,"bw_follow_me":"Display defined social media follow me links"
	,"bw_formal":__("Formal company name","oik")
	,"bw_geo":__("Latitude and Longitude","oik")
	//,"bw_geshi":"Generic Syntax Highlighting"
	// ,"bw_google":"Google+ link"
	// ,"bw_google-plus":"Google+ link"
	// ,"bw_google_plus":"Google+ link"
	// ,"bw_googleplus":"Google+ link"
	// ,"bw_graphviz":"Display a GraphViz diagram"
	// ,"bw_group":"Display summary of selected items"
	// ,"bw_iframe":"Embed a page in an iframe"
	// ,"bw_images":"Display attached images"
	// ,"bw_instagram":"Follow me on Instagram"
	// ,"bw_jq":"Perform a jQuery method"
	//,"bw_link":"Display a link to a post."
	//,"bw_linkedin":"Follow me on LinkedIn"
	// ,"bw_list":"Simple list of pages\/posts or custom post types"
	,"bw_login":__("Display the login form or protected content","oik")
	,"bw_loginout":__("Display the Login or Logout link","oik")
	,"bw_logo":__("Display the company logo","oik")
	,"bw_mailto":__("Mailto (inline)","oik")
	,"bw_mob":__("Mobile phone number (inline)","oik")
	,"bw_mobile":__("Mobile phone number (block)","oik")
	,"bw_more":__("Read more button to progressively reveal content","oik")
	//,"bw_mshot":"bw_shortcode_event"
	//,"bw_navi":"Simple paginated list"
	//,"bw_new":"Display a form to create a new post"
	//,"bw_option":"Display the value of an option field"
	// ,"bw_otd":"Display 'On this day' in history related content "
	// ,"bw_page":"Add page button"
	// ,"bw_pages":"Display page thumbnails and excerpts as links"
	,"bw_parent":__("Display a link back to the parent page","oik")
	,"bw_pdf":__("Display attached PDF files","oik")
	//,"bw_picasa":"Follow me on Picasa"
	// ,"bw_pinterest":"Follow me on Pinterest"
	// ,"bw_plug":"Show plugin information"
	// ,"bw_popup":"Display a popup after a timed delay"
	,"bw_portfolio":__("Display matched portfolio files","oik")
	//,"bw_post":"Add Post button"
	// ,"bw_posts":"Display posts"
	,"bw_power":__("Powered by WordPress","oik")
	//,"bw_qrcode":"Display an uploaded QR code image"
	,"bw_register":__("Display a link to the Registration form, if Registration is enabled","oik")
	//,"bw_related":"Display related content"
	//,"bw_rpt":"bw_shortcode_event"
	// ,"bw_rwd":"Dynamically generate oik responsive web design CSS classes"
	// ,"bw_search":"Display search form"
	// ,"bw_show_googlemap":"Show Google map [bw_show_googlemap]"
	,"bw_skype":__("Skype name","oik")
	,"bw_slogan":__("Primary slogan","oik")
	//,"bw_table":"Display custom post data in a tabular form"
	// ,"bw_tabs":"Display posts in tabbed blocks"
	,"bw_tel":__("Telephone number (inline)","oik")
	,"bw_telephone":__("Telephone number (block)","oik")
	//,"bw_terms":"Display taxonomy terms links"
	//,"bw_testimonials":"Display testimonials"
	// ,"bw_text":"bw_shortcode_event"
	// ,"bw_thumbs":"List pages as fluid thumbnail links"
	// ,"bw_tides":"Display times and tides for a UK location"
	// ,"bw_tree":"Simple tree of pages\/posts or custom post types"
	// ,"bw_twitter":"Follow me on Twitter"
	// ,"bw_user":"Display information about a user"
	// ,"bw_users":"Display information about site users"
	//,"bw_video":"Display the video specified (url=) or attached videos"
	,"bw_wpadmin":__("Site: link to wp-admin","oik")
	//,"bw_wtf":"WTF"
	// ,"bw_youtube":"Follow me on YouTube"
	// ,"bwtrace":"Trace facility form"
	// ,"caption":"Display the caption for an image. Standard WordPress shortcode"
	// ,"classes":"Link to class definitions"
	,"clear":__("Clear divs ","oik")
	//,"clone":"Display the clone tree\/form for a post"
	// ,"cloned":"Display clones of this content"
	// ,"codes":"Create links to related shortcodes"
	// ,"content":"bw_shortcode_event"
	// ,"contents":"bw_shortcode_event"
	// ,"cookies":"Display table of cookies, by category"
	// ,"div":"start a &lt;div&gt; tag"
	// ,"diy":"A sample Do It Yourself shortcode"
	// ,"download_cart":"Show the shopping cart"
	// ,"download_checkout":"Show the checkout form"
	// ,"download_discounts":"Show available discount codes"
	// ,"download_history":"Show user's download history"
	// ,"downloads":"Show downloads list \/ grid "
	// ,"drupal":"bw_shortcode_event"
	// ,"edd_downloads":"edd_downloads_query"
	// ,"edd_login":"Display the login form, if not already logged in"
	// ,"edd_price":"Display the download's price"
	// ,"edd_profile_editor":"User profile editor"
	// ,"edd_receipt":"Display purchase receipt"
	// ,"edd_register":"Display the registration form, if not already logged in."
	// ,"ediv":"end a &lt;div&gt; with &lt;\/div&gt;"
	// ,"embed":"Embed media"
	// ,"etag":"End a tag started with [stag]"
	// ,"file":"Display reference for a file"
	// ,"files":"Link to files definitions"
	// ,"footer_childtheme_link":"Display link to child theme, if defined"
	// ,"footer_copyright":"Display copyright notice"
	// ,"footer_genesis_link":"Display link to the Genesis Framework."
	// ,"footer_home_link":"genesis_footer_home_link_shortcode"
	// ,"footer_loginout":"Display link to WordPress."
	// ,"footer_site_title":"genesis_footer_site_title_shortcode"
	// ,"footer_studiopress_link":"Display link to StudioPress."
	// ,"footer_wordpress_link":"Display link to WordPress."
	// ,"gallery":"Display the attached images in a gallery"
	// ,"getnivo":"&nbsp;","getoik":"&nbsp;"
	// ,"github":"Link to GitHub"
	// ,"gpslides":"Display a Slideshow Gallery Pro slideshow"
	// ,"guts":"bw_shortcode_event"
	// ,"hook":"bw_shortcode_event"
	// ,"hooks":"Link to hook definitions"
	// ,"lartisteer":"Link to Artisteer "
	// ,"lazy":"Link to definition of lazy"
	// ,"lbp":"Link to BuddyPress"
	// ,"lbw":"Link to Bobbing Wide sites"
	// ,"ldrupal":"Link to drupal.org"
	// ,"loik":"Link to [oik]-plugins"
	// ,"loikeu":"Link to oik-plugins.eu"
	// ,"loikp":"Link to oik-plugins.com"
	// ,"loikuk":"&nbsp;","lssc":"&nbsp;"
	// ,"lwp":"Link to WordPress.org"
	// ,"lwpms":"Link to WordPress Multi Site"
	// ,"md":"Format Markdown"
	// ,"ngslideshow":"NextGen gallery slideshow"
	// ,"nivo":"Display the nivo slideshow for attachments or other post types."
	// ,"oik":"Expand to the logo for oik"
	// ,"oik_edd_apikey":"get API key form for EDD"
	// ,"oikp_download":"Produce a download button for a plugin"
	// ,"oikth_download":"Produce a download button for a theme"
	// ,"parsed_source":"bw_shortcode_event"
	,"paypal":__("Paypal shortcodes","oik")
	//,"playlist":"Playlist"
	// ,"post_author":"Display post author name"
	// ,"post_author_link":"Display post author link"
	// ,"post_author_posts_link":"Display link to author's posts"
	// ,"post_categories":"Display category links list"
	// ,"post_comments":"Display link to post comments"
	// ,"post_date":"Display post publication date"
	// ,"post_edit":"Display edit post link"
	// ,"post_modified_date":"Display post last modified date"
	// ,"post_modified_time":"Display post last modified time"
	// ,"post_tags":"Display tag links list"
	// ,"post_terms":"Display linked post taxonomy terms list"
	// ,"post_time":"Display post publication time"
	// ,"purchase_collection":"Display a purchase collection link"
	// ,"purchase_history":"Show user's purchase history"
	// ,"purchase_link":"Display purchase button"
	// ,"s05":"Star rating 0 of 5"
	// ,"s15":"Star rating 1 of 5"
	// ,"s25":"Star rating 2 of 5"
	// ,"s35":"Star rating 3 of 5"
	// ,"s45":"Star rating 4 of 5"
	// ,"s55":"Star rating 5 of 5 "
	// ,"sdiv":"Start a div"
	// ,"sediv":"Start and end a div"
	// ,"smart":"Link to definition of smart"
	// ,"stag":"Start a tag"
	// ,"video":"Embed video files"
	// ,"wp":"Display a styled form of WordPress. "
	// ,"wp_caption":"Display the caption for an image. Standard WordPress shortcode",
	//,"wpms":"Styled form of WordPress Multi Site"
 	,"OIK":__("Spells out the oik backronym","oik")
};



let bw_shortcodes_attrs = 
{ 
	bw_post: bw_post_attrs, 
	bw_posts: bw_posts_attrs,
	bw_page: bw_page_attrs,
	bw_pages: bw_pages_attrs,
};


import { get, has } from 'lodash';

	
/**
 * Returns shortcode's attributes	
 *
 * @param string shortcode name e.g. bw_posts
 * @return array attributes for the shortcode
 */
function getAttributes( shortcode ) {
	console.log( shortcode );
	var attributes = null;
	if ( has( bw_shortcodes_attrs, shortcode ) ) {
		attributes = get( bw_shortcodes_attrs, shortcode );
		console.log( attributes );
	} else {
		console.log( "Not set" );
	}
	return attributes;
}

function getShortcodes( ) {
	return
}

export { bw_shortcodes, bw_shortcodes_attrs, getAttributes };




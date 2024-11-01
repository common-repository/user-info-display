<?php
/*
Plugin Name: User Info Display
Plugin URI: http://wpgiraffe.com/2011/08/24/plugin-userinfodisplay/ 
Description: Creates a shortcode to display some user information in a post, page or widget
Version: 1.0.0
Author: Jason Mayoff
Author URI: http://wpgiraffe.com
License: GPL2
*/

/*  Copyright 2011  Jason Mayoff (email : jmayoff+plugins@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/





?>
<?php


/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
 * @param boole $img True to return a complete IMG tag False for just the URL
 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
 * @return String containing either just a URL or a complete image tag
 * @source http://gravatar.com/site/implement/images/php/
 */
function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {

	
	$url = 'http://www.gravatar.com/avatar/';
	$url .= md5( strtolower( trim( $email ) ) );
	$url .= "?s=$s&d=$d&r=$r";
	if ( $img ) {
		$url = '<img src="' . $url . '"';
		foreach ( $atts as $key => $val )
			$url .= ' ' . $key . '="' . $val . '"';
		$url .= ' />';
	}
	return $url;
}



// Function uinfo_display()
// Description: Allows the use of plugin as a template tag. Accepts arguments and turns them into $atts
// 				and then calls the jm_USER_info() plugin 
// Parameters: 	id 		= the user's id number. Default is id of author of current post/page.
// 				name 	= Yes or no. Do we display the name 
// 				email 	= Yes or no. Do we display the email (link on username)  
// 				url 	= Yes or no. Do we display the user's url   
// Returns: Block of HTML containing user info requested 

function uinfo_display($id, $align, $name, $email, $url, $all){
	
	$atts = array("id" => $id, "align" => $align ,"name" => $name, "email" => $email, "url" => $url, "all" => $all);
	
	
	
	
	return jm_user_info($atts);
	
}



// Function: jm_user_info
// Description: Returns user info contained in the user's profile
// Parameters: 	id 		= the user's id number. Default is id of author of current post/page.
// 				name 	= Yes or no. Do we display the name 
// 				email 	= Yes or no. Do we display the email (link on username)  
// 				url 	= Yes or no. Do we display the user's url   
// Returns: Block of HTML containing user info requested 

function jm_user_info($atts) { 	

	extract( shortcode_atts( array(
			'id' => get_the_author_meta(ID),
			'align' => 'left',
			'name' => 'yes',
			'email' => 'yes',
			'url' => 'yes',
			'all' => 'no'
		), $atts) );

$author_id=$id;
$username = get_the_author_meta('login', $author_id);	
	
		
		// If the user exists, then we return the info requested, 
		// else we return an error message
		
		if(username_exists($username)){
			$author_desc = get_the_author_meta('description',$author_id);
			$author_email = get_the_author_meta('user_email',$author_id);
			$author_fname = get_the_author_meta('first_name', $author_id);
			$author_lname = get_the_author_meta('last_name', $author_id);
			$site_link = get_the_author_meta('user_url', $author_id);
			
			
			// May implement these later
			
			/*
			$facebook_link = get_the_author_meta('aim',$author_id);
			$twitter_link = get_the_author_meta('yim',$author_id);
			$author_img_link = get_the_author_meta('jabber',$author_id);
			*/
			
			// Do we align left or right? 
			$class = "alignleft";
			if ($align != "left"){
				$class = "alignright";
			}
			
			// Creates the url to the gravatar
			$image_url = get_gravatar($author_email, 80, 'mm', 'g', false);
			$image = '<img src="' . $image_url . '" class="'.$class.'">';
			
			// Build the HTML
			$return_text = '<div id="author-info">';
			$return_text .= '<div id="author_photo">' . $image . '</div>'; 
			
			
			// User's name linked with email address
			if ($name !='no' && $email != 'no') { $return_text .= '<h2><a href="mailto:'. $author_email . '">' . $author_fname . ' ' . $author_lname . '</a></h2>'; }
			
			// User's name NOT linked with email address
			if ($name !='no' && $email == 'no') { $return_text .= '<h2>' . $author_fname . ' ' . $author_lname . '</h2>'; }
			
			
			$return_text .= $author_desc;
			
			if ($url != 'no' && trim($site_link) != "" ){$return_text .= '<p><a href="' . $site_link . '">' . $site_link . '</a></p>' ;}
			 
			$return_text .= '</div>';
		}else{
			$return_text = "No such user";	
		}
		
		
		
		
// Return the HTML
return $return_text; 


}

add_shortcode('userinfo', 'jm_user_info');

?>
<?php
// kludge from Ryan Galgon (September 2012) of both wp-tumblr.php and tumblrOauth example (https://groups.google.com/d/msg/tumblr-api/g6SeIBWvsnE/gnWqT9jFSlEJ)
// wp-tumblr.php
// originally by miguel santirso, http://miguelsantirso.es
// updated for wp 1.1 xml export format by christopher j. pilkington, http://0x1.net, 2011 oct 27
//
// Use at your own risk, you may want to test this with a throw-away tumblr account first.
//

// Start a session, load the library
session_start();
require_once('tumblroauth/tumblroauth.php');

// Define the needed keys
$consumer_key = "PUBLIC KEY FROM TUMBLR HERE";
$consumer_secret = "SECRET KEY FROM TUMBLR HERE";

// The full path to the XML file you exported from Wordpress
$xmlFile         = 'PATH TO YOUR XML POSTS FILE';

//name of your tumblr blog that you're importing to
//example: yourName.tumblr.com
$tumblrName = 'yourName.tumblr.com';

// Once the user approves your app at Tumblr, they are sent back to this script.
// This script is passed two parameters in the URL, oauth_token (our Request Token)
// and oauth_verifier (Key that we need to get Access Token).
// We'll also need out Request Token Secret, which we stored in a session.

// Create instance of TumblrOAuth.
// It'll need our Consumer Key and Secret as well as our Request Token and Secret
$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $_SESSION['request_token'], $_SESSION['request_token_secret']);

// Ok, let's get an Access Token. We'll need to pass along our oauth_verifier which was given to us in the URL. 
$access_token = $tum_oauth->getAccessToken($_REQUEST['oauth_verifier']);

// We're done with the Request Token and Secret so let's remove those.
unset($_SESSION['request_token']);
unset($_SESSION['request_token_secret']);

// Make sure nothing went wrong.
if (200 == $tum_oauth->http_code) {
  // good to go
} else {
  die('Unable to authenticate');
}

// What's next?  Now that we have an Access Token and Secret, we can make an API call.

// Any API call that requires OAuth authentiation will need the info we have now - (Consumer Key,
// Consumer Secret, Access Token, and Access Token secret).

// You should store the Access Token and Secret in a database, or if you must, a Cookie in the user's browser.
// Never expose your Consumer Secret.  It should stay on your server, avoid storing it in code viewable to the user.

// I'll make the /user/info API call to get some baisc information about the user

// Start a new instance of TumblrOAuth, overwriting the old one.
// This time it will need our Access Token and Secret instead of our Request Token and Secret
$tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

// If WordPress post is a draft, true to upload as private, false to not upload it.
$publishDraftAsPrivate = true;

// A writeable logfile, to keep track of the new URLs.
$logFile         = 'log.txt';
 
if (file_exists($xmlFile)) {
    $xml = simplexml_load_file($xmlFile);
} else {
    echo "ERROR: no such file\n\n";
    die();
}

if (isset($xml)) {
 
    $nodes = $xml->xpath('/rss/channel/item');
 
    $count = 0;
 
    while(list( , $node) = each($nodes)) {
 
        $post_type =  'regular';
        $post_title = $node->title;
        $post_title = str_replace("%20"," ",$post_title);
        $content =    $node->children("http://purl.org/rss/1.0/modules/content/");            
        $post_body = (string)$content->encoded;    
        $post_body = str_replace(""," ",$post_body);
        $wp =        $node->children("http://wordpress.org/export/1.2/");
        $date =      $node->pubDate;
        echo $post_title . $date . "<br \>";
        $private = 0;
        
        if ($wp->status != "publish" && $wp->status != "inherit") {
 
            if (!$publishDraftAsPrivate) {
                continue;
            }
 
            $private = 1;
        }
        
       if ($wp->post_type == "attachment")
            continue;
 
        $count++;
                
		$postContent = array(
			'type' 	=> 'text',
			'title' => $post_title,
			'body' 	=> $post_body,
			'date' 	=> $date,
		);

        $postUrl = 'http://api.tumblr.com/v2/blog/' . $tumblrName . '/post';
		
		$goPost = $tum_oauth->post($postUrl, $postContent);
	
		//check for error
		if(201 == $tum_oauth->http_code) {
		   // good to go
		 } else { 
			die('unable to post');
		 }	
	
	} //end of while we have posts to see
}

?>

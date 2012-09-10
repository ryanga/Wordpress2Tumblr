Wordpress2Tumblr
================

#Migrate all posts from WordPress to Tumblr

_Known issue:_
This does not preserve the original date of the post. 
It looks to be _just_ a matter of matching up the WP date format to the Tumblr format, but date preservation wasn't something I needed. The posts will retain the correct order however. 

###How to run
Prereq: I'm assuming you can run php scripts from your local machine. Here's a [helpful guide I found for enabling it on OS X](http://foundationphp.com/tutorials/php_leopard.php).

First up, get the code from the GitHub project page [Wordpress2Tumblr](https://github.com/ryanga/Wordpress2Tumblr) and place it in the root of your webserver (if using the bundled OS X one that would be _/Library/WebServer/Documents_)

Next, get a backup of all the posts in your WordPress blog. Details for exporting found [here](http://en.support.wordpress.com/export/). In the options, be sure to select "Posts" rather than "All content". 
This will give you an xml file. Copy it over to the root of your webserver.

Now its time to register the application on Tumblr. This gets us the keys needed for authenticating. )
Goto [http://www.tumblr.com/oauth/apps](http://www.tumblr.com/oauth/apps) and click on "Register Application"
Give the app whatever name you like, but for the callback URL set it to _http://127.0.0.1/callback.php_
Copy down the Consumer and Secret key to a safe place. 

In the connect.php file change both the Consumer and Secret key to the values you just got from Tumblr. 

There are 4 things you need to change in the callback.php file:
* Secret Key
* Consumer Key
* Path to the XML export of your WordPress posts
* Tumblr name 

That's it, now we're ready to go.

Visit [http://127.0.0.1/connect.php](http://127.0.0.1/connect.php). This will direct you to Tumblr asking to authorize the application. Click Yes, and you're redirected back to the callback.php file (remember setting up the callback url?) and it will spin for a bit as it reads through your posts and publishes them to Tumblr. 

If that finishes successfully (you'll get a list of post titles) you can head over to Tumblr and should now see all the imported posts. 

###History
This is a kludge, joining these 2 bits of code and some minor updates:
* [Tumblr Oauth example from lucasec](https://groups.google.com/d/msg/tumblr-api/g6SeIBWvsnE/gnWqT9jFSlEJ)
* [wp-tumblr.php script](http://sourcecookbook.com/es/recipes/73/how-to-export-wordpress-posts-to-tumblr) 
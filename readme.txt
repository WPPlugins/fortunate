=== Fortunate Random Quotes ===
Contributors: macgirvin
Tags: fortune, random, quote, quotation, plugin, widget
Requires at least: 2.7
Tested up to: 2.8.4
Stable tag: 1.4.4

Fortunate provides a random quote or quotation (also known as a 'fortune cookie') for your blog. 

== Description ==

Fortunate provides a hassle free random quote on your blog. There are no databases to download or configure and you do not need to come up with your own witty sayings. It provides instant access to one of the largest collections of random quotes, witticisms, sayings, jokes and trivia on the net and displays one of them on your page. You may select where to display it. Currently supported are

* Header - after or in place of the blog description
* Footer 
* At the end of the page content (e.g. at the end of The Loop)
* As a sidebar widget

When configured as a widget, the title can also be set. 

If these options are insufficient for your needs, the functions may also be manually invoked directly from your theme pages allowing total flexibility. [See Advanced Usage in the FAQ section].  

A configuration page also allows for the selection of different random quotes based on criteria such as 

* language - English, Spanish, Italian, French, German, and Russian are currently supported
* maximum length in bytes
* maximum number of text lines
* Those containing a specific word
* Those matching a regular expression (advanced usage)
* safe (default) or 'offensive' which may include sexual and off-colour content (advanced usage)
* Those coming from a particular quotation database (advanced usage)

Fortunate is designed to silently fail in the event of network delays or congestion which prevent a quotation being promptly returned. Configurable timeouts are provided to display your blog pages in a reasonable amount of time (without Fortunate) even if the network is totally whacked. You get to choose what is 'reasonable'. 

Fortunate may work on WordPress releases prior to 2.7 but this has not been tested.

== Changelog ==

= 1.4.4 =
* Updated compatibility to WP2.8.4.

= 1.4.3 =
* Updated compatibility to WP2.8.3 - no code changes, documentation updated to new changelog format.

= 1.4.2 =
* Updated compatibility to WP2.8.2 - no code changes

= 1.4.1 =
* Updated compatibility to WP2.8.1 - no code changes

= 1.4 =
* Introduced advanced usage. This provides access to several settings which should normally be left untouched, except by the adventurous and/or knowledgeable. This includes the normal/offensive 'rating', Choice of a specific quotation database, and regular expression matching. Selecting 'Advanced mode' also provides statistics (viewable at the bottom of the settings page) on the total number of quotes available which match the current search settings, and a list of applicable databases from which they are derived. [If you are upgrading from an earlier release and have made use of the normal/offensive rating or regular expression features, please go to your options page and enable 'advanced mode' so that these functions will still work for you.]

= 1.3.11 =
* documentation cleanup

= 1.3.10 =
* update compatibility to WP2.8

= 1.3.9 =
* Added Regular Expression matching.

= 1.3.8 =
* readme.txt file was badly formatted in the FAQ example. No code changes.

= 1.3.7 =
* Added ability to call functions directly from page templates, with different arguments than the default. This allows to you have more than one quote on a page and provide complete control over theming and options for each invocation.

= 1.3.6 =
* avoid over-writing any customised CSS edits in the 'fortunate.css' file during updates. The zip archive will now contain a 'fortunate-template.css' file and 'fortunate.css' will need to be created by hand if you wish to use the 'include CSS' feature. Update compatibility to WP-2.7.1 and add screenshot of option page. 

= 1.3.5 =
* Added category randomisation, which provides equal weights to all quotation source collections irrespective of the number of quotes in the collection. This will give equal weight to quotations from George Bush, Luke Skywalker, or the Hitchhikers Guide to the Galaxy, (if using English) and reduce the number of Zippy the Pinhead and Knightbird quotes. The latter have a disproportionate number of quotations relative to other data sources. There is a slight performance penalty so this option is disabled by default.

= 1.3.4 =
* Fix widget title which was appearing after the quote.

= 1.3.3 =
* Add specific word search so that one may provide only quotations containing (for instance) the word 'boat'. Added configurable timeouts to network settings for graceful failure in the event of network congestion or server downtime.

= 1.3.2 =
* Further internationalisation of plugin code

= 1.3.1 =
* Minor tweak to URL format

= 1.3 =
* Add Italian

= 1.2 =
* Add Spanish, French, German, and Russian quotes and internationalisation of plugin code

= 1.1 =
* Cleanup of configuration page

= 1.0 =
* Initial release


== Installation ==

1. Use the WordPress plugin installer (recommended) or
1. Unzip the fortunate zip archive from within your `wp-content/plugins` directory
or unzip on your desktop and upload the resultant `fortunate` folder and contents to your `wp-content/plugins` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the Fortunate configuration page from the 'Settings' menu and make any desired changes.
1. If you check the option box to include the plugin CSS file, copy the supplied 'fortunate-template.css' file to 'fortunate.css' (in the 'wp-content/plugins/fortunate' directory) and make any desired changes. This ensures that any custom changes will not be lost when the plugin is updated. 
  
== Frequently Asked Questions ==

= Where should I display the quotations?  =

This is entirely up to you, however some page locations may interact in a negative way with your theme. The safest place to display is in a sidebar widget and should work with any widget enabled theme.

The default location is at the end of the main loop, which should place the fortune at the bottom of the content region in your pages. 

If you use the header, the fortune will display beneath your blog description, assuming your theme provides the blog description. When using the header location, you should probably choose 1 or 2 as 'Maximum number of text lines' in the options to avoid messing up your banner with long quotations.

You may also use the page footer, but be aware that many themes place the footer content outside the themed region.

In some cases you may need to change CSS definitions to fit the results into your chosen theme. Copy the supplied 'fortunate-template.css' file to 'fortunate.css' and edit to your liking. Then visit the options page and tick the box to include the CSS file. 

= Why don't I see any quotations?  =

It is quite possible to provide search parameters which do not match any quotations at all. Fortunate does not provide any error messages when there are no matching results. Check your settings to see if you may be too specific through use of a search term or quotation size. Fortunate works best without any specific search limitations. Also be aware that the adult/offensive ratings are currently only valid for English, Spanish, and Italian. For all other languages please use 'normal (no adult/offensive content)'. 

It is also possible to set the timeout settings too low to respond in a reasonable time. The default is 5 seconds, which should work for any but the most remote regions of the planet. 

= Advanced usage =

You may call the fortunate_fetch() function directly from your page templates to provide more than one quote on a page and with complete control of options and theming. 

Example:

Code:

	<?php 
	      echo '<div class="myownfortunate">' 
	      . fortunate_fetch(array('lang' => 'fr', 'numlines' => 2)) 
	      . '</div>' ;
	?> 

This will produce a French quote of two lines (max) and allow you to theme
it using CSS as a different class than the standard quotation theme (which would correspond to class="fortunate").

Code:

	<?php 
	      echo '<div class="fortunate">' 
	      . fortunate_fetch(array('db' => 'bush')) 
	      . '</div>' ;
	?> 

This will provide a quote from George Bush. Please note that this database is only valid for the English language (which is the default).

Code:

	<?php 
	      echo '<div class="fortunate">' 
	      . fortunate_fetch(array('lang' => 'en',
                                      'type' => 'o', 
                                      'db' => 'sex')) 
	      . '</div>' ;
	?> 

This will provide an offensive quote from the English 'sex' database.


The options array is entirely optional, and will use standard defaults for any setting not supplied.

It may consist of any of the following:

* System settings:

* 'root'         => string - location of WPINC directory
* 'home'         => string - Referral URL [your website URL]
* 'read_timeout' => integer - number of seconds, default 5
* 'conn_timeout' => float - number of seconds, default 5


* Quotation settings:

* 'lang'         => 'en','es','it','fr','de','ru'  - default is 'en'
* 'type'         => '' (Normal - default), 'o' (Offensive), 'a' (Any)
* 'length'       => integer - maximum length of quotation - default unlimited (0)
* 'numlines'     => integer - maximum number of lines - default unlimited (0)
* 'pattern'      => string - return quotations containing this word - default ''
* 'regex'        => string - return quotations matching this (MySQL) regular expression - default ''
* 'equal'        => integer - provide equal weight to all data sources. 0 or 1, default 0
* 'db'           => string - name of a specific database to pull quotes from - default '' 
 
== Screenshots ==

1. Screenshot of Fortunate option setting page.


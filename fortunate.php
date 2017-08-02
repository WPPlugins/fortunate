<?php
/*
Plugin Name: fortunate
Plugin URI: http://wordpress.org/extend/plugins/fortunate
Description: Displays random quotation (fortune cookie) on page
Author: Mike Macgirvin
Version: 1.4.4
Author URI: http://macgirvin.com
*/

/*
Copyright 2008-2009 Mike Macgirvin. All rights reserved.

Redistribution and use in source and binary forms, with or without 
modification, are permitted provided that the following conditions are met:

   1. Redistributions of source code must retain the above copyright notice, 
this list of conditions and the following disclaimer.
   2. Redistributions in binary form must reproduce the above copyright 
notice, this list of conditions and the following disclaimer in the 
documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY Mike Macgirvin ``AS IS'' AND ANY EXPRESS OR 
IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF 
MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. 
IN NO EVENT SHALL Mick Dakota OR ANY PROJECT CONTRIBUTORS BE LIABLE FOR ANY 
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES 
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; 
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND 
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT 
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS 
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

$fortunate_textdomain = 'fortunate';

  add_option("fortunate_lang"     , 'en'       , '' , 'yes');
  add_option("fortunate_type"     , ''         , '' , 'yes');
  add_option("fortunate_length"   , '0'        , '' , 'yes');
  add_option("fortunate_numlines" , '0'        , '' , 'yes');
  add_option("fortunate_location" , 'loop_end' , '' , 'yes');
  add_option("fortunate_title"    , 'Random Quote'  , '' , 'yes');
  add_option("fortunate_addcss"   , '0'        , '' , 'yes');
  add_option("fortunate_pattern"  , ''         , '' , 'yes');
  add_option("fortunate_regex"    , ''         , '' , 'yes');
  add_option("fortunate_db"       , ''         , '' , 'yes');
  add_option("fortunate_equal"    , '0'        , '' , 'yes');
  add_option("fortunate_read_timeout"   , '5'        , '' , 'yes');
  add_option("fortunate_conn_timeout"   , '5'        , '' , 'yes');
  add_option("fortunate_advanced" , '0'        , '' , 'yes');
  
  if(get_option('fortunate_addcss') == "1")   
    add_action('wp_head','fortunate_css');

  add_action('admin_menu', 'fortunate_plugin_menu');

  $fortunate_location = get_option('fortunate_location');
  if($fortunate_location == 'widget')
    add_action('plugins_loaded', 'fortunate_widget_init');
  elseif($fortunate_location == 'bloginfo')
    add_filter('bloginfo','fortunate_filter',10,2);
  else
    add_action($fortunate_location,'fortunate_stdout',1);


function fortunate_widget_init() {
  register_sidebar_widget('Fortunate', 'fortunate_widget');
}


function fortunate_plugin_menu() {
  add_options_page('Fortunate Plugin Options', 'Fortunate', 
    8, __FILE__, 'fortunate_plugin_options');
}

function fortunate_css() {
  echo '<link type="text/css" rel="stylesheet" href="' 
    . get_bloginfo('wpurl') 
    . '/wp-content/plugins/fortunate/fortunate.css" />' 
    . "\n";
}


function fortunate_widget($args) {
  extract($args);

  echo $before_widget
       . $before_title . get_option('fortunate_title') . $after_title
       . fortunate() . $after_widget;
}

function fortunate_filter($info,$req) {
  if($req == 'description')
    return $info . fortunate();
  return $info;
}

function fortunate_stdout() {
  echo fortunate();
}

function fortunate($root = WPINC, $stats = 0 ) {
  static $fortunate_done = false;
  if($fortunate_done)
    return;
  $advanced = ((get_option('fortunate_advanced') == '1' ) ? 1 : 0);
  $showstats = (($stats && $advanced) ? 1 : 0);
  $database = get_option('fortunate_db');
  $db = (($advanced && strlen($database)) ? $database : '');
  $type = get_option('fortunate_type');
  $typeopt = (($advanced && strlen($type)) ? $type : '');
  $expr = get_option('fortunate_regex');
  $regex = (($advanced && strlen('expr')) ? $expr : '');

  $options = array( 'root'         => $root,
                    'home'         => get_option('home'),
                    'read_timeout' => get_option('fortunate_read_timeout'),
                    'conn_timeout' => get_option('fortunate_conn_timeout'),
                    'lang'         => get_option('fortunate_lang'),
                    'length'       => get_option('fortunate_length'),
                    'numlines'     => get_option('fortunate_numlines'),
                    'pattern'      => get_option('fortunate_pattern'),
                    'equal'        => get_option('fortunate_equal'),
                    'regex'        => $regex,
                    'db'           => $db,
                    'type'         => $typeopt,
                    'stats'        => $showstats
                    );

  $result = '<div class="fortunate">' 
          . fortunate_fetch($options)
          . '</div>' . "\n" ;
  $fortunate_done = true;
  return $result;
}

function fortunate_fetch($opts = NULL) {

  $haveopts = (is_array($opts));

  $root = ((($haveopts) && (array_key_exists('root',$opts)))
     ? $opts['root']
     : WPINC);

  @include($root . "/class-snoopy.php");
  if(class_exists('Snoopy')) {
    $sn = new Snoopy;
    $sn->referer = 
      ((($haveopts) && (array_key_exists('home',$opts)))
        ? $opts['home']
        : get_option('home'));
    $sn->read_timeout = 
      ((($haveopts) && (array_key_exists('read_timeout',$opts)))
        ? $opts['read_timeout']
        : 5);
    $sn->_fp_timeout = 
      ((($haveopts) && (array_key_exists('conn_timeout',$opts)))
        ? $opts['conn_timeout']
        : 5);

    $fortunate_url = "http://fortunemod.com/cookie.php?rand=" . mt_rand();
    $f = $opts['lang'];
    if($f != 'en')
      $fortunate_url .= "&lang=$f";
    $f = $opts['type'];
    if($f)
      $fortunate_url .= "&off=$f";
    $f = intval($opts['length']);
    if($f)
      $fortunate_url .= "&length=$f";
    $f = intval($opts['numlines']);
    if($f)
      $fortunate_url .= "&numlines=$f";
    $f = urlencode($opts['pattern']);
    if(strlen($f))
      $fortunate_url .= "&pattern=$f";
    $f = urlencode($opts['regex']);
    if(strlen($f))
      $fortunate_url .= "&regex=$f";
    $f = intval($opts['equal']);
    if($f)
      $fortunate_url .= "&equal=$f";
    $f = urlencode($opts['db']);
    if(strlen($f))
      $fortunate_url .= "&db=$f";
    $f = intval($opts['stats']);
    if($f)
      $fortunate_url .= "&stats=$f";
    @$sn->fetch($fortunate_url);
    $result = $sn->results;
    return $result;
  }
}

function fortunate_plugin_options() {
  
  load_plugin_textdomain($fortunate_textdomain,
     PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)),
     dirname(plugin_basename(__FILE__)));

  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save settings
    update_option('fortunate_lang',$_POST['fortunate_lang']);
    update_option('fortunate_length',$_POST['fortunate_length']);
    update_option('fortunate_numlines',$_POST['fortunate_numlines']);
    update_option('fortunate_location',$_POST['fortunate_location']);
    update_option('fortunate_title',$_POST['fortunate_title']);
    update_option('fortunate_pattern',trim($_POST['fortunate_pattern']));
    update_option('fortunate_read_timeout',
                  intval($_POST['fortunate_read_timeout']));
    update_option('fortunate_conn_timeout',
                  floatval($_POST['fortunate_conn_timeout']));
    if($_POST['fortunate_addcss'] == "1")
      update_option('fortunate_addcss',"1");
    else
      update_option('fortunate_addcss',"0");
    if($_POST['fortunate_equal'] == "1")
      update_option('fortunate_equal',"1");
    else
      update_option('fortunate_equal',"0");

    if($_POST['fortunate_advanced'] == "1") {

      // Don't update these settings unless advanced mode was already in 
      // effect. Otherwise they will get reset to defaults every time you 
      // toggle Advanced mode. 

      if(intval(get_option('fortunate_advanced'))) {
        update_option('fortunate_db',trim($_POST['fortunate_db']));
        update_option('fortunate_regex',trim($_POST['fortunate_regex']));
        update_option('fortunate_type',$_POST['fortunate_type']);
      }
      update_option('fortunate_advanced',"1");
    }
    else
      update_option('fortunate_advanced',"0");

    echo "<div class=\"updated\"><p><strong>"; 
    echo __('Options saved.', $fortunate_textdomain );
    echo "</strong></p></div>";
  }

    $fortunate_lang = get_option('fortunate_lang');
    $fortunate_type = get_option('fortunate_type');
    $fortunate_length = intval(get_option('fortunate_length'));
    $fortunate_numlines = intval(get_option('fortunate_numlines'));
    $fortunate_location = get_option('fortunate_location');
    $fortunate_title = get_option('fortunate_title');    
    $fortunate_addcss = get_option('fortunate_addcss');
    $fortunate_pattern = get_option('fortunate_pattern');
    $fortunate_regex = get_option('fortunate_regex');
    $fortunate_equal = intval(get_option('fortunate_equal'));
    $fortunate_read_timeout = get_option('fortunate_read_timeout');
    $fortunate_conn_timeout = get_option('fortunate_conn_timeout');
    $fortunate_advanced = get_option('fortunate_advanced');
    $fortunate_db = get_option('fortunate_db');

    // Now display the options editing screen

    echo '<div class="wrap">';
    echo "<h2>" 
         . __( 'Fortunate Random Quote Plugin Options', 
           $fortunate_textdomain ) 
         . "</h2>";

    echo "<p>&copy; <a href=\"http://macgirvin.com\">Mike Macgirvin</a></p>";

    // options form

   echo "<form name=\"form1\" method=\"post\" action=\"" 
     .str_replace( '%7E', '~', $_SERVER['REQUEST_URI']). "\">";

  $fortunate_lang_text = __('Select a quotation language.', $fortunate_textdomain);

   echo "<p>$fortunate_lang_text</p><p>";

   $langs = array('en' => __('English', $fortunate_textdomain),
                  'es' => __('Spanish', $fortunate_textdomain),
                  'it' => __('Italian', $fortunate_textdomain),
                  'fr' => __('French',  $fortunate_textdomain),
                  'de' => __('German',  $fortunate_textdomain),
                  'ru' => __('Russian', $fortunate_textdomain));
  
   echo __("Language:", $fortunate_textdomain);
   echo "<select name=\"fortunate_lang\">";
   foreach($langs as $k => $v) {
     echo "<option value=\"$k\""
      . (($fortunate_lang == $k) ? " selected=\"selected\" " : "")
      . ">$v</option>";
   }   
   echo "</select></p>";

   $fortunate_location_text1 = __('Select a location on the page to display the random quotes. Some locations may require css modifications appropriate to your selected theme.', $fortunate_textdomain); 

   $fortunate_location_text2 = __('Selecting \'Widget\' for the location requires an additional step: once these options are saved, go to your <a href="widgets.php">widget administration page</a> and add the widget to the desired location on the sidebar.',$fortunate_textdomain);

   $fortunate_location_text3 = __('Selecting \'Header - description\' will add the quote text to the blog description, which is generally displayed on your page header. This works best when used with 1 for \'Maximum number of lines\' (below).',$fortunate_textdomain);

   echo "<hr /><p>$fortunate_location_text1</p><p>$fortunate_location_text2</p><p>$fortunate_location_text3</p><p>";

   echo __("Where to display:", $fortunate_textdomain);
   echo "<select name=\"fortunate_location\">";
   echo "<option value=\"loop_end\""
      . (($fortunate_location == 'loop_end') ? " selected=\"selected\" " : "")
      . ">" 
      . __('Page Content Area',$fortunate_textdomain) 
      . "</option>";  
   echo "<option value=\"bloginfo\""
      . (($fortunate_location == 'bloginfo') ? " selected=\"selected\" " : "")
      . ">"
      . __('Header - description',$fortunate_textdomain)
      . "</option>";   
   echo "<option value=\"wp_footer\""
      . (($fortunate_location == 'wp_footer') ? " selected=\"selected\" " : "")
      . ">"
      . __('Footer',$fortunate_textdomain)
      . "</option>";   
   echo "<option value=\"widget\""
      . (($fortunate_location == 'widget') ? " selected=\"selected\" " : "")
      . ">"
      . __('Widget',$fortunate_textdomain)
      . "</option>";   

   echo "</select></p>";

   $fortunate_title_text = __('This setting is only used if you selected \'Widget\' above. It allows you to change the sidebar title to your liking.',$fortunate_textdomain);

   echo "<hr /><p>$fortunate_title_text</p><p>";
   echo __("Fortunate Widget Title:", $fortunate_textdomain);
   echo "<input type=\"text\" size=\"16\" name=\"fortunate_title\"
         value=\"$fortunate_title\" /></p>";

   $fortunate_css_text = __('Include the wp-content/plugins/fortunate/fortunate.css file? Some page locations may require CSS changes to display exactly as you wish. If you wish to use this feature, copy the supplied \'fortunate-template.css\' to \'fortunate.css\', make any changes you desire; and include the file by checking the box below. Alternatively, your theme may display everything just fine and you might not wish to use the additional CSS file at all.',$fortunate_textdomain);

   echo "<hr /><p>$fortunate_css_text</p><p>";
   echo __("Include plugin CSS file:", $fortunate_textdomain);
   echo "<input type=\"checkbox\" name=\"fortunate_addcss\"";
   echo (($fortunate_addcss == "1") ? " checked=\"checked\" " : "" );
   echo " value=\"1\" /></p>";


   $fortunate_length_text = __('You may restrict results to those that are less than a certain number of characters, as some entries can get quite lengthy. This number is only a guideline as it is applied to the quotation before HTML markup is applied, which might change the length a bit. If you set this in the range of 200-300, you\'ll see more simple quotations and fewer long stories and jokes. For best results, you should probably set this to 0 or something greater than 80.',$fortunate_textdomain); 

   echo "<hr /><p>$fortunate_length_text</p><p>";

   echo __("Maximum length in bytes:", $fortunate_textdomain);
   echo "<input type=\"text\" size=\"8\" name=\"fortunate_length\"
         value=\"$fortunate_length\" />"
        . __(' 0 is unlimited',$fortunate_textdomain)
        . "</p>";
   $fortunate_lines_text1 = __('You may also wish to only display results that are limited to a specific number of text lines (ignoring browser wordwrap)*. This is most useful if the setting is 1 or 2 for use in a limited space, such as on a page header or footer with a fixed height. For best results, you should probably set this to 0, 1, or 2.',$fortunate_textdomain);

   $fortunate_lines_text2 = __('* A single line from a returned quotation may contain up to 80 characters. If the target page region does not provide enough space for 80 column text, some lines may get wrapped or folded.',$fortunate_textdomain);

   echo "<hr /><p>$fortunate_lines_text1</p><p>$fortunate_lines_text2</p><p>";

   echo __("Maximum number of lines:", $fortunate_textdomain);
   echo "<input type=\"text\" size=\"8\" name=\"fortunate_numlines\"
         value=\"$fortunate_numlines\" />"
        . __(' 0 is unlimited',$fortunate_textdomain)
        . "</p>";

   $fortunate_pattern_text = __('You may also wish to only display quotations containg a specific word. For best results, either leave blank or use a very common word.',$fortunate_textdomain);

   echo "<hr /><p>$fortunate_pattern_text</p><p>";

   echo __("Specific word to search for:", $fortunate_textdomain);
   echo "<input type=\"text\" size=\"8\" name=\"fortunate_pattern\"
         value=\"$fortunate_pattern\" />"
        . "</p>";



   $fortunate_equal_text = __('For a slight performance penalty you may wish to randomize the quotation categories, which provides equal weight to categories from different data sources on the server, regardless of the number of quotations in that category. This merely adds an extra level of randomization and is not normally required.',$fortunate_textdomain);

   echo "<hr /><p>$fortunate_equal_text</p><p>";
   echo __("Randomize categories:", $fortunate_textdomain);
   echo "<input type=\"checkbox\" name=\"fortunate_equal\"";
   echo (($fortunate_equal == "1") ? " checked=\"checked\" " : "" );
   echo " value=\"1\" /></p>";

   $fortunate_timeout_text = __('The following timeouts will allow for graceful failure if the random quotes take too much time to load. Set these to a few seconds so your blog will still display normally in a reasonable period of time (although without Fortunate) should a network disruption occur.',$fortunate_textdomain);


   echo "<hr /><p>$fortunate_timeout_text</p><p>";

   echo __("Connect timeout (seconds):", $fortunate_textdomain);
   echo "<input type=\"text\" size=\"8\" name=\"fortunate_conn_timeout\"
         value=\"$fortunate_conn_timeout\" />"
        . "</p>";

   echo "<p>";
   echo __("Data timeout (seconds):", $fortunate_textdomain);
   echo "<input type=\"text\" size=\"8\" name=\"fortunate_read_timeout\"
         value=\"$fortunate_read_timeout\" />"
        . "</p>";


   $fortunate_advanced_text = __('Advanced usage. Select the Advanced mode checkbox and submit this page. You will then have additional options such as choosing offensive (sexual or off-colored quotes), specific databases to search, and the ability to provide a MySQL regular expression for searches. If you deselect this checkbox, any advanced options will be ignored.',$fortunate_textdomain);

   echo "<hr /><hr /><p>$fortunate_advanced_text</p><p>";

   echo __("Advanced mode:", $fortunate_textdomain);
   echo "<input type=\"checkbox\" name=\"fortunate_advanced\"";
   echo (($fortunate_advanced == "1") ? " checked=\"checked\" " : "" );
   echo " value=\"1\" /></p>";

   if($fortunate_advanced == "1" ) {

     echo "<hr /><hr />"
       . __("Advanced Options:", $fortunate_textdomain);

     $fortunate_off_text = __('By default, assume that this content is for general audiences and do not include quotations and jokes which are overtly sexual, racial, touch on religious sensibilities, or otherwise might be deemed offensive. If you change this setting, be aware that members of your audience may be offended or insulted. You have been warned. This setting currently only applies to English, Spanish, and Italian content. Other languages have not yet been content rated and \'Normal\' is the only setting which will work.',$fortunate_textdomain);

     echo "<hr/><p>$fortunate_off_text</p><p>";
     echo __("Quotation Rating:", $fortunate_textdomain);
     echo "<select name=\"fortunate_type\">";
     echo "<option value=\"\""
        . (($fortunate_type == '') ? " selected=\"selected\" " : "")
        . ">"
        . __('Normal - no offensive/adult',$fortunate_textdomain)
        . "</option>";   
     echo "<option value=\"a\""
        . (($fortunate_type == 'a') ? " selected=\"selected\" " : "")
        . ">"
        . __('Any - including offensive/adult',$fortunate_textdomain)
        . "</option>";   
     echo "<option value=\"o\""
        . (($fortunate_type == 'o') ? " selected=\"selected\" " : "")
        . ">"
        . __('Only offensive/adult',$fortunate_textdomain)
        . "</option>";   
     echo "</select></p>";


     $fortunate_db_text = __('You have the option to choose a specific database (the names of any applicable databases will be shown in the statistics report at the bottom of this form). Changing the search parameters significantly will affect whether or not any quotes are returned from a specific database, so using this feature may require a bit of trial and error. Specifically, if you change the language or quotation ratings, please submit and reload this page (without a selected database) to determine which databases are available with those settings.',$fortunate_textdomain);

     echo "<hr/><p>$fortunate_db_text</p><p>";

     echo __("Use this database:", $fortunate_textdomain);
     echo "<input type=\"text\" size=\"8\" name=\"fortunate_db\"
          value=\"$fortunate_db\" />"
          . "</p>";



     $fortunate_regex_text = __('[Very Advanced Usage!] You may also select quotations by using (MySQL) Regular Expressions. Please leave this blank unless you know exactly what you are doing.',$fortunate_textdomain);

     echo "<hr /><p>$fortunate_regex_text</p><p>";

     echo __("Regular Expression:", $fortunate_textdomain);
     echo "<input type=\"text\" size=\"8\" name=\"fortunate_regex\"
          value=\"$fortunate_regex\" />"
          . "</p>";



   }



   echo "<hr /><hr /><p class=\"submit\">"
     . "<input type=\"submit\" name=\"Submit\" value=\"";
   echo __('Update Options', $fortunate_textdomain ); 
   echo "\" /></p></form>";



   echo "<hr /><hr />" 
     . __("Sample:", $fortunate_textdomain) 
     . fortunate( ABSPATH . WPINC, 1 ) 
     . "</hr>";
   echo "</div>";

}



<?php
/* Developed by Kernel Team.
   http://kernel-team.com
*/

// brake line
$bbcode["/\[br\]/Usi"]="<br/>";

// link
//$bbcode["/\[a\ *href\ *=[\ '\"]*([^\ '\"\]]*)[\ '\"]*.*\](.*)\[\/a\]/Usi"]="<a href=\"\\1\">\\2</a>";

// bolded text
$bbcode["/\[b\](.*)\[\/b\]/Usi"]="<strong>\\1</strong>";

// italicized text
$bbcode["/\[i\](.*)\[\/i\]/Usi"]="<em>\\1</em>";

// underlined text
$bbcode["/\[u\](.*)\[\/u\]/Usi"]="<ins>\\1</ins>";

// strikethrough text
$bbcode["/\[s\](.*)\[\/s\]/Usi"]="<del>\\1</del>";

// quoted text
$bbcode["/\[quote\](.*)\[\/quote\]/Usi"]="<blockquote>\\1</blockquote>";

// monospaced text
$bbcode["/\[code\](.*)\[\/code\]/Usi"]="<pre>\\1</pre>";

// image
$bbcode["/\[img\](.*)\[\/img\]/Usi"]="<img src=\"\\1\" alt=\"\"/>";

// smileys
$smileys[":)"]          = "<img src='$config[statics_url]/static/images/emoticons/smile.png' alt=':)'/>";
$smileys["8-)"]         = "<img src='$config[statics_url]/static/images/emoticons/cool.png' alt='8-)'/>";
$smileys[";("]          = "<img src='$config[statics_url]/static/images/emoticons/cwy.png' alt=';('/>";
$smileys[":D"]          = "<img src='$config[statics_url]/static/images/emoticons/grin.png' alt=':D'/>";
$smileys[":("]          = "<img src='$config[statics_url]/static/images/emoticons/sad.png' alt=':('/>";
$smileys[":O"]          = "<img src='$config[statics_url]/static/images/emoticons/shocked.png' alt=':O'/>";
$smileys[":P"]          = "<img src='$config[statics_url]/static/images/emoticons/tongue.png' alt=':P'/>";
$smileys[";)"]          = "<img src='$config[statics_url]/static/images/emoticons/wink.png' alt=';)'/>";
$smileys[":heart:"]     = "<img src='$config[statics_url]/static/images/emoticons/heart.png' alt=':heart:'/>";
$smileys[":ermm:"]      = "<img src='$config[statics_url]/static/images/emoticons/ermm.png' alt=':ermm:'/>";
$smileys[":angel:"]     = "<img src='$config[statics_url]/static/images/emoticons/angel.png' alt=':angel:'/>";
$smileys[":angry:"]     = "<img src='$config[statics_url]/static/images/emoticons/angry.png' alt=':angry:'/>";
$smileys[":alien:"]     = "<img src='$config[statics_url]/static/images/emoticons/alien.png' alt=':alien:'/>";
$smileys[":blink:"]     = "<img src='$config[statics_url]/static/images/emoticons/blink.png' alt=':blink:'/>";
$smileys[":blush:"]     = "<img src='$config[statics_url]/static/images/emoticons/blush.png' alt=':blush:'/>";
$smileys[":cheerful:"]  = "<img src='$config[statics_url]/static/images/emoticons/cheerful.png' alt=':cheerful:'/>";
$smileys[":devil:"]     = "<img src='$config[statics_url]/static/images/emoticons/devil.png' alt=':devil:'/>";
$smileys[":dizzy:"]     = "<img src='$config[statics_url]/static/images/emoticons/dizzy.png' alt=':dizzy:'/>";
$smileys[":getlost:"]   = "<img src='$config[statics_url]/static/images/emoticons/getlost.png' alt=':getlost:'/>";
$smileys[":happy:"]     = "<img src='$config[statics_url]/static/images/emoticons/happy.png' alt=':happy:'/>";
$smileys[":kissing:"]   = "<img src='$config[statics_url]/static/images/emoticons/kissing.png' alt=':kissing:'/>";
$smileys[":ninja:"]     = "<img src='$config[statics_url]/static/images/emoticons/ninja.png' alt=':ninja:'/>";
$smileys[":pinch:"]     = "<img src='$config[statics_url]/static/images/emoticons/pinch.png' alt=':pinch:'/>";
$smileys[":pouty:"]     = "<img src='$config[statics_url]/static/images/emoticons/pouty.png' alt=':pouty:'/>";
$smileys[":sick:"]      = "<img src='$config[statics_url]/static/images/emoticons/sick.png' alt=':sick:'/>";
$smileys[":sideways:"]  = "<img src='$config[statics_url]/static/images/emoticons/sideways.png' alt=':sideways:'/>";
$smileys[":silly:"]     = "<img src='$config[statics_url]/static/images/emoticons/silly.png' alt=':silly:'/>";
$smileys[":sleeping:"]  = "<img src='$config[statics_url]/static/images/emoticons/sleeping.png' alt=':sleeping:'/>";
$smileys[":unsure:"]    = "<img src='$config[statics_url]/static/images/emoticons/unsure.png' alt=':unsure:'/>";
$smileys[":woot:"]      = "<img src='$config[statics_url]/static/images/emoticons/w00t.png' alt=':woot:'/>";
$smileys[":wassat:"]    = "<img src='$config[statics_url]/static/images/emoticons/wassat.png' alt=':wassat:'/>";

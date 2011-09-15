#!/usr/bin/perl -w

use wg;

$baseurl = "http://localhost/library";

$call_html = wg("$baseurl/call.php");

if ($call_html =~ /Unclassified(.*)/) {
    print($1);
}

else {
print $call_html;
print "\nFUCK\n\n"
}

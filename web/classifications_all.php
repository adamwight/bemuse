<?
$no_call_setup = TRUE;
include_once("classifications.php");

include("classifications_dewey.php");
$call_dewey = $call_system;
unset($call_system);

include("classifications_lc.php");
$call_lc = $call_system;
unset($call_system);

include("classifications_h.php");
$call_h = $call_system;

unset($call_system); //this one is to fuck with bad code
?>

<?php
require_once("session.php");

// all call systems should support these functions
interface headings
{
    public function get_heading($call);
    public function get_heading_verbose($call, $this_call_type);
    public function get_call_major($call);
    public function get_shelf_label($call);

    public function get_heading_if_seq($call);
    public function get_shelf_label_if_seq($call);
}

// headings_base can be used for static (lookup-table)
// or dynamic (users-constructed) call systems
abstract class headings_base implements headings
{
    var $call_major_re;

    function get_heading_verbose($call, $this_call_type)
    {
	global $call_type;
	$saved_type = $call_type;
	$call_type = "call_" . $this_call_type;
	setup_call_vars();

	$heading = $this->get_heading($call);
	$shelf = $this->get_shelf_label($call);

	$call_type = $saved_type;

	return
	  (isset($heading) ? $heading : "Undefined subject")
	  . (isset($shelf)
	      ? ($shelf != "Unclassified"
		  ? "; on the $shelf shelf" : " on an unknown shelf")
	      : "");
    }

    // should be in heading_printer
    function get_heading_if_seq( $call )
    {
	static $last_topic = "";

	$heading = $this->get_heading($call);

	if ($heading == $last_topic)
	    return NULL;

	$last_topic = $heading;

	return $heading;
    }
    
    function get_shelf_label_if_seq($call)
    {
	static $last_topic = "";

	$heading = $this->get_shelf_label($call);

	if ($heading == $last_topic)
	    return NULL;

	$last_topic = $heading;

	return $heading;
    }

    function get_call_major($call)
    {
	global $call_type;

	$matches = array();
	if (preg_match( $this->call_major_re, $call, $matches ))
	{
	    return $matches[1];
	}
	return NULL; // implicit?
    }
}

// static headings are probably stored in a lookup table
abstract class headings_static extends headings_base
{
    var $classifications;
    var $shelf_labels;

    function get_heading($call)
    {
	if (!isset($call)) {
	    return NULL;
	}
	$call_major = $this->get_call_major($call);
        if (!isset($call_major) || $call_major == "") {
            return NULL;
        }
        if (isset($this->classifications[$call_major])) {
            $heading = $this->classifications[$call_major];
        } else {
	    global $call_type;
            error_log( "Subject heading not found: $call ($call_type)" );
	    return NULL;
        }

	return $heading;
    }

    function get_shelf_label($call)
    {
	if (!isset($call)) {
	    return NULL;
	}
	if (strlen($call)>0)
	{
	    foreach ($this->shelf_labels as $label => $call_re)
	    {
		// XXX there really isn't a shorthand for RE hash?
		$matches = array();
		if (preg_match("/^$call_re/", $call, $matches ))
		{
		    return $label;
		}
	    }
	}
	return "Unclassified";
    }
}

if (!isset($no_call_setup)) {
    setup_call_vars();
    setup_classifications();
}

abstract class headings_db extends headings_base
{
    // cache has the lifespan of one interaction
    var $heading_cache = array();

    function get_heading($call)
    {
	global $call_type;

	$call_major = $this->get_call_major($call);

	if (!isset($call_major))
	    return NULL;

	if ($cached = $heading_cache[$call_major])
	    return $cached;

	$result = db_query(
	    "SELECT name FROM call_${call_type}_heading " .
		"WHERE abbrev=?", $call_major);
	list ($heading) = $result->fetch(PDO::FETCH_NUM);
	if (!$heading)
	{
	    global $call_type;
	    if (isset($call_major))
		error_log("Subject heading not found for $call ($call_type)");
	    unset($heading);
	}

	$heading_cache[$call_major] = $heading;
	return $heading;
    }

    function get_shelf_label($call)
    {
	return "Unclassified";
    }
}
?>

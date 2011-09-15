<?
/*
 * The halfway library's homemade call numbering.
 */
class headings_h extends headings_db
{
    function headings_h()
    {
	$this->call_major_re = "/^([a-zA-Z]+)/";
    }

    // db.do("INSERT heading into h_headings");
    function new_heading()
    {
    }
}

global $call_system;
$call_system = new headings_h();

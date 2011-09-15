<?
// $Id: librarian.php,v 1.6 2007/02/20 08:40:13 adamw Exp $

class librarian
{
    var $filter, $raw_location, $location_id, $location_array;

    function librarian($location)
    {
	$this->raw_location = $location;
    }

    function get_db_location()
    {
        if (!isset($this->db_location))
        {
            $sql = "SELECT city, librarian, contact, abbrev, description " .
                "FROM location WHERE n=" . $this->location_id;
            $result = db_query($sql);

            $this->db_location = mysql_fetch_assoc($result);
        }

        return ($this->db_location);
    }

    function print_library_noun()
    {
	echo $this->get_library_noun();
    }

    function get_abbrev()
    {
        $loc = get_db_location();
        return $loc['abbrev'];
    }

    function get_library_noun()
    {
	global $n_locations; // lazy, saves a select
	global $long_city;
	global $with_contact;

	$out = "";
	$this->get_location_id();

	if ($this->location_id == -1)
	{
	    $out = "<b>All locations</b>\n";
	}
	elseif ($this->location_id > 0)
	{
	    $row = $this->get_db_location();

	    $city = htmlspecialchars($row['city']);
	    $librarian = htmlspecialchars($row['librarian']);
	    $description = htmlspecialchars($row['description']);

	    $out = $this->link_browse($this->location_id, $librarian)
		. "'s library in " . $this->link_browse($city, $city);

	    if ($with_contact && $row['contact'])
	    {
		$contact = '(' . htmlspecialchars($row['contact']) . ')';
		$contact = str_replace( '@', '&nbsp;at ', $contact );
		$contact = str_replace( '.', ' dot&nbsp;', $contact );

		$out .= " $contact";
	    }
	}
	else
	{
	    // location is a city name
	    $location_h = htmlspecialchars($this->raw_location);

	    $linked = $this->link_browse($this->raw_location, $location_h);

	    if ($long_city == 1)
	    {
		if ($n_locations == 1)
		    $out = "the only <b>$linked</b> location\n";
		elseif ($n_locations == 2)
		    $out = "both <b>$linked</b> locations\n";
		else
		    $out = "all $n_locations <b>$linked</b> locations\n";
	    }
	    else
		$out = $linked;
	}

	return $out;
    }

    function link_browse($loc, $name)
    {
	global $location;
	if ($location != $loc)
	    return "<a href=\"browse.php?l=$loc\"><b>$name</b></a>";
	else
	    return "<b>$name</b>";
    }

    function print_library_order()
    {
	global $order, $call_column;

	echo ' ';
	switch ($order)
	{
	    case $call_column:	echo 'by call number';	    break;
	    case 'title_sort':	echo 'by title';	    break;
	    case 'author':	echo 'by author';	    break;
	    case 'n DESC':	echo 'in the order entered'; break;
	}
    }

    function print_location_images()
    {
	$sql = "SELECT n, attr FROM image WHERE location_id=" . $this->get_location_id();

	$result = db_query( $sql );

	while ($row = mysql_fetch_row( $result ))
	{
	    echo "<p><img src=\"image/$row[0]\" $row[1]>\n";
	    echo "<br>[<a href=\"browse.php?l=" . $this->location_id . "&a=di&i=$row[0]\">";
	    echo "delete image</a>]\n";
	}
    }

    function get_location_id()
    {
	list ($this->location_id) = sscanf($this->raw_location, "%d");
	if (!isset($this->raw_location)) $this->location_id = -1;
	return $this->location_id;
    }

    function setup_library_filter()
    {
	global $n_locations;

	$this->get_location_id();

	if ($this->location_id == -1) //is... everywhere
	{
	    $this->filter = 'true';
	}
	elseif ($this->location_id > 0) //library
	{
	    $this->filter = "location_id=" . $this->location_id;
	    $this->location_array = array($this->location_id);
	}
	else //city name
	{
	    $sql = "SELECT GROUP_CONCAT(n SEPARATOR ',') "
                    . "FROM location "
                    . "WHERE city='" . addslashes( $this->raw_location ) . "'";
	    $result = db_query( $sql );
	    list($ids) = mysql_fetch_row( $result );

	    $this->filter = "location_id in ($ids)";
	    $this->location_array = explode(',', $ids);
            $n_locations = count($this->location_array);
	}
    }

    function get_where()
    {
	if (!isset($filter))
	    $this->setup_library_filter();

        return $this->filter;
    }

    function get_location_array()
    {
	if (!isset($filter))
	    $this->setup_library_filter();

	return $this->location_array;
    }

    function print_library_stats()
    {
	global $with_instruments;

	echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;";

	$sql = "SELECT COUNT(*) FROM physical WHERE " . $this->get_where();
	$result = db_query( $sql );
	$row = mysql_fetch_row( $result );
	$total = $row[0];

	/*
	$sql = "SELECT COUNT(*) AS dupes FROM physical WHERE "
	     . $this->get_where()
	     . " GROUP BY book_id HAVING dupes>1";
	$result = db_query( $sql );
	$duplicates = 0;
	while ($row = mysql_fetch_row($result))
	    $duplicates += $row[0];*/

	if ($total == 1) echo "1 book";
	else echo "$total books";

	/*if ($duplicates > 0)
	{
	    echo ", <font color=red>$duplicates duplicate";
	    if ($duplicates > 1) echo "s";
	    echo "</font>";
	}*/

	if ($with_instruments)
	{
	    $sql = "SELECT COUNT(*) FROM instrument WHERE " . $this->get_where();
	    $result = db_query( $sql );
	    $row = mysql_fetch_row( $result );
	    $total = $row[0];

	    if ($total == 1) echo " and 1 instrument";
	    elseif ($total) echo " and $total instruments";
	}

	echo " available";
    }
}
?>

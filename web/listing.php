<?
include_once( "classifications.php" );
include_once( "common.php" );

$divide_topics = 0;
$location = get_session_value( 'l', NULL );
?>

<html>
<head>
    <title>The Halfway Library</title>
    <style TYPE="text/css">
	td {
	    font-family: times, serif;
            font-size: 12pt;
	}
	a {
	    color: black;
	    text-decoration: none;
	}
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#003366" vlink="#666666" alink="#003366">

<center>
<table cellspacing=0 cellpadding=20 border=0 width="100%">
<tr>
<td>

    <? print_menu(); ?>

</td>
<td>

    <? print_title(); ?>

</td>
</tr>
</table>
</center>

<p>
<?
    list_by_subject();
    print_locations();

print_footer();

function list_by_subject()
{
    global $classifications, $divide_topics, $hilight_accessible, $location;

?>
<table cellspacing=0 cellpadding=0 border=0>
<tr><td><table cellspacing=0 cellpadding=2 border=0 bgcolor=white>

<?
$sql = "SELECT book.n, book.title_full, book.author, book.responsible, "
	    . "book.call_lc, location.n, location.city, count(*) "
	  . "FROM physical, location, book "
	  . "WHERE physical.book_id=book.n AND physical.location_id=location.n "
	  . "AND informed "
	  . "ORDER BY call_lc, title_sort, location.city ";

$result = db_query( $sql );

$even_row = 0;
$last_topic = NULL;

while ($row = $result->fetch())
{
    $matches = array();
    if (preg_match( "/^([a-zA-Z]+)/", $row[4], $matches ))
    {
	$cur_topic = $matches[1];

	if ($cur_topic != $last_topic)
	{
	    if ($divide_topics)
		echo "<tr><td colspan=4><hr size=2 noshade></td></tr>\n";

	    if ($classifications[$cur_topic])
		echo "<tr><td colspan=2><font size=\"+1\"><b>$classifications[$cur_topic]</b></font></td></tr>\n";
	    else
		error_log( "Classification not found: $cur_topic" );
	}
	$last_topic = $cur_topic;
    }

    if ($even_row) $color = "bgcolor=\"#ffffcc\"";
    else $color = "bgcolor=\"#ffeedd\"";

    echo "<tr>",
         "<td $color><u>$row[1]</u>";

    if ($row[3]) echo " / <i>$row[3]</i>";
    elseif ($row[2]) echo " / <i>$row[2]</i>";

    $city = str_replace( ' ', '&nbsp;', $row[6] );
    echo "</i></td><td $color>",
	$row[6], "</td></tr>\n";
    
    $even_row = !$even_row;
}

?>
</table>
</td>
</tr>
</table>
<?
}
?>

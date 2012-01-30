<?
// $Id: call.php,v 1.32 2007/02/20 07:59:49 adamw Exp $

require_once( "classifications.php" );
require_once( "common.php" );
require_once( "session.php" );
require_once( "librarian.php" );

$debug=0;
$hilight_accessible = 1;
$divide_topics = 0;
$show_call = 0;
$local_only = get_session_value( 'lo', 0 );
$long_city = 0;
$with_unclassified = 1;
$publicity_expr = "publicity > 0"; // do not list private books

get_any_location();
$library = new librarian($location);
        # XXX
        #mb_detect_order(mb_list_encodings());

    print_header("Shelf by Call No");
    print_title();
    print_legend();

    list_by_call();
    if ($with_unclassified)
	list_unclassified();

    print_footer();


function print_legend()
{
    global $hilight_accessible, $location, $local_only, $library;

    if ($location == -1 || !$location)
    {
	$hilight_accessible = 0;
    }
    else
    {
	$noun = $library->get_library_noun();
?>
<div id=legend>
    <?
    if ($local_only)
    {
	?>
	Only books in <?=$noun?> are listed.<br>
	[<a href="call.php?lo=0">show all books</a>]
	<?
    }
    else
    {
	?>
	Books marked with "<img src="img/avail.png" alt=">">" are in <?=$noun?>.
	<br>
	[<a href="call.php?lo=1">show only this location</a>]
	<?
    }
?>
</div>
<?
    }
}

function list_by_call()
{
    global $call_column, $publicity_expr, $library;

$sql = "SELECT b.n, b.title_full, b.author, b.responsible, b.$call_column, "
            . "GROUP_CONCAT(DISTINCT l.n SEPARATOR ',') AS location_id, "
            . "GROUP_CONCAT(DISTINCT l.abbrev SEPARATOR ',') AS abbrev "
	. "FROM physical p "
	  . "LEFT JOIN book b ON p.book_id=b.n "
	  . "LEFT JOIN location l ON p.location_id=l.n "
	. "WHERE $call_column IS NOT NULL AND length($call_column)>0 "
          . "AND ($publicity_expr) "
          . "AND (".$library->get_where().") "
	. "GROUP BY b.n, l.city "
	. "ORDER BY $call_column, b.title_sort, l.city";

    $result = db_query( $sql );

    print_results_by_subject($result);
}

function list_unclassified()
{
    global $call_column, $publicity_expr;

$sql = "SELECT b.n, b.title_full, b.author, b.responsible, b.$call_column, "
            . "GROUP_CONCAT(DISTINCT l.n SEPARATOR ',') AS location_id, "
            . "GROUP_CONCAT(DISTINCT l.abbrev SEPARATOR ',') AS abbrev "
	  . "FROM physical p "
	  . "LEFT JOIN book b ON p.book_id=b.n "
	  . "LEFT JOIN location l ON p.location_id=l.n "
	  . "WHERE $call_column IS NULL OR length($call_column)=0 "
	  . "AND b.title_sort IS NOT NULL "
          . "AND ($publicity_expr) "
	  . "GROUP BY b.n  ORDER BY title_sort";

    $result = db_query( $sql );

    print_results_by_subject($result);
}
/*
 * Takes a result of (
 *   book: n, title_full, author, responsible, call_X,
 *   location: n, city,
 *   count)
 */
function print_results_by_subject($result)
{
    global $classifications, $divide_topics, $hilight_accessible, $location,
       $show_call, $local_only, $call_type, $call_column, $debug, $call_system;

    $avail_img = "<img src=\"img/avail.png\" alt=\">\">";

    ?><div id=call_list><table><?

    $even_row = 0;
    //$last_topic = NULL;

    while ($row = mysql_fetch_assoc( $result ))
    {
	$shelf_label = $call_system->get_shelf_label_if_seq($row[$call_column]);

	if (isset($shelf_label))
	    echo "<tr><td colspan=4><h2>$shelf_label</h2></td></tr>\n";

	$heading = $call_system->get_heading_if_seq($row[$call_column]);

	if (isset($heading))
	{
	    if ($divide_topics)
		echo "<tr><td colspan=4><hr size=2 noshade></td></tr>\n";

	    $call_major = $call_system->get_call_major($row[$call_column]);
	    echo "<tr><td colspan=4><a name=\"$call_major\"><h3>$heading</h3></a></td></tr>\n";
	}

	if ($even_row) $style = 'even';
	else $style = 'odd';

	if ($hilight_accessible
	    && is_here($row['location_id']))
	    $status = $avail_img;
	else
	    $status = "&nbsp;";

	echo "<tr class=$style><td>$status</td>";
	if ($show_call)
	{
	    $call = $row[$call_column];
            // cut the minor # off
	    if ($call_type == 'lc')
		$call = preg_replace('/ \d\d\d\d[a-z]?$/', '', $call);
	    echo "<td>$call</td>";
	}
	$title = $row['title_full'];
//$enc = mb_detect_encoding($title);
#$title = mb_convert_encoding($title, 'UTF-8', '');
	if (!$title)
	    $title = "<i>No title</i>";
	echo "<td><u><a href=\"edit.php?b=" . $row['n'] . "\">"
	    . $title . "</a></u>";

	if ($row['responsible'])
	    echo " / <i>" . $row['responsible'];
	elseif ($row['author'])
	    echo " / <i>" . $row['author'];

	echo '</i></td><td><div class="a">' . $row['abbrev'] . "</div></td></td></tr>\n";
        if ($debug > 1) print_r($row);

	$even_row = !$even_row;
    }

    ?></table></div><?
}

function is_here( $loc_id )
{
    global $library;

    if (strstr($loc_id,','))
    {
        #return intersects(explode(',', $loc_id), $library->get_location_array());
        foreach (explode(',', $loc_id) as $loc)
            if (is_here($loc)) return true;
    }
    else
        return in_array($loc_id, $library->get_location_array());
}
?>

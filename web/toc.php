<?
include("common.php");
include("session.php");
include("classifications.php");

print_header("Table of Contents");

print_toc();

function print_toc()
{
    global $call_column;

    echo "<div id=toc>\n";
    echo "<h1>Table of Contents</h1>\n";

    $sql = "SELECT b.$call_column, count(*) "
	      . "FROM physical p, book b "
	      . "WHERE p.book_id=b.n "
	      . "AND b.$call_column IS NOT NULL AND length(b.$call_column)>0 "
	      . "AND p.publicity > 0 "
	      . "GROUP BY b.n "
	      . "ORDER BY b.$call_column";

    $result = db_query( $sql );

    while ($row = $result->fetch())
    {
	print_heading_verbose_link($row[0]);
    }
    echo "</div>\n";
}

function print_heading_verbose_link($call)
{
    global $call_system;

    $heading = $call_system->get_heading_if_seq($call);
    $shelf_label = $call_system->get_shelf_label_if_seq($call);

    if (isset($shelf_label))
    {
	echo "<h1>$shelf_label</h1>\n";
    }

    if (isset($heading))
    {
	echo "<a href=\"call.php?#"
            . $call_system->get_call_major($call)
            . "\"><h3>$heading</h3></a>\n";
    }
}
?>

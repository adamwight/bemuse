<?
require_once("common.php");
require_once("book.php");
require_once("librarian.php");
require_once("session.php");

$debug = FALSE;
$with_books = 0;
$with_scoreboard = 0;
$order = 'title_sort';  //  book order iff with books
$long_city = TRUE;
$with_contact = TRUE;

if (isset($_GET['c'])) {
    $with_books = TRUE;
    $by_librarian = TRUE;
}

setup_call_vars();

    print_header("Librarians");

    if (isset($by_librarian) && $by_librarian)
        print_collections( );
    else
        print_locations( );

    print_footer();

// class people_browser {
function print_locations()
{
?>
<div id=locations>
<a href="browse.php?l=-1"><b>All locations</b></a></p>
<?
    $sql = "SELECT city, COUNT(*) " .
	    "FROM location l JOIN physical p ON l.n=p.location_id " .
	    "WHERE p.publicity > 0 GROUP BY city";
    $result = db_query( $sql );

    while ($row = mysql_fetch_row($result))
    {
	print_city_books( $row[0], $row[1] );
    }
    echo "</div>\n";
}

function print_collections()
{
    global $with_books;
?><div id=locations><?
    $sql = "SELECT l.n FROM location l "
    	. "JOIN physical p ON l.n=p.location_id "
	. "WHERE p.publicity > 0 "
    	. "GROUP BY location_id ORDER BY l.librarian, l.city";
    $result = db_query( $sql );

    while ($row = mysql_fetch_row($result))
    {
	$library = new librarian($row[0]);
	?><div style="margin-top: 1em;"><?
	$library->print_library_noun();
        if ($with_books) {
            $printer = new book_printer();
            ?><div style="margin-left: .25in;"><?
            $printer->print_books($library->get_where());
            ?></div><?
        }
	?></div><?
    }
    ?></div><?
}

function print_city_books( $city, $n_books )
{
    global $with_books, $with_scoreboard;

    $sql = "SELECT l.librarian, l.description, l.n, COUNT(*) as nobooks "
	. "FROM physical p "
		. "JOIN location l ON l.n=p.location_id "
    	. "WHERE l.city='" . addslashes($city) . "' AND publicity > 0 "
    	. "GROUP BY l.librarian";
    $result = db_query( $sql );

    $city = htmlspecialchars($city);
    $n_people = mysql_num_rows( $result );
    $n_per = round($n_books / $n_people, 1);

    // city
    echo "<a href=\"browse.php?l=$city\">$city</a> ("
	. plural($n_books, "book", "books");
    if ($n_people > 2) echo ", $n_per per capita";
    //n.b. with_scoreboard --> scoreboard_detail/mode
    echo ")<br>\n";
    echo "<div style=\"margin: 0px 0px 1em .25in;\">\n";

    // librarians
    while ($row = mysql_fetch_assoc( $result ))
    {
	$librarian = htmlspecialchars( $row['librarian'] );

	echo "<a href=\"browse.php?l=".$row['n']."\">$librarian</a>";
	if ($with_scoreboard) echo " (".$row['nobooks'].")";
        if (isset($row['description']))
            echo ": " . $row['description'];
	echo "<br>\n";

	if ($with_books)
	{
	    $library = new librarian($row['n']);
	    $printer = new book_printer();
	    ?><div style="margin-left: .25in;"><?
	    $printer->print_books($library->get_where());
	    ?></div><?
	}
    }

    echo "</div>\n";
}

function plural($n, $singular, $plural)
{
    if ($n == 1)
	return "1 $singular";
    else
	return "$n $plural";
}
?>

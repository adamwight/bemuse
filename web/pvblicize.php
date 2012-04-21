<?
// $Id: browse.php,v 1.31 2007/02/20 08:40:13 adamw Exp $
global $location, $with_circulation, $location_id;
$with_circulation = 0;
$with_incomplete = 1;
$as_table = 0;
$long_city = 1;
$with_contact = 1;
$with_instruments = 1;

require_once("common.php" );
require_once("session.php" );
require_once("librarian.php");
require_once("book.php");


get_any_location();

setup_call_vars();

$order = order_key_to_column(
	    get_session_value( 's', 't' ));

// delete image
if (isset( $_GET['a'] ) && ($_GET['a'] == 'di'))
{
    delete_image($_GET['i'], $location_id, 'location');
}

    print_header("Shelf by Location"); // XXX use location string

    if (!$location)
    {
	?><h1>Select a location.</h1><?
    }
    else
    {
	print_library($location);
	echo "<p>\n";
	echo "Change libraries...<br>\n";
    }
?>
<form action="browse.php" method=get>
    <? print_locations_options( ); ?>
    <input type=submit value="Show">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <? print_sort_menu(); ?>
</form>
<p>
<?

print_library_contents($location);
print_library_images();


print_footer();

function print_library($location)
{
    global $order;

    $library = new librarian($location);
    if (!isset($library))
	return;

    $library->setup_library_filter();

    $library->print_library_noun($location);
    //print_librarian_contact($location);
    $library->print_library_order($order);

    $library->print_library_stats();
}

function print_library_contents($location)
{
    global $with_instruments;

    $library = new librarian($location);
    if (!$library)
	return;

    $library->setup_library_filter();

    ?><div id=books><?
    if ($with_instruments)
	list_instruments($library->get_where());

    list_books($library->get_where());
    ?></div><?
}

function print_library_images()
{
    global $location_id, $location;

    $library = new librarian($location);
    if (!$library)
	return;

    if ($location_id > 0)
    {
	echo "<p>";

	print_attach_image( 'l', $location );
	$library->print_location_images();
    }
}

function print_sort_menu()
{
    global $order, $call_column;
?>
    Sort by:
<select name=s>
    <option value=t<?= is_selected( $order, 'title_sort' ) ?>>Title
    </option>
    <option value=a<?= is_selected( $order, 'author'	 ) ?>>Author
    </option>
    <option value=c<?= is_selected( $order, $call_column ) ?>>Call no.
    </option>
    <option value=n<?= is_selected( $order, 'n DESC'	 ) ?>>Date added
    </option>
</select>
<?
}

function list_instruments($where)
{
    $sql = "SELECT n, type, brand, year FROM instrument $where ORDER BY type";
    $result = db_query($sql);

    if ($result->rowCount())
	echo "<h2>Instruments</h2>";

    while ($row = $result->fetch())
    {
       $type = htmlspecialchars( $row[1] );
       $instrument = "<u><a href=\"edit_i.php?i=$row[0]\">"
                   . "$type</a></u>";
       $brand = htmlspecialchars( $row[2] );
       $year = htmlspecialchars( $row[3] );

       echo "$instrument";
       if ("$year$brand" != '')
       {
           echo " ($year $brand)";
       }
       echo "<br>";
    }
}

function list_books($where)
{
    $printer = new book_printer();
    echo "<h2>Books</h2>";
    $printer->print_books($where);
}

function delete_image($image_id, $obj_id, $type)
{

    list ($id) = sscanf($image_id, "%d");

    $sql = "UPDATE image SET ?=? "
		. "WHERE n=? AND ?=?";

    $column = "${type}_id";
    db_query( $sql, $column, 0-$obj_id, $id, $column, $obj_id );
}
?>

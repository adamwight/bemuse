<?
include( "common.php" );

if ($_POST['location'])
{
    header( "Content-Type: text/plain" );

    $count = count( $_POST['location'] );
    $locs = array();

    for ($i = 0; $i < $count; $i++) $locs[] = $_POST['location'][$i];

    $locations = join( ',', $locs );

    $sql = "SELECT location.*,COUNT(*) AS count FROM location, physical "
	 . "WHERE location.n IN ($locations) "
	 . "AND location.n=physical.location_id GROUP BY location_id";

    $result = mysql_query( $sql );
    echo mysql_error();

    echo "flat file format v1.0\n\n";
    echo "locations:\n";
    while ($row = mysql_fetch_assoc( $result ))
    {
	echo $row['n'] . ": " . $row['city'] . " (" . $row['count'] . " books)\n";
	echo "    l: " . $row['librarian'] . "\n";
	echo "    c: " . $row['contact'] . "\n";
    }
    echo "\n";

    $sql = "SELECT book.*, physical.location_id, location.city "
	  . "FROM physical, location, book "
	  . "WHERE physical.book_id=book.n "
	  . "AND physical.location_id in ($locations) "
	  . "AND physical.location_id = location.n "
	  . "ORDER BY location.n,book.n";

    $result = mysql_query( $sql );
    echo mysql_error();

    while ($row = mysql_fetch_assoc( $result ))
    {
	echo "t: " . $row['title'] . "\n";
	echo "a: " . $row['author'] . "\n";
	echo "c: " . $row['call_lc'] . "\n";
	echo "i: " . $row['isbn'] . "\n";
	echo "l: " . $row['location_id'] . "\n\n";
    }

    exit;
}

print_header();
?>

<form action="export.php" method=post>
<font size="-1">
Use <kbd>&lt;control&gt;</kbd> or <kbd>&lt;shift&gt;</kbd> keys to select multiple locations:<br>
</font>
<select name=location multiple>
<?
    $sql = "SELECT n,city FROM location";
    $result = mysql_query( $sql );
    echo mysql_error();

    while ($row = mysql_fetch_row( $result ))
    {
	echo "<option value=$row[0]>$row[1]</option>\n";
    }
?>
</select>
<p>
<input type=submit value="Export">
</form>

<? print_footer(); ?>

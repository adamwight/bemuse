<?
include( "common.php" );

html_header();
?>
<table cellspacing=0 cellpadding=20 border=0 width="100%" height="100%">
<tr>
<td>
<a href="entry_i.php">Offer instruments</a><p>
<a href="index.php">Halfway Library</a>
</td>
<td>

<?
    if (!isset($where))
        $where = "";

    $sql = "SELECT n, type, brand, year FROM instrument $where ORDER BY type";
    $result = db_query( $sql );

    echo "<p>\n<font size=\"-1\" face=serif>\n";

    while ($row = mysql_fetch_row( $result ))
    {
	$type = htmlspecialchars( $row[1] );
	$instrument = "<u><a href=\"edit_i.php?i=$row[0]\">"
		    . "$type</a></u>";
	$brand = htmlspecialchars( $row[2] );
	$year = htmlspecialchars( $row[3] );

	echo "$instrument";

	if ("$year$brand")
	{
	    echo " (" . ("$year" ? "$year " : '') . "$brand)";
	}
	echo "<br>";
    }
?>

</td>
</tr>
</table>

</body>
</html>

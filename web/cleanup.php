<?
include( "common.php" );

print_header();

if ($_GET['a'] == 'isbn-all')
{
    echo "<pre>\n";
    system( "$exec_path --isbn-all > /dev/null 2>&1" );
    echo "</pre>\n";
}

if ($_GET['m'] == 'a') $where = '';
else $where = 'AND NOT informed';

?>

<p>
<?
    if ($_GET['m'] == 'a')
	echo '<a href="cleanup.php">Show incomplete records</a> ';
    else
	echo '<a href="cleanup.php?m=a">Show all books</a> ';
#<a href="cleanup.php?a=isbn-all">Lookup all ISBN's</a>
?>
<p>
<table cellspacing=2 cellpadding=0 border=0 bgcolor=black>
<tr><td>
<table cellspacing=0 cellpadding=3 border=1 bgcolor=white>
<tr><th>Title</th><th>Author</th><th>ISBN</th><th>Action</th></tr>

<?
$sql = "SELECT title, author, isbn, book.n "
	  . "FROM book, physical WHERE physical.book_id=book.n $where "
	  . "GROUP BY book.n ORDER BY title_sort, author";

$result = mysql_query( $sql );
echo mysql_error();

while ($row = mysql_fetch_row( $result ))
{
    $title = ($row[0] ? $row[0] : '&nbsp;');
    $author = ($row[1] ? $row[1] : '&nbsp;');
    echo "<tr><td>$title</td><td>$author</td><td>$row[2]</td>";
    echo "<td><a href=\"edit.php?a=e&b=$row[3]\">Edit</a>&nbsp;";
    echo "<a href=\"edit.php?a=d&b=$row[3]\">Remove</a></td></tr>\n";
}

?>

</table></td></tr></table>

<?

print_footer();
?>

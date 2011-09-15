<?
include("common.php");
include("classifications_all.php");
include_once("session.php");
include("book.php");
include("lookup.php");

// ?a=
//actions:
//      [*]	defaults to a form allowing the user to edit
//		    a book's bibliographic data
//	"s"	submit manually-entered changes to book data
//	"r"	replace book with one returned by the Lookup tool
//	"d"	delete a book from a library's collection
//	"di"	delete one of a book's associated images
//	Lookup  (a submit button) 

$view_type = get_session_value( 'v', 'b' );
// view types:
//      [*] defaults to a reasonable set of data
//	"." is suitable for inclusion in an extant page
//      "e" is the extended display with maximum info

$form_source = 'post';

if ($view_type != '.')
    print_header("Book Details");

$book_id = get_book_id();

if (!isset($book_id))
{
    echo "<h2>Don't do that.</h2>\n";
    print_footer();
    exit;
}

if (isset($_GET['a'])) {
    process_action( $_GET['a'] );
}

$book = get_book( $book_id );

?>
<table cellpadding=0 cellspacing=0 border=0>
<tr><td>

    <? print_book_mutable( $book ); ?>

</td></tr>
<?
if (isset($books)) {
    // we just ran --lookup --everywhere.  list alternative book cards
    print_lookup_results($books);
}

if ($book_id)
{
?>
<tr><td>
<?
    print_holdings( $book_id );

    echo "<p>\n";

    print_review( $book_id );
    print_attach_image( 'b', $book_id );

    print_images( $book_id );
?>
</td></tr>
<?
}
echo "</table>";

print_footer();

//
// function defs from here on
//

function process_action( $action )
{
    global $book_id, $exec_path, $books, $debug;

    if ($action == 'r')
    {
	// the book is being replaced by another lookup
	list ($new_id) = sscanf( $_GET['s'], "%d" );

	replace_book( $book_id, $new_id );

	$book_id = $new_id;
    }
    elseif ($action == 's' && ($book_id == $_POST['book_id']))
    {
	// the book was edited by hand.  update the database.
	submit_data( $book_id );
    }
    elseif ($action == 'di')
    {
	list ($image_id) = sscanf($_GET['i'], "%d");

	if (is_delete_verified())
	{
	    // delete an image

	    // the ids are simply negated for easy recovery
	    $sql = "UPDATE image SET book_id=-$book_id "
		     . "WHERE n=$image_id AND book_id=$book_id";
	    db_query( $sql );
	}
	else
	{
	    delete_book_image_verified($book_id, $image_id);

	    print_footer();
	    exit;
	}
    }

    if (isset($_POST['lookup']) || $action == 'l')
    {
	//
	// Lookup
	// checks all available library databases using every
	// combination of availible data (title, isbn, etc.)
	//
	// If the book hasn't changed, a second lookup is unfruitful.
	//
	// Bots defenses are needed; POST might not be a possibility.
	//

	// BACKGROUND THE FUCKER once query_id is gotten
	$cmd = "$exec_path --lookup --book-id $book_id --everywhere";
	if ($debug) error_log($cmd);
	$f = popen("$cmd", "r");

	$query_id = fgets($f);
	pclose($f);

	$books = load_query_results($query_id);
    }
}

function print_book_mutable( $book )
{
    global $view_type, $call_system, $call_dewey, $call_lc, $call_h;
?>
<div id=book class=writable>
<form action="edit.php?a=s&b=<?= $book['n'] ?>" method="post">
<input type=hidden name=book_id value=<?= $book['n'] ?>>

<table cellspacing=0 cellpadding=5 border=0>

<tr>
    <td align=right>title</td>
    <td colspan=3>
	<input type=text name=title size=70 value="<?= $book['title_full'] ?>">
    </td>
</tr>
<tr>
    <td align=right>author</td>
    <td colspan=3>
	<input type=text name=author size=50 value="<?= $book['author'] ?>">
    </td>
</tr>

<?
    if ($view_type == 'e')
    {
?>
<tr>
    <td align=right>responsible</td>
    <td colspan=3>
	<input type=text name=responsible size=50 value="<?= $book['responsible'] ?>">
    </td>
</tr>
<?
    }
// XXX candidate for normalization
?>

<tr>
    <td align=right>Library of Congress call #</td>
    <td>
	<input type=text name=call_lc value="<?= $book['call_lc'] ?>">
    </td>
    <td colspan=2>
	= <?= $call_lc->get_heading_verbose($book['call_lc'], "lc") ?>
    </td>
</tr>
<tr>
    <td align=right>Dewey call #</td>
    <td>
	<input type=text name=call_dewey value="<?= $book['call_dewey'] ?>">
    </td>
    <td colspan=2>
	= <?= $call_dewey->get_heading_verbose($book['call_dewey'], "dewey") ?>
    </td>
</tr>
<tr>
    <td align=right><i>your call</i></td>
    <td>
	<input type=text name=call_h value="<?= $book['call_h'] ?>">
    </td>
    <td colspan=2>
	= <?= $call_h->get_heading_verbose($book['call_h'], "h") /*add enables an edit boxfor new heading*/ ?>
    </td>
</tr>
<tr>
    <td align=right>LCCN</td>
    <td colspan=3>
	<input type=text name=lccn value="<?= $book['lccn'] ?>">
	ISBN
	<input type=text name=isbn value="<?= $book['isbn'] ?>">
    </td>
</tr>

<?
    if ($book)
    {
	print_subjects( $book );

	print_notes( $book );
    }

    if ($view_type == 'e')
    {
?>
<input type=hidden name=ve value=1>
<tr>
    <td align=right>Publisher</td>
    <td colspan=3><font size="-2">city</font>
	<input name=pub_place value="<?= $book['pub_place'] ?>"
	       type=text size=20>:
	<font size="-2">name</font>
	<input name=pub_name value="<?= $book['pub_name'] ?>"
	       type=text size=20>,
	<font size="-2">date</font>
	<input name=pub_dates value="<?= $book['pub_dates'] ?>"
	       type=text size=6>.
    </td>
</tr>
<?
    }
?>

<tr>
    <td>&nbsp;</td>
    <td align=right>
	<input type=submit name=save value=Save>
    </td>
    <td>
	<input type=submit name=lookup value=Lookup>
    </td>
    <td align=right>

<?
    if ($view_type == 'e')
	cmd_link("edit.php?b=" . $book['n'] . "&v=b", "Less");
    else
	cmd_link("edit.php?b=" . $book['n'] . "&v=e", "More");
?>

    </td>
</tr>

</table>
</form>
</div>
<?
}

function print_lookup_results($books)
{
    global $book_id;

    $n_results = count($books);
    if ($n_results == 0) return;

?>
<tr><td align=center>&nbsp;<p>
<h3><?= $n_results . ' result' . ($n_results == 1 ? '' : 's') ?> found.</h3>
</td></tr>
<tr><td>
<?

    //$sql = "SELECT * FROM book WHERE n IN ($ids) ORDER BY title_full, author";

    $printer = new book_printer();
    foreach ($books as $b)
    {
	if ($book_id == $b->get_id())
	{
	    $book_action = "[This record replaced your original]\n";
	}
	else
	{
	    $book_action = "<a href=\"edit.php?b=$book_id&a=r&s="
		.$b->get_id()."\">Use this record</a>\n";
	}

	$printer->print_index_card_static($b, $book_action);
    }
?>
</td></tr>
<?
}

function print_review( $book_id )
{
    // Share reviews with the book readers' department

    echo "<p>\n<a href=\"http://ludd.net/books/review.php?import=$book_id\">Dangerously Review the book</a>\n<p>\n";

    // the books db doesn't have an index into these book ids
    $sql = "SELECT review, reviewer FROM books.review WHERE library_id = $book_id";

    $result = db_query( $sql );

    while ($row = mysql_fetch_row( $result ))
    {
    echo "<h1>Reader Review</h1>\n";

	echo "<dd>$row[0] --<i>$row[1]</i><p>\n";
    }
}


function print_images( $book_id )
{
    $sql = "SELECT n, attr FROM image WHERE book_id=$book_id";
    $result = db_query( $sql );

    while ($row = mysql_fetch_row( $result ))
    {
	echo "<p><img src=\"image/$row[0]\" $row[1]><br>\n";
	echo "[<a href=\"edit.php?b=$book_id&a=di&i=$row[0]\">",
		"delete image",
	     "</a>]\n";
    }
}


function print_holdings( $book_id )
{
    //
    // Who has this book and where can I get it?
    //
?>
<div id=circulation>
<?
    $sql =
    "SELECT l.n, l.city, l.librarian, p.physical_id
	FROM physical p LEFT JOIN location l
			  ON p.location_id=l.n
	WHERE p.book_id=$book_id";
    $result = db_query( $sql );

    $nrows = mysql_num_rows( $result );
    echo "$nrows cop" . ($nrows == 1 ? "y" : "ies") . " available in<p>\n";

    echo "<div id=physical>\n";

    while ($row = mysql_fetch_row( $result ))
    {
	$n = $row[0];
	$city = htmlspecialchars($row[1]);
	$librarian = htmlspecialchars($row[2]);
	$physical = $row[3];
	?>
	    <a href="browse.php?l=<?=$city?>"><?=$city?></a>,
	    <a href="browse.php?l=<?=$n?>"><?=$librarian?>'s library</a>
	      [<a href="move.php?p=<?=$physical?>">Move book</a>,
	       <a href="enqueue.php?p=<?=$physical?>">Save book</a>]<br>
	<?
    }

    echo "</div>\n";

?>
</div>
<?
}


function replace_book( $book_id, $new_id )
{
    global $debug;

    //
    // the "lookup" tool displays a list of books with bibliographic
    // data.  each has a "replace" link that substitutes its own data
    // for the book the user was viewing.
    //

    $sql = "UPDATE physical SET book_id=$new_id WHERE book_id=$book_id";
    if ($debug) echo "$sql<br>\n";
    db_query( $sql );

    $sql = "UPDATE image SET book_id=$new_id WHERE book_id=$book_id";
    if ($debug) echo "$sql<br>\n";
    db_query( $sql );
}

function delete_book_verified( $book_id )
{
    //
    // This function displays a verification the first time,
    // and really deletes the book the second time through.
    //

    if (isset($_POST['delete']))
    {
	$sql = "DELETE FROM physical WHERE book_id=$book_id";
	db_query( $sql );
	echo "<h2>Deleted book.</h2>\n";
    }
    else
    {
?>
<h2>Verify delete</h2>
<u><?= $book['title_full'] ?></u> by <?= $book['author'] ?>
<form action="edit.php?a=d&b=<?= $book_id ?>" method=post>
<input name=delete value=Delete type=submit>
</form>
<?
    }
}

function submit_data( $book_id )
{
    global $debug, $exec_path;
    // Update the database with user-entered bibliographic information.

    $matches = array();

    if (preg_match( "/^(.+) *(\.[A-Z].*)$/",
		    $_POST['call_lc'],
		    $matches ))
    {
	$lc_class_number = mysql_real_escape_string($matches[1]);
	$lc_item_number = mysql_real_escape_string($matches[2]);
    }
    else
    {
	$lc_class_number = massage('call_lc');
    }

    // Crudely parse into title / subtitle;
    if (preg_match( "/^(.+?) *([:;] *)(.*)$/", $_POST['title'], $matches ))
    {
	$title = mysql_real_escape_string($matches[1]);
	$sep = mysql_real_escape_string($matches[2]);
	$subtitle = mysql_real_escape_string($matches[3]);
    }
    else
    {
	$title = massage('title');
	$sep = '';
	$subtitle = '';
    }

    $sql = "UPDATE book SET modified=1, title='$title"
	    . "', subtitle='$subtitle"
	    . "', title_full='$title$sep$subtitle"
	    . "', author='" . massage('author')
	    . "', lc_class_number='$lc_class_number"
	    . "', lc_item_number='$lc_item_number"
	    . "', call_h='" . massage('call_h')
	    . "', call_dewey='" . massage('call_dewey')
	    . "', call_lc='" . massage('call_lc')
	    . "', lccn='" . massage('lccn')
	    . "', isbn='" . massage('isbn');
    if ($debug) echo "$sql<br>\n";
    if (isset($_POST['ve']))
    {
	$sql .= "', responsible='" . massage('responsible')
	      . "', pub_place='" . massage('pub_place')
	      . "', pub_name='" . massage('pub_name')
	      . "', pub_dates='" . massage('pub_dates');
    }
    $sql .= "' WHERE n=$book_id";

    $result = db_query( $sql );

    exec( "$exec_path --standard $book_id" );
}

function get_book( $book_id )
{
    $sql = "SELECT * FROM book WHERE n=$book_id";
    $result = db_query( $sql );

    if ($row = mysql_fetch_assoc( $result ))
	$book = $row;

    return $book;
}

function print_subjects( $book )
{
    // gets and displays all MARC/LC subject data

    $sql = "SELECT * FROM subject, subject_link " .
		"WHERE subject_id=n AND book_id=" . $book['n'];
    $result = db_query( $sql );
    if ($n = mysql_num_rows( $result ))
    {
	echo "<tr><td rowspan=$n valign=center>Subject</td>\n";
	$first_row = 1;
    }

    while ($row = mysql_fetch_assoc( $result ))
    {
	$fields = array( 'topic', 'subordinate', 'location', 'date',
			 'title', 'affiliation', 'geographic',
			 'chronological', 'general', 'form' );
	$subject = array();

	foreach ($fields as $f) if ($row[$f]) $subject[] = $row[$f];

	if (!$first_row) echo "<tr>";
	$first_row = 0;

	echo "<td colspan=3>", join( ' -- ', $subject ), "</td></tr>\n";
    }
}

function print_notes( $book )
{
    $sql = "SELECT * FROM note_link LEFT JOIN note ON note.n=note_id " .
		"WHERE book_id=" . $book['n'];
    $result = db_query( $sql );
    while ($row = mysql_fetch_assoc( $result ))
    {
	echo "<tr><td>Notes</td><td colspan=3><font size=\"-1\">",
	    $row['contents'], "</font></td></tr>\n";
    }
}
?>

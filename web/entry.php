<?
include( "common.php" );
include( "session.php" );
include( "book.php" );
include( "lookup.php" );

$debug = 0;

$must_continue = 0;
$added = array();

print_header("Book Entry");
?>

<h1>Offer books</h1>
<form action="entry.php" method="post" name=f>
<input type=hidden name=continue value=1>
<?

if (!get_session_location())
{
?>
    <div id=error>Location Unspecified</div><br>
    Before you offer books, explain where they are and how to find you:<br>
    <a href="location.php?r=entry">set location</a>
    <p>
<?
    print_footer();
    return;
}
?>

<font size="+1">Location:</font> <?=$librarian_h?>'s books in <?=$city_h?>
    (<a href="location.php?r=entry">change librarian</a>)
<br>
View <a href="browse.php">your library</a>.
<p>

<?
$have_data = isset($_POST['continue']);

if ($have_data)
{
    process_input_data($_POST);

    print_additions();

    if ($rejects)
    {
	?><div id=error>If you entered the information accurately and the
	   book wasn't found, your best bet is to enter it by title.</div><?
    }

    if ($must_continue)
    {
	?><div id=error>Your books matched more than one entry. Please
	select the right one from this list.</div><?
    }
}

if (!$must_continue)
{
    if (!$have_data) print_help();

    echo "<p>\n";
    print_entry_fields();
}
?>
<br>
<input type=submit value="continue">
</form>

<?
print_footer();


// TODO move to lookup.php and take POST args as a hash
function process_input_data($args)
{
    global $debug, $exec_path, $location,
	   $rejects, $base_cmd;

    echo "<blockquote>\n";

    $base_cmd = $exec_path;

    foreach ($args as $key => $value)
    {
	//XXX we could use the query_id
	if (strstr( $key, "ISBN-" )) add_book( $value );
	if (strstr( $key, "LCCN-" )) add_book( $value );
	if (strstr( $key, "author-" ))
	{
	    list ($id) = sscanf( substr( $key, 7 ), "%d" );
	    set_book_author( $id, $value );
	    add_book( $id );
	}
    }

    if (isset($args['isbn']))
    {
	$isbn = trim( $args['isbn'] );
	$isbn = str_replace( '-', '', $isbn );
	$isbn = str_replace( ' ', '', $isbn );

	$matches = array();
	if (preg_match( '/^([0-9]{9}[0-9X])$/', $isbn, $matches ))
	{
	    add_by('isbn', $matches[1], 'ISBN');
	}
	else
	{
	    // app should do this
	    echo "Malformed ISBN: $isbn<br>\n";
	    $rejects[] = $isbn;
	}
    }
    elseif (isset($args['lccn']))
    {
	$lccn = trim( $args['lccn'] );
	$lccn = str_replace( ' ', '', $lccn );
	add_by('lccn', $lccn, 'LCCN');
    }
    elseif (isset($args['title']))
    {
	add_by_t_a( $args['title'], $args['author'] );
    }

    echo "</blockquote>\n";
}

function run_server($args)
{
    global $base_cmd, $debug, $out, $ret, $books;

    $cmd = "$base_cmd $args";
    if ($debug)
	echo "[$cmd]<br>\n";

    $out = array();
    exec( $cmd, $out, $ret );

    if ($debug)
	print_r($out);

    // one book on the next line.
    if ($out[0] == '1')
    {
	$books = array(new book($out[1]));
    }
    // this is a query id
    else
    {
	$books = load_query_results($out[0]);
    }
}

function add_by($optname, $arg, $longname)
{
    global $ret, $rejects;

    run_server("--$optname $arg", $longname);
    print_book_results( $longname, $arg );
    if ($ret)
	$rejects[] = $arg;
}

function add_by_t_a( $title, $author )
{
    global $ret, $rejects;

    $t_arg = escapeshellarg( $title );
    $a_arg = escapeshellarg( $author );
    run_server("--title $t_arg --author $a_arg --add");
    if ($ret)
	$rejects[] = $title; #XXX what about author?

    print_book_results( 'title and author', "$title/$author" );
}

function print_book_results( $type, $arg )
{
    global $debug, $location, $call_column,
	$must_continue, $rejects_err, $books;

    if (count($books) < 1)
    {
	echo "Nothing found for $type, $arg.<br>\n";
	return;
    }

    setup_call_vars();

    $n_results = count($books);

    if ($n_results == 1)
    {
	$book = $books[0];
	$book_h = "<u><a href=\"edit.php?b=".$book->get_id().'">'.$book->get_title()."</a></u> by ".$book->get_any_author(); // 'by' is often redundant with "responsible"

	if (0) // TODO revisit duplicate checks
	{
	    echo "<input type=checkbox name=\"$type-$arg\" value=$row[0]>",
		 "Allow duplicate of $book_h?<br>\n";
	}
	else
	{
	    add_book($book->get_id());
	}
    }
    elseif ($n_results > 1)
    {
	echo "<dl><dt>Multiple results for $type $arg:<br>\n<dd>";

	foreach ($books as $b)
	{
	    echo "<input type=radio name=\"$type-$arg\" value=".$b->get_id().">";
	    echo "<u>".$b->get_title()."</u> by ".$b->get_any_author()."<br>\n";
	}

	echo "<input type=radio name=\"$type-$arg\" value=0>",
	     "None of the above</dl>\n";

	$must_continue = 1;
    }
}

function add_book( $id )
{
    global $debug, $location, $added;

    if (!$id) return;

    $sql = "INSERT INTO physical SET book_id=?, location_id=?, home_location=?";
    db_query( $sql, $id, $location, $location );

    $added[] = $id;
}

function print_additions()
{
    global $added;

    $books = load_book_array($added);

    echo "<div id=\"added\">\n";
    foreach ($books as $b) {
	echo "Added <u>".$b->get_title()."</u>";

	$shelf = $b->get_shelf();
	if ($shelf)
	{
	    echo ", on the $shelf shelf.";
	}
	else
	{
	    echo " [<a href=\"edit.php?b=".$book->get_id()
		."&a=l\">Lookup</a> missing details]";
	}
	echo "<br>\n";
    }
    echo "</div>\n";
}

// but, it should look the book up as well, not failing however
function set_book_author( $id, $author )
{
    global $debug;

    $sql = "UPDATE book SET author=? WHERE n=?";
    db_query( $sql, $author, $id );
}

function print_help()
{
   ?> 
<div id=help>
<p>
Fill out <i>one</i> of the sets of information about each book, either:
<ol>
    <li>The 10-digit ISBN--not the barcode, but it can be found nearby
    <li>For older US books, the Library of Congress Catalog Card number, including any hyphens
    <li>The title and author
</ol>
</div>


Go to <a href="batch.php">batch entry</a>.
<?/*
    <li>A Library of Congress Catalog Card Number, hyphen mandatory
    <li>(<i>not yet</i>) A Library of Congress call number
    <li>(<i>not yet</i>) If you happen to have a barcode reader, do your magic.
*/?>
<?
}

function print_entry_fields()
{
?>
<table border=0 cellspacing=0 cellpadding=3>
    <tr><td bgcolor="#ff6666">
	ISBN: <input type=text width=10 name=isbn>
    </td>
    <td><input type=submit value=continue></td>
    </tr>
    <tr><td align=center colspan=2>
	<i>or</i>
    </td></tr>
    <tr><td bgcolor="#ffff66">
	Library of Congress #: <input type=text width=10 name=lccn>
    </td>
    <td><input type=submit value=continue></td>
    </tr>
    <tr><td align=center colspan=2>
	<i>or</i>
    </td></tr>
    <tr><td bgcolor="#66ff66" colspan=2>
	Title: <input type=text width=100 name=title>
    <p>
	by Author: <input type=text width=80 name=author>
    </td></tr>
</table>
<?
}

?>

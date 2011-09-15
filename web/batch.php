<?
include( "common.php" );
include( "session.php" );

$debug = 0;

print_header("Batch Book Entry");

?>
<form action="batch.php" method="post" name=f>
<input type=hidden name=continue value=1>
<?

if (!get_session_location())
{
?>
    <div id=error>Location Unspecified</div><br>
    Before you offer a batch of books, explain where they are:
    <a href="location.php?r=batch">set location</a>
    <p>
<?
    print_footer();
    return;
}

if (isset($_POST['continue']))
    $have_data = 1;
if ($_POST['t'] == 'i')
    $instruments = 1;

echo "<div id=report_location>\n";
echo "<font size=\"+1\">Location:</font> ";

if (get_session_location())
{
    echo "$librarian_h's books in $city_h";
}
else
{
    echo "<font color=red>Unspecified</font>";
}
?>

<br>
<a href="location.php?r=batch"><?=($location ? 'change' : 'set')?> location</a>

<?
if ($have_data && $location)
{
    process_titles();

    process_input_data();

    if (count($title_ids) > 0)
    {
	print_by_title_continuations( $title_ids );
    }

    if ($rejects)
    {
	echo "<blockquote>Some entries were either rejected as invalid ISBN's "
	   . "or couldn't be found online.  Your best bet is to enter the "
	   . "book by title.</blockquote>";
	echo $failures;
    }

    if ($used_title)
    {
	echo '<p><blockquote>Since you entered some books by title, to finish the job '
       . 'you\'ll need to go to the <a href="cleanup.php">cleanup</a> page.</blockquote>';
    }

    if ($must_continue)
    {
	echo '<p>Some of your books matched more than one entry, please ',
	     'select the real one from the list.';
    }

    echo "<p>You may continue entering data here...\n";

    echo "<p><textarea name=data rows=20 cols=40>\n";

    if ($rejects)
    {
	foreach ($rejects as $r) echo stripslashes( $r ) . "\n";
    }
    
    echo "</textarea>\n";
}
elseif (isset($location)) // and if there was no POST data
{

    print_help();

    ?>
<p>

<textarea name=data rows=20 cols=60>
<?= htmlspecialchars( $_POST['data'] ) ?>
</textarea>
<?
}

if ($location)
{
  echo "<br>\n";
  echo "<input type=submit value=\"continue\">\n";
}

?>
</form>

<?
print_footer();


function process_titles()
{
    global $debug,
	   $title_ids, $used_title;

    $title_ids = array();
    if ($_POST['titles'])
    {
	$titles = split( ',', $_POST['titles'] );

	foreach ($titles as $book_id)
	{
	    $author = trim( $_POST["author-$book_id"] );

	    if (!$author) 
	    {
		$title_ids[] = $book_id;
		continue;
	    }

	    $sql = "UPDATE book SET author='$author' WHERE n=$book_id";
	    db_query( $sql );

	    echo "recorded author `$author' ",
		 "<a target=\"_blank\" href=\"edit.php?v=.&b=$book_id\">",
		    "Edit",
		 "</a><br>\n";
	}

	$used_title = 1;
    }
}

function process_input_data()
{
    global $debug, $exec_path, $location,
	   $out, $rejects, $title_ids, $used_title, $base_cmd;

    echo "<blockquote>\n";

    foreach ($_POST as $key => $value)
    {
	if (strstr( $key, "ISBN-" )) add_book( $value );
	elseif (strstr( $key, "LCCN-" )) add_book( $value );
	elseif (strstr( $key, "bar-" )) add_book( $value );
	elseif (strstr( $key, "author-" ))
	{
	    list ($id) = sscanf( substr( $key, 7 ), "%d" );
	    set_book_author( $id, $value );
	    add_book( $id );
	}
    }

    $entries = split( "\n", $_POST['data'] );
    $rejects = array();

    $base_cmd = "$exec_path --location $location";

    foreach ($entries as $e)
    {
	$e = trim( $e );

	if ($debug) echo "``$e''\n";
	unset( $out );

	// isbn or lccn, possibly w/ hyphens and spaces
	if (preg_match( '/^[ 0-9X-]+$/', $e ))
	{
	    $i = str_replace( '-', '', $e );
	    $i = str_replace( ' ', '', $i );

	    $matches = array();
	    if (preg_match( '/([0-9]{9}[0-9X])/', $i, $matches ))
	    {
		add_by_isbn( $matches[1] );
	    }
	    elseif (count( $i ) < 9)
	    {
		add_by_lccn( $e );
	    }
	    else // bad ISBN
	    {
		echo "malformed ISBN: $e<br>\n";
		$rejects[] = $e;
	    }
	}
	elseif (preg_match( '/^\..+\.$/', $e ))
	{
	    add_by_barcode($e);
	}
	elseif ($e) // title
	{
	    add_by_title( $e );
	}
    }

    echo "</blockquote>\n";
}

function add_by_isbn( $isbn )
{
    global $debug, $base_cmd,  $out, $rejects;

    $out = array();
    $cmd = "$base_cmd --isbn $isbn";

    if ($debug) echo "[$cmd]<br>\n";
    exec( $cmd, $out, $ret );
    if ($ret) $rejects[] = $isbn;

    print_book_results( $out, 'ISBN', $isbn );
}

function add_by_barcode( $barcode )
{
    global $debug, $base_cmd,  $out, $rejects;

    $out = array();
    $cmd = "$base_cmd --barcode $barcode";

    if ($debug) echo "[$cmd]<br>\n";
    exec( $cmd, $out, $ret );
    if ($ret) $rejects[] = $isbn;

    print_book_results( $out, 'bar', $isbn );
}

function add_by_lccn( $lccn )
{
    global $debug, $base_cmd,  $out, $rejects;

    $out = array();
    $cmd = "$base_cmd --lccn $lccn";

    if ($debug) echo "[$cmd]<br>\n";
    exec( $cmd, $out, $ret );
    if ($ret) $rejects[] = $lccn;

    print_book_results( $out, 'LCCN', $e );
}

function add_by_title( $title )
{
    global $debug, $base_cmd,  $out, $title_ids, $used_title;

    $arg = escapeshellarg( $title );
    $cmd = "$base_cmd --title $arg";
    if ($debug) echo "[$cmd]<br>\n";
    exec( $cmd, $out );
    echo "<u>$title</u> by <input name=author-$out[2] type=text size=50>\n";
    $used_title = 1;
}

function print_book_results( $out, $type, $arg )
{
    global $debug, $location,  $must_continue, $rejects_err;

    list ($n_results) = sscanf( $out[0], "%d" );

    if ($n_results < 1)
    {
	echo "Nothing found for $type $arg.<br>\n";
	return;
    }

    $sql = "SELECT b.n, b.title, b.author, p.physical_id " .
	        "FROM book b LEFT JOIN physical p ON " .
		"(p.book_id=b.n and p.location_id=$location) " .
		"WHERE b.n IN (" . $out[1] . ") GROUP BY b.n";

    $result = db_query( $sql );

    if ($n_results == 1)
    {
	$row = mysql_fetch_row( $result );
	$book_h = "<u>$row[1]</u> by $row[2]";

	if ($row[3])
	{
	    echo "<input type=checkbox name=\"$type-$arg\" value=$row[0]>",
		 "Allow duplicate of $book_h?<br>\n";
	}
	else
	{
	    add_book( $row[0] );
	    echo "Added $book_h<br>\n";
	}
    }
    elseif ($n_results > 1)
    {
	echo "<dl><dt>Multiple results for $type $arg:<br>\n<dd>";

	while ($row = mysql_fetch_row( $result ))
	{
	    echo "<input type=radio name=\"$type-$arg\" value=$row[0]>";
	    echo "<u>$row[1]</u> by $row[2]<br>";
	}

	echo "<input type=radio name=\"$type-$arg\" value=0>",
	     "None of the above</dl>\n";

	$must_continue = 1;
    }
}

function add_book( $id )
{
    global $location, $debug;

    if ($debug)
	error_log("adding book $id<br>\n");
    if (!$id)
    {
	error_log("no id passed to add_book!");
	return;
    }

    $sql = "INSERT INTO physical SET book_id=$id, location_id=$location, home_location=$location";
    db_query( $sql );
}

// but, it should look the book up as well, not failing however
function set_book_author( $id, $author )
{
    global $debug;

    $sql = "UPDATE book SET author='$author' WHERE n='$id'";
    db_query( $sql );
}

function print_help()
{
   ?> 
<div id=help>
Enter books one per line, in any of the following formats:
<ol>
    <li>The 10-digit ISBN--not the barcode, but it can be found nearby
    <li>The title, in which case much of the effort is on your ass
    <li>A Library of Congress Catalog Card Number, hyphen mandatory
    <li>Raw barcode reader
    <li>(<i>one day A Library of Congress call number</i>)
</ol>

<br>

You're <font color=red>warned</font> and advised to enter only a small number
of books the first few times you use this system, after copying to the
clipboard.  Relax, check the results, and repeat twice in the mornings.
</div>
    <?
}
?>

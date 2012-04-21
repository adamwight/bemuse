<?
$style = '.title { font-family: sans-serif; font-variant: small-caps }';

include( "common.php" );
include( "session.php" );

$action = $_GET['a'];
$view = get_session_value( 'v', 'b' );

if ($view != '.')
    print_header();

if (isset($_GET['i']))
{
    list ($instrument_id) = sscanf( $_GET['i'], "%d" );

    if ($action == 's' && ($instrument_id == $_POST['instrument_id']))
    {
	submit_data( $instrument_id );
    }
    elseif ($action == 'di')
    {
	list ($image_id) = sscanf( $_GET['img'], "%d" );

	$sql = "UPDATE image SET instrument_id=? WHERE n=? AND instrument_id=?";
	db_query( $sql, 0-$instrument_id, $image_id, $instrument_id );
    }

    $instrument = get_instrument( $instrument_id );
}

if ($action == 'd')
{
    if ($_POST['submit'] == 'Delete')
    {
	$sql = "DELETE FROM instrument WHERE n=?";
	db_query( $sql, $instrument_id );
	echo "<h2>Deleted instrument.</h2>\n";
    }
    else
    {
?>
<h2>Verify delete</h2>
<u><?= $instrument['type'] ?></u>
<form action="edit_i.php?a=d&i=<?= $instrument_id ?>" method=post>
<input name=submit value=Delete type=submit>
</form>
<?
    }

    print_footer();
    exit;
}
?>

<table cellpadding=0 cellspacing=0 border=0>
<tr><td>

    <? print_instrument_edit( $instrument ); ?>

</td></tr>
<tr><td>

<?
if ($instrument_id)
{
    ?> <p> <?

    print_images( $instrument_id );
    print_attach_image( 'i', $instrument_id );
}

?>
</td>
</table>
<?

print_footer();

function print_instrument_edit( $instrument )
{
?>
<form action="edit_i.php?a=s&i=<?= $instrument['n'] ?>" method="post">
<input type=hidden name=instrument_id value=<?= $instrument['n'] ?>>

<h2><?=$instrument['type']?></h2>
<table cellspacing=0 cellpadding=2 border=0 bgcolor=black width="100%">
<tr><td>
<table cellspacing=0 cellpadding=3 border=0 bgcolor=white width="100%">
<tr><td>
<span class=title>Circulation</span>
<hr size=1>
<? print_holdings($instrument['n']); ?>
<blockquote>
<input type=checkbox name=reference <?=($instrument['reference']?'checked':'')?>>Reference use only<br>
</blockquote>
</td></tr>
</table>
</td></tr>
</table>

<p>
<table cellspacing=0 cellpadding=2 border=0 bgcolor=black width="100%">
<tr><td>
<table cellspacing=0 cellpadding=3 border=0 bgcolor=white>
<tr><td>
<span class=title>Description</span> (Optional)
<hr size=1>
<p>
Year: <input type=text size=10 name=year value="<?= $instrument['year'] ?>"><br>
Manufacturer: <input type=text size=20 name=brand value="<?= $instrument['brand'] ?>"><br>
History, comments, etc.<br>
<textarea name=comment rows=15 cols=60>
<?= $instrument['comment'] ?>
</textarea>
</td></tr></table>
</td></tr></table>

<tr>
    <td align=right>
	<input name=submit type=submit value=Save>
    </td>
</tr>

</table>
</td></tr></table>
</form>
<?
}

function print_images( $instrument_id )
{
    $sql = "SELECT n, attr FROM image WHERE instrument_id=?";
    $result = db_query($sql, $instrument_id);

    while ($row = $result->fetch())
    {
	echo "<p><img src=\"image/$row[0]\" $row[1]><br>\n";
	echo "[<a href=\"edit_i.php?i=$instrument_id&a=di&img=$row[0]\">",
		"delete image",
	     "</a>]\n";
    }
}

function submit_data( $instrument_id )
{
    global $form_source;
    $form_source = 'post';

    $sql = "UPDATE instrument SET"
	      . " year=?"
	    . "', brand=?"
	    . "', comment=?"
	    . "', reference=?"
	    . " WHERE n=?";

    $result = db_query($sql,
        massage('year'),
        massage('brand'),
        massage('comment'),
        $_POST['reference'] == 'on',
        $instrument_id
    );
}

function get_instrument( $instrument_id )
{
    $sql = "SELECT * FROM instrument WHERE n=?";

    $result = db_query( $sql, $instrument_id );

    if ($row = $result->fetch(PDO::FETCH_ASSOC))
    {
        return $row;
    }
}

function print_holdings( $instrument_id )
{
    $sql =
    "SELECT location.n, location.city, location.librarian
	FROM instrument LEFT JOIN location
			  ON instrument.location_id=location.n
	WHERE instrument.n=?";
    $result = db_query( $sql, $instrument_id );

    while ($row = $result->fetch())
    {
	echo "<a href=\"browse.php?l=$row[0]\">",
		htmlspecialchars( $row[1] ),
		" (", htmlspecialchars( $row[2] ), ")</a><br>\n";
    }
}
?>

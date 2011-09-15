<?
include( "common.php" );
include( "session.php" );

$debug = 0;
print_header("Instrument Entry");
?>

<form action="entry_i.php" method=post name=f>
<input type=hidden name=continue value=1>
<?

if (!get_session_location())
{
    echo "You have to <a href=\"location.php?r=entry_i\">specify</a> " .
	 "a location to offer your instruments.";
    print_footer();
    return;
}
?>

<h2>Offer Instruments</h2>
<font size="+1">Location:</font>
    <?=$librarian_h?>'s instruments in <?=$city_h?>
    (<a href="location.php?r=entry_i">change location</a>)

<?
if (!process_input_data()) /*print_help()*/ ;
?>

<p>

Type of instrument: <input type=text size=20 name=type>
<input type=submit value="list it">
<blockquote>
<input type=checkbox name=reference>Reference use only<br>
</blockquote>
<p>
<table cellspacing=0 cellpadding=2 border=0 bgcolor=black><tr><td>
<table cellspacing=0 cellpadding=3 border=0 bgcolor=white>
<tr><td>
(Optional)
<p>
Year: <input type=text size=10 name=year><br>
Manufacturer: <input type=text size=20 name=brand><br>
History, comments, etc.<br>
<textarea name=comment rows=15 cols=60>
<?= htmlspecialchars( $_POST['extra'] ) ?>
</textarea>
</td></tr></table>
</td></tr></table>
<br>
<input type=submit value="list it">
</form>

<?
print_footer();


function process_input_data()
{
    global $debug, $location;

    if (!$_POST['type']) return;

    ?><blockquote>
    <?

    $type      = trim( $_POST['type'] );
    $reference = ($_POST['reference'] == 'on') ? 1 : 0;
    $year      = trim( $_POST['year'] );
    $brand     = trim( $_POST['brand'] );
    $comment   = trim( $_POST['comment'] );

    $sql = "INSERT INTO instrument VALUES " .
		"(null, $location, $location, $reference, null, " .
		"'$type', '$brand', '$year', '$comment')";
    if (db_query( $sql ))
    {
	$id = mysql_insert_id();
	?>Listed a <a href="edit_i.php?i=<?=$id?>"><?=$type?></a>.<?
    }
    else
    {
	echo "Failed and who knows why";
    }

    ?></blockquote>
    <?
}

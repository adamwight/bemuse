<?
include( "common.php" );
include( "session.php" );

$debug = 0;
$edit = 0;
$specific_locations_only = 1;
$form_source = 'post';

if (isset($_POST['continue']) // any post data at all
    && isset($_POST['create'])) // create button was pressed
    parse_location();

$edit = isset($_POST['edit']);		    // edit button was pressed
if (isset($_POST['save'])) parse_edit();    // save edits
if ($_GET['t'] == 'i') $suffix = '_i';	    // type = instruments

print_header("Location Settings");
?>

<form action="edit_location.php?<?= $_SERVER["QUERY_STRING"] ?>"
      method="post" name=f>
<input type=hidden name=continue value=1>

<table cellspacing=0 cellpadding=3 border=0 bgcolor=white>

<?
if (get_session_location())
{
    ?>
    <tr><td align=center><font size="+1">Current Location:</font>
	<?=$librarian_h?>'s library in <?=$city_h?>
	<input type=submit value=Edit name=edit>
    </td></tr>
    <?
}
?>

<tr>
    <td align=center>
	<input type=submit value=Create name=create>
	a new location
    </td>
</tr>
<tr>
<td>

<table cellspacing=1 cellpadding=0 border=0 width="100%" height="100%">
<tr>
    <td>City:</td>
    <td><input type=text name=city size=60 value="<?= $city_n ?>"></td>
</tr>
<tr>
    <td>Librarian:</td>
    <td><input type=text name=librarian size=40 value="<?= $librarian_n ?>">
    </td>
</tr>
<tr>
    <td>Contact:</td>
    <td><input type=text name=contact size=40 value="<?= $contact_n ?>"></td>
</tr>
</table>
<tr><td align=center><b>or</b></td></tr>
<tr>
    <td align=center>
	<? print_use_form(); ?>
    </td>
</tr>
</table>
<?
    if ($edit)
    {
	?> <p> <font size="+1">Edit Location</font> <?
	print_edit_form();
    }
?>
</td></tr></table>

</form>

<?
print_footer();


function parse_location()
{
    global $location, $city, $librarian, $city_n, $librarian_n, $contact_n;

    $location_id = parse_query_location($_POST['l']);

    if (!isset($city)) $city      = massage('city');
    $librarian = massage('librarian');
    $contact   = massage('contact');

    $some_manual_data = $city || $librarian || $contact;
    $manual_data = $city && $librarian && $contact;

    if ($location_id && isset($_POST['use']))
    {
	$_SESSION['l'] = $location_id;

	get_session_location();
    }
    elseif ($manual_data)
    {
	$sql = "INSERT INTO location SET city      = '$city', "
				      . "librarian = '$librarian', "
				      . "contact   = '$contact'";
	mysql_query( $sql );
	echo mysql_error();

	$location_id = mysql_insert_id();
	$_SESSION['l'] = $location_id;
    }
    else
    {
	?>
	<i>Either fill in some details about your library
	    or choose it from the list.</i>
	<hr>
	<?
    }

    $city_n	 = stripslashes( $city );
    $librarian_n = stripslashes( $librarian );
    $contact_n   = stripslashes( $contact );
}

//XXX share func
function parse_query_location($qvar)
{
    global $location_id, $city;

    if (isset($qvar))
    {
	list ($location_id) = sscanf( $qvar, '%d' );
	if (!$location_id) $city = $qvar;
    }
}

function print_create_form()
{
}

function print_use_form()
{
    ?><input type=submit name=use value="Use"> an existing location: <?

    print_locations_options();
}

function print_edit_form()
{
    $type = get_session_value('ct','lc');
?>
    <table>
    <tr><td>
    Preferred call no. system:
    <select name=call>
	<option value=lc<?=$type == 'lc' ? ' selected' : ''?>>
	Library of Congress</option>
	<option value=dewey<?=$type == 'dewey' ? ' selected' : ''?>>
	Dewey Decimal</option> 
    </select>
    </td></tr>
    <tr><td>
    <input type=submit name=save value="Save">
    </td></tr>
    </table>
<?
}

function parse_edit()
{
    set_session_value('ct', $_POST['call']);
}
?>

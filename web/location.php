<?
include( "common.php" );
include( "session.php" );

$debug = 0;
$specific_locations_only = 1;
$form_source = 'post';

if (isset($_POST['continue'])) // any post data at all
    parse_location();

if (isset($location))
    header( "Location: " . $_GET['r'] . ".php" );
print_header("Location Settings");

?>
<div id=set_location>
<form action="location.php?<?= $_SERVER["QUERY_STRING"] ?>"
      method="post" name=f>
<input type=hidden name=continue value=1>

<?
if (get_session_location())
{
    ?><tr><td align=center><font size=\"+1\">Current Location:</font>
	<?=$librarian_h?>'s library in <?=$city_h?>
	</td></tr>
    <?
}
#function print_where_if();

function print_errors()
{
    global $error;

    if ($error)
	echo "$error\n";
}
?>

<p>
<input type=submit name=use value=Use> an existing location: 
<? print_locations_options(); ?>
<hr>

<b>or</b>
<p>

<? print_errors(); ?>
<table cellspacing=1 cellpadding=0 border=0>
<tr>
    <td>Librarian:</td>
    <td><input type=text name=librarian size=40 value="<?= $librarian_h ?>">
    </td>
</tr>
<tr>
    <td>City:</td>
    <td><input type=text name=city size=60 value="<?= $city_h ?>"></td>
</tr>
<tr>
    <td>Contact:</td>
    <td><input type=text name=contact size=40 value="<?= $contact_h ?>"></td>
</tr>
</table>

<input type=submit name=create value=Create>
a new location
</div>

<br>
</form>

<?
print_footer();

function location_sync_session()
{
//...
    if (isset($_POST['l']))
    {
	list ($location_id) = get_digit('l');
	if (!$location_id)
	    $city = $_POST['l'];
    }
    if ($location_id && isset($_POST['use']))
    {
       // look up the location row
       $_SESSION['l'] = $location_id;

       get_session_location();
    }
}

function parse_location($ref = null)
{
    global $location, $city, $librarian, $city_h, $librarian_h, $contact_h,
	$error;

    $city      = massage('city');
    $librarian = massage('librarian');
    $contact   = massage('contact');

    $some_manual_data = $city || $librarian || $contact;
    $full_manual_data = $city && $librarian && $contact;

    if ($full_manual_data)
    {
	$sql = "INSERT INTO location SET city      = ?, "
				      . "librarian = ?, "
				      . "contact   = ?";
	db_query( $sql, $city, $librarian, $contact );

	$location_id = PDO::lastInsertId();
	$location = $location_id;
    }
    else
    {
	$error = "<div id=error>Either fill in some details about your library or choose it from the list.</div>";
    }

    $city_h	 = htmlspecialchars( $city );
    $librarian_h = htmlspecialchars( $librarian );
    $contact_h   = htmlspecialchars( $contact );
}
?>

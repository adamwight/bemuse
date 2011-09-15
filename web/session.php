<?
phpinfo();
if (!isset($GLOBALS['_SESSION']))
    session_start();

// get the variable from anywhere and save it to SESSION
function get_session_value($key, $default)
{
    if (isset($_GET[$key]))
	$value = stripslashes( html_entity_decode( $_GET[$key] ) );
    elseif (isset($_POST[$key]))
	$value = stripslashes( html_entity_decode( $_POST[$key] ) );
    elseif (isset($_SESSION[$key]))
	$value = $_SESSION[$key];
    else
	$value = $default;

    set_session_value($key, $value);

    return $value;
}

function set_session_value($key, $value)
{
    $_SESSION[$key] = $value;
}

// accepts city names and -1; data from G,P or session
function get_any_location()
{
    global $location, $location_id;

    $loc = get_session_value('l', null);
    if (!get_session_location() // not an id
	&& isset($loc))
    {
	$location = $loc;
    }
    else
    {
	list ($location_id) = sscanf($location, "%d");
    }
}

function get_session_location()
{
    global $location,
	   $city, $librarian, $contact, $description, $show_help,
	   $city_h, $librarian_h, $contact_h, $description_h;

    if (isset($_SESSION['l']))
	list ($location) = sscanf( $_SESSION['l'], '%d' );

    if (!isset($location) || !($location > 0))
    {
	unset($location);
	return 0;
    }

    $sql = "SELECT city, librarian, contact, description, show_help "
		. "FROM location WHERE n=$location";

    $result = db_query( $sql );

    if ($row = mysql_fetch_row( $result ))
    {
	$city        = $row[0];
	$librarian   = $row[1];
	$contact     = $row[2];
	$description = $row[3];
	$show_help   = $row[4];
	$city_h	       = htmlspecialchars( $city );
	$librarian_h   = htmlspecialchars( $librarian );
	$contact_h     = htmlspecialchars( $contact );
	$description_h = htmlspecialchars( $description );
    }
    else
    {
	// But the location wasn't found!
	unset($location);
	return 0;
    }

    return 1;
}

// deprecated
function get_call_type( )
{
    global $call_type;

    setup_call_vars();

    return $call_type;
}

function setup_call_vars()
{
    global $call_type, $call_column;

    $call_type = get_session_value( 'ct', 'lc' );
    // this_system.type = 

    if (!preg_match( '/^[a-z]+$/', $call_type ))
	    $call_type = 'lc';

    $call_column = "call_$call_type";

}

function setup_classifications()
{
    global $call_type, $call_system; // interesting... needed to set it
    include_once("classifications_$call_type.php");
}

function get_book_id()
{
    //global $book_id;

    if (isset($_GET['b']))
	list ($book_id) = sscanf($_GET['b'], "%d");

    return $book_id;
}
?>

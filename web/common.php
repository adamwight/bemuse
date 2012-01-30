<?
mysql_connect( "localhost", "halfway", "barz on q" );
mysql_select_db( "halfway" );
mysql_set_charset("utf8");

$exec_path = '/www/library/prog';
$specific_locations_only = 0;

$max_favorites = 10;

global $debug;
if (!isset( $debug )) $debug = 0;

if ($debug) error_reporting(E_ALL);

 
function print_header($page_title = "")
{
    html_header($page_title);

    print_menu();
}

function print_footer()
{
    tail_nav();
    html_footer();
}

function tail_nav()
{
    global $page_commands;

    if (!isset($page_commands))
	return;

    foreach ($page_commands as $i)
    {
	echo "<a href=\"" . $i["url"] . "\">" . $i["abbrev"] . "</a>";
    }
}

function html_header($page_title = "")
{
    global $style;
    $style_h = '<style type="text/css" media="all">' .
	'@import "base.css";' . $style . '</style>' .
	'<link rel="stylesheet" type="text/css" media="print" href="print.css" />';

    if ($page_title)
	$title_h = ": $page_title";
    else
        $title_h = "";

?>
<html>
<head>
<title>The Halfway Library<?=$title_h?></title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?=$style_h?>
</head>

<body bgcolor=white text=black>
<?
}

function html_footer()
{
?>
</body>

</html>
<?
}

function print_locations_options( )
{
    global $debug, $location, $specific_locations_only;

    echo "<select name=l>\n";

    if ($debug) echo "<!-- [$location] -->\n";

    if (!$specific_locations_only)
    {
?>
    <option value="-1" <?= ($location == -1 ? 'selected' : '') ?>>
	everywhere
    </option>
    <option value=0>
    </option>
<?
    }
    else
    {
?>
    <option value=0>
	none
    </option>
<?
    }

    $sql = 'SELECT n, city, librarian, ' .
	'(SELECT COUNT(*) FROM location s WHERE s.city=l.city) ' .
	'FROM location l ' .
        'JOIN physical ON l.n=location_id ' .
        'GROUP BY l.n ORDER BY city, librarian';

    $debug = 1;
    $result = db_query( $sql );

    $cur_city = '';

    while ($row = mysql_fetch_row( $result ))
    {
	$city = htmlspecialchars( $row[1] );
	$librarian = htmlspecialchars( $row[2] );

	if ($cur_city != $city)
	{
	    if ($cur_city)
	    echo "<option value=0 disabled></option>\n";
	    echo "<option value=\"$city\" disabled>$city</option>\n";

	    if ($location == $city) $selected = ' selected';
	    else $selected = '';

	    if (($row[3] > 1) && !$specific_locations_only)
		echo "<option value=\"$city\"$selected>",
			 "all $city locations",
		     "</option>\n";

	    $cur_city = $city;
	}

	if ($location == $row[0]) $selected = ' selected';
	else $selected = '';

	echo "<option value=$row[0]$selected>",
		 "...$librarian",
	     "</option>\n";
    }

    echo "</select>\n";
}

function print_menu()
{
    global $debug;
?>
<div id=menu>
    <p><? link_if_appropriate("index.php", "home") ?><br>
    <? link_if_appropriate("about.php", "about") ?><br>

    <p>
    View
    <br>...<? link_if_appropriate("call.php", "as shelf") ?>
    <br>...<? link_if_appropriate("people.php", "people involved") ?>
    <br>...<? link_if_appropriate("browse.php", "by library or city") ?>
<!--<br>...<? link_if_appropriate("index_s.php", "subject index") ?>-->
    <br>...<? link_if_appropriate("mill.html", "instruments") ?>
    <br>...<? link_if_appropriate("toc.php", "table of contents") ?>

    <p>
    Offer
    <br>...<? link_if_appropriate("entry.php", "books") ?>
    <br>...<? link_if_appropriate("entry_i.php", "instruments") ?>

<? if ($debug) { ?>
    <hr>
    <p><? link_if_appropriate("cleanup.php", "clean up") ?>
    <?/*<p><a href="http://ludd.net/bugs/">Report bug</a>*/?>
    <? } ?>
</div>
<?
}

function print_title()
{
?>
<div id=title>
    <h1>Recommend books</h1>
    <h2>lend them, find them</h2>
</div>
<?
}


function is_selected( $value, $if_matches )
{
    if ($value == $if_matches)
	return ' selected';
    else
	return '';
}

function db_query( $sql )
{
    global $debug;

    $result = mysql_query( $sql );
    if ($debug > 1)
	error_log("SQL: $sql");
    if (mysql_error())
	error_log(mysql_error() . " query: " . $sql);

    return $result;
}

function print_attach_image( $type, $id )
{
    if ($type == 'l') $type_name = 'location';
    elseif ($type == 'b') $type_name = 'book';
    elseif ($type == 'i') $type_name = 'instrument';
    ?>
    <form enctype="multipart/form-data" action="upload.php" method="post">
    <input type=hidden name=MAX_FILE_SIZE value="2000000">
    <input type=hidden name=<?=$type?> value="<?=$id?>">
    To attach an image to this <?=$type_name?>,
    <input name="userfile" type="file" size=30>, then
    <input type="submit" value="Upload">
    </form>
    <?
}

function link_if_appropriate( $page, $text )
{
    if (in_page($page))
    {
	echo "<b>$text</b>";
    }
    else
    {
	echo "<a href=\"$page\">$text</a>";
    }
}

function in_page($page)
{
    $current_page = basename($_SERVER['SCRIPT_FILENAME']);
    return ($current_page == $page);
}

function dump_assoc($array)
{
    foreach ($array as $key => $value) echo "$key : $value<br>";
}

function cmd_link_abbrev($url, $long, $short)
{
    echo "<a href=\"$url\">$long</a>";

    global $page_commands;
    $page_commands[] = array("url" => $url, "abbrev" => $short);
}

function cmd_link($url, $long)
{
    cmd_link_abbrev($url, $long, $long);
}

function order_key_to_column($order)
{
    global $call_column,
	$with_call, $with_subject;

    switch($order)
    {
	case 'a': return 'author';
	case 'c':
	    if (!isset($with_call)) $with_call = 1;
	    return $call_column;
	case 's':
	    if (!isset($with_subject)) $with_subject = 1;
	    return $call_column;
	case 'n': return 'n DESC';
    }
    return 'title_sort';
}

function massage($var_name)
{
    $s = get_form_var($var_name);
    return mysql_real_escape_string(htmlspecialchars(trim($s)));
}

function get_digit($var_name)
{
    $s = get_form_var($var_name);
    return sscanf( $s, '%d' );
}

function get_form_var($var_name)
{
    global $form_source;
    if ($form_source == 'post')
	return $_POST[$var_name];
}
?>

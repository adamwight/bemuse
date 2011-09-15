<?
include( "common.php" );

if (isset($_GET['n']))
{
    list ($image_id) = sscanf( $_GET['n'], '%d' );
}
else
{
    //
    // Requires that the server configuration contain a line like
    // AliasMatch /library/image/\d+ [...]/library/web/getimage.php
    //

    $matches = array();
    if (preg_match( '/\/(\d+)$/', $_SERVER['REQUEST_URI'], $matches ))
    {
	$image_id = $matches[1];
    }
}

//$headers = apache_request_headers();

//foreach ($headers as $h => $v) { echo "$h: $v<br>\n"; }

//if (1 or $headers['If-Modified-Since'])
//{
    //header( "HTTP/1.0 304 Not Modified" );
    //exit();
//}

$sql = "SELECT mime, length(data), data FROM image WHERE n=$image_id";
$result = db_query( $sql );

//phpinfo(INFO_VARIABLES);
if ($row = mysql_fetch_array( $result ))
{
    //header( "Cache-Control: max-age=2592000" );
    header( "Content-Type: $row[0]" );
    header( "Content-Length: $row[1]" );
    header( "Last-Modified: " . gmdate( 'D, j M Y H:i:s' ) . ' GMT' );

    echo $row[2];
}
?>

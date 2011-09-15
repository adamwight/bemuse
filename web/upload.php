<?
include( "common.php" );

$debug=1;
$document_base = "/tmp/library-img-";
$form_source = 'post';

$book_id = get_digit('b');
$location_id = get_digit('l');
$instrument_id = get_digit('i');

if ($book_id) {
    $tmpfname = $document_base . "book" . $book_id;
} elseif ($location_id) {
    $tmpfname = $document_base . "loc" . $location_id;
} elseif ($instrument_id) {
    $tmpfname = $document_base . "inst" . $instrument_id;
}

move_uploaded_file($_FILES['userfile']['tmp_name'], $tmpfname);

if (!file_exists( $tmpfname ))
{
    echo "Nothing was uploaded.<BR>\n";
    exit();
}

$size = getimagesize( $tmpfname );

$f = fopen( $tmpfname, "r" );
$data = fread( $f, filesize( $tmpfname ) );
fclose( $f );
unlink( $tmpfname );

$escaped = mysql_real_escape_string( $data );

if ($book_id)
{
    $sql = "INSERT INTO image SET book_id=$book_id, "
	 . "mime='" . $size['mime'] . "', attr='$size[3]', "
	 . "data='$escaped'";
    header( "Location: edit.php?b=$book_id" );
}
elseif ($location_id)
{
    $sql = "INSERT INTO image SET location_id=$location_id, "
	 . "mime='" . $size['mime'] . "', attr='$size[3]', "
	 . "data='$escaped'";
    header( "Location: browse.php?l=$location_id" );
}
elseif ($instrument_id)
{
    $sql = "INSERT INTO image SET instrument_id=$instrument_id, "
	 . "mime='" . $size['mime'] . "', attr='$size[3]', "
	 . "data='$escaped'";
    header( "Location: edit_i.php?i=$instrument_id" ); //XXX
}

db_query( $sql );
?>

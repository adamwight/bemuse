<? // api to catalogue engine
include_once("book.php");

function load_query_results($query_id)
{
    return load_books("source_query_id=$query_id");
}

function load_book_array($ids)
{
    return load_books("book.n in (".implode(',', $ids).")");
}

function load_books($where)
{
    $sql = "SELECT * FROM book WHERE $where";
    // sort by score
    $result = db_query($sql);

    $book_array = array();
    while ($row = mysql_fetch_assoc( $result ))
	$book_array[] = new book($row);
    return $book_array;
}

?>

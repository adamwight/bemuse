<? // api to catalogue engine
include_once("book.php");

function process_input_data($args)
{
    global $debug, $exec_path, $location,
	   $rejects, $base_cmd;

    echo "<blockquote>\n";

    $base_cmd = $exec_path;

    foreach ($args as $key => $value)
    {
	//XXX we could use the query_id
	if (strstr( $key, "ISBN-" )) add_book( $value );
	if (strstr( $key, "LCCN-" )) add_book( $value );
	if (strstr( $key, "author-" ))
	{
	    list ($id) = sscanf( substr( $key, 7 ), "%d" );
	    set_book_author( $id, $value );
            // give credit, you must have the book:
	    add_book( $id );
	}
    }

    if (isset($args['isbn']))
    {
	$isbn = trim( $args['isbn'] );
	$isbn = str_replace( '-', '', $isbn );
	$isbn = str_replace( ' ', '', $isbn );

	$matches = array();
	if (preg_match( '/^([0-9]{9}[0-9X])$/', $isbn, $matches ))
	{
	    add_by('isbn', $matches[1], 'ISBN');
	}
	else
	{
	    // app should do this
	    echo "Malformed ISBN: $isbn<br>\n";
	    $rejects[] = $isbn;
	}
    }
    elseif (isset($args['lccn']))
    {
	$lccn = trim( $args['lccn'] );
	$lccn = str_replace( ' ', '', $lccn );
	add_by('lccn', $lccn, 'LCCN');
    }
    elseif (isset($args['title']))
    {
	add_by_t_a( $args['title'], $args['author'] );
    }

    echo "</blockquote>\n";
}

class query
{
    var $params;
    var $value;
    var $add;
    //var $result;

    static $safe_keys = array ('title', 'author', 'isbn', 'lccn');
    function str()
    {
    }
}

function lookup($args)
{
//    $rejects[]
    //print_book_results( 'title and author', "$title/$author" );
}

function run_server($args)
{
    global $base_cmd, $debug;

    $args = "";
    foreach ($args as $key => $value)
    {
        if (in_array($key, query::$safe_keys))
        {
            $args .= " --{$key} ".escapeshellarg($value);
        }
    }

    $cmd = "$base_cmd $args";
    if ($debug)
	echo "[$cmd]<br>\n";

    $out = array();
    $ret;
    exec( $cmd, $out, $ret );
    if ($ret)
        return array();

    if ($debug)
	print_r($out);

    if (array_key_exists('ignore_output', $args))
        return;

    // one book on the next line.
    if ($out[0] == '1')
    {
	$books = array(new book($out[1]));
    }
    // this is a query id
    else
    {
	$books = load_query_results($out[0]);
    }

    return $books;
}


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

    return $result->fetchAll(PDO::FETCH_ASSOC);
}

?>

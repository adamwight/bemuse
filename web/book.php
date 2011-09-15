<?
require_once("common.php");
require_once("librarian.php");
require_once("classifications_all.php");

// recognizes globals
// $with_incomplete, $with_circulation, $with_call, $with_contact, $with_instruments, $with_subject, $as_table

class book
{
    public $id, $db_book;

    // accepts id or result of db.fetchall_assoc
    function book($book)
    {
	if (is_string($book)) {
	    list($book) = sscanf($book, '%d');
	}

	if (is_int($book)) {
	    $this->id = $book;
	} elseif (is_array($book)) {
	    $this->db_book = $book;
	    $this->id = $book['n'];
	} else {
	    error_log("bad book initializer: $book");
	    exit;
	}
    }

    function get_id()
    {
	return $this->id;
    }

    function get_any_author()
    {
	$author	= $this->get_author();
	if ($author != null)
	    return $author;

	return $this->get_responsible();
    }

    function get_author()
    {
	return htmlspecialchars($this->db_book['author']);
    }

    function get_responsible()
    {
	return htmlspecialchars($this->db_book['responsible']);
    }

    function get_title()
    {
	return htmlspecialchars($this->db_book['title']);
    }

    function get_call()
    {
	global $call_column;

	return htmlspecialchars($this->db_book[$call_column]);
    }

    function get_shelf()
    {
	global $call_lc, $call_dewey, $call_h, $call_column;
	//max(array_map(function($sys) { $sys->get_shelf_label($book) },$calls )
	//call_default->
	return htmlspecialchars(
	    $call_lc->get_shelf_label($this->db_book['call_lc']));
    }

    function get_created_date()
    {
	//$format = "%Y-%m-%d %H:%M:%S";
	//return strptime($this->db_book['created'], $format);
	return substr($this->db_book['created'], 0, 10);
    }

    function get_db_book()
    {
	return $this->db_book;
    }
}

class physical extends book
{
    public $location_id;

    function physical($db_book, $location_id)
    {
	parent::book($db_book);
	$this->location_id = $location_id;
    }

    function get_location_id()
    {
	return $this->location_id;
    }
}

class book_printer
{
    var $order, $last_book;

    function book_printer()
    {
	global $order;

	$this->order = $order;
    }

    function print_book_circulation($location_id)
    {
	echo "<i>";
	// wasteful
	$library = new librarian($location_id);
	$library->print_library_noun();
	echo "</i>\n";
    }

    function print_favorite_books($where, $invert_cutpoint = 0)
    {
        global $max_favorites, $order;
        $this->order = "publicity DESC, n DESC";
        if ($invert_cutpoint == 0)
            $this->print_books("$where AND (publicity > 1)", "LIMIT $max_favorites");
        else
            $this->print_books("$where AND (publicity > 1)", "LIMIT 16777216 OFFSET $max_favorites");
        //XXX fuckers

        $this->order = $order; //restore
    }

    function print_public_books($where)
    {
        $this->print_books($where . " AND (publicity = 1)");
    }

    function print_private_books($where)
    {
        $this->print_books($where . " AND (publicity = 0)");
    }

    //XXX describe why you should use this wrapper
    //TODO eliminate q_suffix
    function print_books($where, $q_suffix = NULL)
    {
	global $as_table, $call_column, $with_incomplete;

        echo "<div>";
	if ($this->order == $call_column && $as_table)
	    echo "<table>";

	if ($this->order == 'n DESC')
	{
	    $this->list_books($where, $q_suffix);
	}
	else
	{
            if (!strpos($this->order, ',')) { //simple index
                $this->list_books("$where AND (" . $this->order . " IS NOT NULL AND LENGTH(" . $this->order . ")>0)", $q_suffix);
                if ($with_incomplete) { //plus those with a null index
                    $and = "AND b.n IS NOT NULL ";
                        $and .= "AND (" . $this->order . " IS NULL " .
                            "OR LENGTH(" . $this->order . ")=0) ";
                    $this->list_books("$where $and", $q_suffix);
                }
            } else { //compound index, oh well for now
                $this->list_books($where, $q_suffix);
            }
	}

	if ($this->order == $call_column && $as_table)
	    echo "</table>";
        echo "</div>";
    }

    function get_books($where, $q_suffix = NULL)
    {
	global $call_column, $with_circulation;

	$sql = "SELECT b.n, b.title, b.author, b.responsible, "
		  . "b.$call_column, b.created, p.publicity, "
                  . "p.location_id FROM physical p "
		  . "LEFT JOIN book b ON p.book_id = b.n "
		  . "LEFT JOIN location l ON l.n = p.location_id "
		  . "WHERE $where ";
	if (!$with_circulation)
	    $sql .= "GROUP BY b.n ";
	$sql .=	"ORDER BY " . $this->order . " ";
        if (isset($q_suffix))
            $sql .= $q_suffix;

	$result = db_query( $sql );

        $out = array();
	while ($book = mysql_fetch_assoc($result))
	{
            //XXX streaming print
	    //$this->print_book(new physical($book, $book['location_id']));
	    $out[] = new physical($book, $book['location_id']));
	}
        return $out;
    }

    function close_row()
    {
	global $as_table, $call_column;

	if ($as_table)
	{
	    echo "</td></tr>";
	    if ($this->order != $call_column)
		echo "</table>";
	}
        else
            echo "</div>";
	echo "\n";
    }

    function print_book($book)
    {
	global $as_table, $call_column, $order,
	    $with_circulation, $with_call;

	// close last book, if any
	if ($book->get_id() == $this->last_book)
	{
	    if ($with_circulation)
	    {
		echo "<br>\n";
		$this->print_book_circulation($book->get_location_id());
	    }
	    return;
	}
	if (isset($this->last_book))
	    $this->close_row();

	// start new
	$raw_title = $book->get_title();
	if ($raw_title == null)
	    $raw_title = "<i>Unknown</i>";

	$title	= "<u><a href=\"edit.php?b=" . $book->get_id(). "\">"
		    . $raw_title
		    . "</a></u>";
	$author = $book->get_any_author();
	$call	= $book->get_call();

	if ($author == null)
	    $author = "<i>[unknown]</i>";

	if ($as_table)
	{
	    if ($this->order != $call_column)
		echo "<table cellspacing=0 cellpadding=1 border=0 width=\"100%\">";
	    echo "<tr><td>";
	}
	else
	    echo "<div class=book>\n";

        //global order determines style
	if ($order == 'n DESC')
	{
	    if ($book->get_created_date() != $this->last_date)
	    {
		echo "<p><b>" . $book->get_created_date() . "</b><br>";
		$this->last_date = $book->get_created_date();
	    }
	}

	if ($order == 'title_sort' or $order == 'n DESC')
	    echo "$title " . ($author ? " by $author" : '') . "\n";
	elseif ($order == 'author') echo "$author. $title<br>\n";
	elseif ($order == $call_column)
	{
	    if ($with_call)
	    {
		if ($call != null)
		    echo str_replace(" ", "&nbsp;", $call);
		else
		    echo "&nbsp;";
	    }

	    if ($as_table)
		echo "</td><td align=left width=\"100%\">";

	    echo "$title by $author";
	}

	if ($with_circulation)
	{
	    if ($as_table)
		echo "<td align=right>";
	    $this->print_book_circulation($book->get_location_id());
	}
	$this->last_book = $book->get_id();
    }

    function print_index_card_static( $book, $extra )
    {
	global $call_lc, $call_dewey, $call_h;
	$db_book = $book->get_db_book();
    ?>
    <div id=book class=readonly>
    <table bgcolor="#412d10" cellpadding=2 cellspacing=0 border=0>
    <tr><td>
    <table bgcolor=white cellspacing=0 cellpadding=5 border=0>

	<tr>
	    <td align=right>title</td>
	    <td colspan=3><?= $book->get_title() ?></td>
	</tr>
	<tr>
	    <td align=right>author</td>
	    <td colspan=3><?= $book->get_author() ?></td>
	</tr>
    <? if ($db_book['responsible']) { ?>
	<tr>
	    <td align=right>responsible</td>
	    <td colspan=3><?= $book->get_responsible() ?></td>
	</tr>
    <? } ?>
    <? if ($db_book['call_lc']) { ?>
	<tr>
	    <td align=right>Library of Congress call #</td>
	    <td ><?= $db_book['call_lc'] ?></td>
	    <td >= <?= $call_lc->get_heading_verbose($db_book['call_lc'], 'lc') ?></td>
	</tr>
    <? } ?>
    <? if ($db_book['call_dewey']) { ?>
	<tr>
	    <td align=right>Dewey call #</td>
	    <td ><?= $db_book['call_dewey'] ?></td>
	    <td >= <?= $call_dewey->get_heading_verbose($db_book['call_dewey'], 'dewey') ?></td>
	</tr>
    <? } ?>
    <? if ($db_book['call_h']) { ?>
	<tr>
	    <td align=right><i>your call</i></td>
	    <td ><?= $db_book['call_h'] ?></td>
	    <td >= <?= $call_h->get_heading_verbose($db_book['call_h'], 'h'/* huh? why repeat even once? call->get_heading_verbose($db_book)*/) ?></td>
	</tr>
    <? } ?>
    <? if ($db_book['lccn'] || $db_book['isbn']) { ?>
	<tr>
	    <td align=right>LCCN</td>
	    <td colspan=3><?= $db_book['lccn'] ?> ISBN <?= $db_book['isbn'] ?></td>
	</tr>
	<tr>
	    <td align=right>Publisher</td>
	    <td colspan=3>
		<?= $db_book['pub_place'] ?>: <?= $db_book['pub_name'] ?>,
		<?= $db_book['pub_dates'] ?>.
	    </td>
	</tr>
    <? } ?>
	<tr>
	    <td align=center colspan=4>
		<?= $extra ?>
	    </td>
	</tr>

    </table>
    </td></tr></table>
    </div>
    <?
    }

    //function print($book, $location_id);
}
?>

#!/usr/bin/perl -w
# $Id: dump-book.pl,v 1.1 2005/01/23 09:28:05 adamw Exp $

use Getopt::Long;
use DBI qw(:sql_types);

$verbose = 0;

GetOptions( 'where=s' => \$where,
	    'verbose+' => \$verbose );

if ($ARGV[0] =~ /(\d{10})/) { $where = "isbn='$1'"; }
if (!$where) { die 'need a where'; }

$dbh = DBI->connect( "DBI:mysql:library", "web", "gronk" )
	    || die "Connect failed: $DBI::errstr\n";

$sql = "SELECT n, isbn, title_full, author, call_lc, call_dewey "
    . "FROM book WHERE $where";
$sth = $dbh->prepare( $sql );
$sth->execute;

while (@row = $sth->fetchrow_array( ))
{
    print "$row[2] by $row[3]\n";
    print "call $row[4] or $row[5]\n";
    print "database id: $row[0], isbn: $row[1]\n";

    $sql = "SELECT location.n, location.city, location.librarian "
	    . "FROM physical "
	    . "LEFT JOIN location ON physical.location_id=location.n "
	    . "WHERE book_id=$row[0]";
    $sth2 = $dbh->prepare( $sql );
    $sth2->execute;
    while (@row2 = $sth2->fetchrow_array())
    {
	print "  $row2[2]'s library in $row2[1] (loc #$row2[0])\n";
    }
    $sth2->finish;
}

$sth->finish;

$dbh->disconnect( );

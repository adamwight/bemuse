#!/usr/bin/perl -w
# $Id: delete-book.pl,v 1.1 2005/01/23 09:28:05 adamw Exp $

use Getopt::Long;
use DBI qw(:sql_types);

$verbose = 0;

GetOptions( 'where=s' => \$where,
	    'verbose+' => \$verbose );

if ($ARGV[0] =~ /(\d{10})/) { $where = "isbn='$1'"; }
if (!$where) { die 'need a where'; }

$dbh = DBI->connect( "DBI:mysql:library", "web", "gronk" )
	    || die "Connect failed: $DBI::errstr\n";

@row = $dbh->selectrow_array( "SELECT n FROM book WHERE $where" );
if (!$row[0]) { die "no books match \"$where\""; }
$id = $row[0];

print "Deleting book id $id\n";

$dbh->do( "DELETE FROM physical WHERE book_id=$id" );
$dbh->do( "DELETE note, note_link FROM note, note_link "
	. "WHERE book_id=$id AND note_id=note.n" );
$dbh->do( "DELETE subject, subject_link FROM subject, subject_link "
	. "WHERE book_id=$id AND subject_id=subject.n" );
$dbh->do( "DELETE FROM book WHERE n=$id" );

$dbh->disconnect( );

/*
 * $Id: metacatalog.cc,v 1.12 2006/04/04 03:01:14 adamw Exp $
 */

#include <stdio.h>
#include <mysql/mysql.h>
#include <string.h>

#include "mysql_db.h"
#include "isbn.h"
#include "location.h"
#include "metacatalog.h"
#include "catalog.h"
#include "catalog_amazon.h"
#include "catalog_parallel.h"
#include "globals.h"
#include "query.h"

metacatalog::metacatalog()
{
    db_query = NULL;
    z = NULL;
}

metacatalog::~metacatalog()
{
    if (db_query)
	delete db_query;
    if (z)
	delete z;
}

int metacatalog::get_info( const char *isbn )
{
    if (!valid_isbn( isbn ))
    {
	fprintf( stderr, "the checkdigit on isbn %s is wrong\n", isbn );
	return 0;
    }

    book b;
    b.isbn = isbn;

    return get_info( b );
}

int metacatalog::get_info( book& b )
{
    db_query = new query(b);
    if (!z)
	z = new catalog_parallel();

    // XXX insert logic here
    if (b.db_get())
    {
	results.add(b);
	b.source_query = db_query; // loop
	b.commit();
	if (!all_catalogs)
	    return n_results();
    }

    int n_results = z->do_query(db_query);
    results.add(z->results);

    if (!n_results)
    {
	if (b.title.empty() || b.author.empty())
	{
	    catalog_amazon amazon;
	    if (!amazon.do_query(db_query)) return 0;

	    n_results = z->do_query(db_query);

	    if (!n_results) n_results = 1; // XXX? think i am faking to ensure that the book is successfully inserted with just T&A
	}
    }

    // flush out book details using query results
    refine(b, results.result_set);
    if (!b.db_id) b.commit(); // XXX amazon, it seems. but this is dangerous behavior because the book might still be made of query-quality kardbord

    return n_results;
}

void metacatalog::refine(book& b, const vector<unsigned long>& r)
{
    vector<book> hits = result_store::get_results(r);
    //TODO
    if (hits.size()) {
        b.db_id = hits[0].db_id;
        b.db_get();
    }
}

void metacatalog::refine_all( )
{
    printf( "getting all missing book info by isbn\n" );

    db->execute( "UPDATE book SET informed=0 WHERE title IS NULL "
			 "OR title='' OR author IS NULL "
			 "OR author='' OR call_lc IS NULL "
			 "OR call_lc=''" );

    MYSQL_RES *result = db->query( 
	"SELECT isbn FROM book WHERE NOT informed "
		"AND isbn IS NOT NULL AND isbn != '' "
		"ORDER BY isbn" );


    while (MYSQL_ROW row = mysql_fetch_row( result ))
    {
	get_info( row[0] );
    }

    mysql_free_result( result );
    printf( "done\n" );
}

void metacatalog::lookup_file( const char* path )
{
    /* this should be in book? */
    char buf[32];
    FILE* f = fopen( path, "r" );

    while (fgets( buf, 32, f ))
    {
	if (strlen( buf ) > 11)
	{
	    fprintf( stderr, "invalid isbn: %s\n", buf );
	    continue;
	}

	buf[10] = 0;
	book b;
	b.isbn = buf;
	get_info( b );
	location l(location_id);
	l.add(b);

	printf( "added <u>%s</u> by %s (isbn %s)\n", b.title.c_str(),
		b.author.c_str(), buf );
    }
}

void metacatalog::wait()
{
    if (z)
	z->wait_all();
}

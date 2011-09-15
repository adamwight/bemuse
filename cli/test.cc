/*
 * $Id: test.cc,v 1.9 2006/04/04 03:02:56 adamw Exp $
 */

#include "marc.h"
#include "isbn.h"
#include "book.h"
#include "metacatalog.h"
#include "catalog_amazon.h"
#include "mysql_db.h"
#include "globals.h"
#include "catalog_z3950.h"
#include "catalog_parallel.h"

#include <string>
#include <iostream>
using namespace std;

#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <stdio.h>

//todo:
void test_standardize();
void test_parallel();
void test_result_set();

void test_validate_isbn();
void test_lookup_isbn_siteselect();
void test_all_servers();
void test_amazon();
void test_amazon_dependent();
void test_localdb();
void test_z_lookup();
void test_lookup_lccn();

int lookup_isbn(string isbn, book& b);
void marc_read();

void run_all_tests();

int main()
{
    init_globals();
    verbose = 0;
    dump = 0;

    run_all_tests();
}

void run_all_tests()
{
    test_validate_isbn();
    test_lookup_isbn_siteselect();
    test_amazon();
    test_amazon_dependent();
    test_lookup_lccn();
}

void test_lookup_isbn_siteselect()
{
    book book1;
    string loc_only = "1572241071";
    if (!lookup_isbn(loc_only, book1) || book1.src->get_name() != "z3950.loc.gov")
	fprintf(stderr, "[LOC isolate] lookup fails\n");

    book book2;
    string yale_only = "123456789X";
    if (!lookup_isbn(yale_only, book2) || book2.src->get_name() != "prodorbis.library.yale.edu")
	fprintf(stderr, "[yale isolate] lookup fails\n");

    //book book3;
    //string amazon_only = "0395349222"; // somehow gets to the correct book, 039519492X
    //if (!lookup_isbn(amazon_only, book3) || book3.src != "amazon.com")
}

void test_all_servers()
{
    catalog_parallel p;

    for (unsigned i = 0; i < p.server_list.size(); i++)
    {
	catalog_z3950* z = p.server_list[i];

	book b;
	b.title = "the joy of pi";
	if (z->get_info(b) == 0)
	    cerr << "Fucking z catalog doesn't work: "
		 << z->get_server()->hostname << endl;
	else
	    cout << "Z catalog #" << i + 1 << " works, "
		 << z->get_server()->hostname << endl;
    }
}

void test_amazon_dependent()
{
    book b;
    string amazon_only = "0743490398";
    //if (!lookup_isbn(amazon_only, b) || ((server_z3950*)b.src)->hostname == "amazon.com" || b.call_lc.empty())
    if (!lookup_isbn(amazon_only, b) || b.src->get_name() == "www.amazon.com" || b.call_lc.empty())
	fprintf(stderr, "amazon -> Z enhancement lookup fails\n");
    if (verbose > 1)
	b.print();
}

void test_amazon()
{
    book b;
    b.isbn = "0743490398";
    catalog_amazon cat;
    if (cat.get_info(b) < 1)
	cerr << "amazon lookup gets nothing" << endl;
    else
    {
	if (verbose > 1)
	    b.print();
    }
    if (b.title.empty())
	cerr << "amazon lookup doesn't retrieve title" << endl;
    if (b.author.empty())
	cerr << "amazon lookup doesn't retrieve author" << endl;
}

void test_localdb()
{
    const string local_only = "0900384832";
    book b;
    b.isbn = local_only;
    // TODO: ensure that this is a db get. it isn't right now
    if (!lookup_isbn(local_only, b))
	fprintf(stderr, "failed to get bib from the local database\n");
}

void test_validate_isbn()
{
    char lower_x[] = "123456789x";
    char bad_checksum[] = "1234567893";
    char bad_length[] = "123456789";
    char good[] = "123456789X";

    if (!valid_isbn(lower_x)) fprintf(stderr, "Lowercase X fails\n");
    if (valid_isbn(bad_checksum)) fprintf(stderr, "Allows bad checksum\n");
    if (valid_isbn(bad_length)) fprintf(stderr, "Lowercase X\n");
    if (!valid_isbn(good)) fprintf(stderr, "Good isbn fails\n");
}

void test_z_lookup()
{
    catalog_parallel p;
    catalog_z3950* z = p.server_list[0];

    book b;
    b.isbn = "0393956369";
    z->get_info(b);
    if (b.title.empty())
	cerr << "Fucking z catalog doesn't work" << endl;
}

void test_lookup_lccn()
{
    book b;
    b.lccn = "61-11340";
    metacatalog c;
    if (c.get_info(b) == 0)
	cerr << "lccn lookup failed" << endl;
}

/*
 * helper shit
 */

int lookup_isbn(string isbn, book& b)
{
    b.isbn = isbn;

    metacatalog cat;
    int n_results = cat.get_info(b);
    if (verbose)
	printf("Meta lookup of isbn %s, %d results\n", isbn.c_str(), n_results);
    if (verbose > 1)
	b.print();
    if (verbose > 2)
    {
	for (int i = 0; i < cat.n_results(); i++)
	{
	    b.db_id = cat.result_set()[i];
	    b.db_get();
	    b.print();
	}
    }

    return n_results;
}

void marc_read()
{
    int fd = open( "docs/sandburg.mrc", O_RDONLY );
    if (fd == -1) perror( NULL );
    
    off_t size = lseek( fd, 0, SEEK_END );
    lseek( fd, 0, SEEK_SET );
    char* data = (char*)malloc( size + 1 );
    off_t nb = read( fd, data, size );
    if (nb == -1) perror( NULL );
    close( fd );

    marc m;

    m.parse_raw( data );
    free( data );
}

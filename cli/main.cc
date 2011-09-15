/*
 * $Id: main.cc,v 1.33 2006/04/04 03:00:12 adamw Exp $
 */

#include <unistd.h>
#include <stdio.h>
#include <stdlib.h>
#include <getopt.h>
#include <netdb.h>
#include <netinet/in.h>
#include <sys/socket.h>
#include <sys/time.h>
#include <iostream>
#include <mysql/mysql.h>

#include "mysql_db.h"
#include "book.h"
#include "metacatalog.h"
#include "isbn.h"
#include "html_printer.h"
#include "location.h"
#include "scandata.h"
#include "globals.h"

void parse_options(int argc, char* argv[]);
void usage();

void announce_success( book& b );

printer* output;
metacatalog catalog;
location* default_location;

int batch = 0;
int lookup = 0;
int lookup_all = 0;
int add_it = 0;
string isbn, author, title, lccn, barcode;
string filename;
string search_param;
unsigned long book_id;

int main( int argc, char* argv[] )
{
    output = new html_printer;
    int ret = 0;

    parse_options(argc, argv);
    init_globals();

    book b;

    b.db_id = book_id;
    b.isbn = isbn;
    b.lccn = lccn;

    if (!author.empty())
    {
	b.author = author;
    }
    if (!title.empty())
    {
	b.title = title;
	// XXX there should be no magic in title
	b.commit();

        announce_success(b);
        output->results(b);

	b.db_id = 0; // XXX b.cannotcommit=1
    }
    else if (!filename.empty())
    {
	catalog.lookup_file( filename.c_str() );
    }
    else if (lookup_all)
    {
	catalog.refine_all();
    }
    else if (!isbn.empty() || !lccn.empty()) /// XXX assumption is that query returns same isbns...
    {
	if (b.db_get() && b.informed())
	{
	    b.commit();
	    announce_success( b );
            output->results(b);
	}
	else lookup = 1;
    }

    if (b.db_id)
    {
	b.db_get();
	b.commit(); // XXX why?
    }

    if (lookup)
    {
	int n_results = catalog.get_info( b );

	if (n_results)
	{
	    announce_success( b );
            output->results(catalog);
	}
	else if (fast)
	{
	    announce_success( b );
            output->results(catalog);
	    catalog.wait();
	}
	else
	{
            string err = "couldn't find " + search_param;
            output->fail(err);

	    ret = -1;
	}
    }

    if (b.db_id && add_it) default_location->add(b);

    delete db;
    delete output;

    return ret;
}

void parse_options(int argc, char* argv[])
{
    int opt;
    struct option opts[] =
    {
	{ "book-id",     required_argument, NULL,	   'n' },
	{ "file",        required_argument, NULL,	   'f' },
	{ "author",      required_argument, NULL,	   'a' },
	{ "isbn",        required_argument, NULL,	   'i' },
	{ "barcode",     required_argument, NULL,	   'b' },
	{ "lccn",        required_argument, NULL,	   'c' },
	{ "location",    required_argument, NULL,	   'l' },
	{ "standardize", required_argument, NULL,	   's' },
	{ "title",       required_argument, NULL,	   't' },
	{ "verbose",     no_argument,	    NULL,	   'v' },
	{ "add",	 no_argument,	    &add_it,	    1  },
	{ "dump",        no_argument,	    &dump,	    1  },
	{ "everywhere",  no_argument,	    &all_catalogs,  1  },
	{ "isbn-all",    no_argument,	    &lookup_all,    1  },
	{ "lookup",      no_argument,       &lookup,	    1  },
	{ 0, 0, 0, 0 }
    };

    if (argc < 2) { usage(); }

    while ((opt = getopt_long( argc, argv, "b:df:h:i:l:n:t:v", opts, NULL )) != EOF)
    {
	switch (opt)
	{
	case 'a':
	    author = optarg;
	    break;
	case 'n':
	    book_id = strtoul( optarg, NULL, 10 );
	    break;
	case 'f':
	    filename = optarg;
	    break;
	case 'i':
	    if (!valid_isbn( optarg ))
	    {
                string err = "'" + *(new string(optarg)) + "' is a malformed isbn";
                output->fail(err);
		exit(-1);
	    }

	    isbn = optarg;
	    search_param = "isbn " + isbn;
	    break;
	case 'b':
	    barcode = optarg;
	    {
		scandata d(barcode.c_str());
		if (d.valid())
		{
		    isbn = d.isbn;
		    search_param = "isbn " + isbn;
		}
	    }
	    break;
	case 'l':
	    default_location = new location(strtoul( optarg, NULL, 10 ));
	    break;
	case 'c':
	    lccn = optarg;
	    search_param = "lccn " + lccn;
	    break;
	case 's':
	    book_id = strtoul( optarg, NULL, 10 );
	    break;
	case 't':
	    title = optarg;
	    break;
	case 'v':
	    verbose++;
	    break;
	case 0: break;
	default:
	    usage();
	    break;
	}
    }
}

void announce_success( book& b )
{
    output->announce_add_success(b, search_param);

    if (location_id && add_it) default_location->add(b);
}

void usage()
{
    printf(
"usage: prog [OPTION]...\n\n"
"  -n, --book-id ID\n"
"  -t, --title STRING\n"
"  -a, --author STRING\n"
"  -i, --isbn STRING\n"
"  -b, --barcode STRING\n"
"  -c, --lccn STRING\n"
"      --lookup\n"
"  -e, --everywhere\n"
"      --add\n"
"  -l, --location ID\n"
"  -s, --standardize ID\n"
"      --isbn-all\n"
"  -f, --file FILE\n"
"  -d, --dump\n"
"  -v, --verbose\n" );
    exit(1);
}

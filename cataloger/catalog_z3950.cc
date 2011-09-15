/*
 * $Id: catalog_z3950.cc,v 1.18 2006/04/04 02:55:56 adamw Exp $
 */

#include <stdlib.h>
#include <stdio.h>
#include <iostream>

#include "catalog_z3950.h"
#include "marc.h"
#include "globals.h"

catalog_z3950::catalog_z3950(server_z3950* s) :
    catalog_base(),
    server(s),
    conn(NULL)
{
}

catalog_z3950::~catalog_z3950()
{
    close();
}

int catalog_z3950::get_by_isbn( book& b )
{
    string q = "@attr 1=7 \"" + b.isbn + "\"";

    return search( q.c_str() );
}

int catalog_z3950::get_by_lccn( book& b )
{
    string q = "@attr 1=9 \"" + b.lccn + "\"";

    return search( q.c_str() );
}

int catalog_z3950::get_by_lc_call( book& b )
{
    return 0;

    string q = "@attr 1=16 \"" + b.call_lc + "\"";

    return search( q.c_str() );
}

int catalog_z3950::get_by_title_author( book& b )
{
    b.calculated_fields();
    string q = "@and @attr 1=4 \"" + b.title_sort + "\" "
			 "@attr 1=1003 \"" + b.author + "\"";
    // N.B. - UCB shits if a keyword like "and" appears in a term

    return search( q.c_str() );
}

int catalog_z3950::get_by_title( book& b )
{
    string q = "@attr 1=4 \"" + b.title_sort + "\"";

    return search( q.c_str() );
}

int catalog_z3950::connect()
{
    if (conn) return 1;

    if (verbose) cout << "connecting to " << server->hostname << "\n";

    try {
	conn = new ZOOM::connection(server->hostname.c_str(), server->port);
    } catch(ZOOM::bib1Exception& err) {
	cerr << server->hostname << ": connect: bib1Exception " <<
	    err.errmsg() << " (" << err.addinfo() << ")\n";
	return 0;
    } catch(ZOOM::exception& err) {
	cerr << server->hostname << ": connect: exception " <<
	    err.errmsg() << "\n";
	return 0;
    }

    conn->option( "databaseName", server->dbname.c_str() );
    conn->option( "preferredRecordSyntax", "USMARC" );

    return 1;
}

void catalog_z3950::close()
{
    delete conn;
    conn = NULL;
}

int catalog_z3950::search( const char* q )
{
    if (!connect()) return 0;

    if (verbose)
	cout << "z query " << server->hostname << ": " << q << "\n";

    ZOOM::prefixQuery pq(q);
    int n_records;

    try
    {
	ZOOM::resultSet rs( *conn, pq );

	n_records = rs.size();
	if (verbose)
	    cout << "got " << n_records << " matches from "
		 << server->hostname << "\n";

	for (int i = 0; i < n_records; i++)
	{
	    const ZOOM::record rec( rs, i );

	    //cout << "[" << rec.rawdata() << "]\n";
	    marc m;
	    m.parse_raw( rec.rawdata().c_str() ); // rename or move function
	    m.src = server;
	    m.source_query = db_query;
	    m.commit();

	    if (result_set().size() && result_set().back() == m.db_id) continue;

	    results.add(m);
	}
    }
    catch(ZOOM::bib1Exception err)
    {
	cerr << "error while searching " << server->hostname << ": "
	    << err.errmsg();
	if (!err.addinfo().empty())
	    cerr << "; " << err.addinfo() << "\n";
	return 0;
    }

    return n_records;
}

server_z3950* catalog_z3950::get_server()
{
    return server;
}

/*
 * $Id: server_z3950.h,v 1.3 2006/04/04 03:02:40 adamw Exp $
 */

#ifndef SERVER_Z3950_H
#define SERVER_Z3950_H

#include <string>
using namespace std;

#include "source.h"

// maybe rename to `server'
class server_z3950 : public source
{
public:
    server_z3950( unsigned long id, const char* h, int p, const char* d ) :
	source(id), hostname( h ), dbname( d ), port( p ) {}
    virtual ~server_z3950() {}

    virtual string get_name() { return hostname; }

    string hostname, dbname;
    int port;
};

#endif

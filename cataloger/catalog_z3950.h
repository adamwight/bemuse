/*
 * $Id: catalog_z3950.h,v 1.10 2006/04/04 02:59:39 adamw Exp $
 */

#ifndef CATALOG_Z3950_H
#define CATALOG_Z3950_H

#include "yazpp/zoom.h"

#include "catalog_base.h"
#include "book.h"
#include "server_z3950.h"

class catalog_z3950 : public catalog_base
{
public:
    catalog_z3950(server_z3950* server);
    virtual ~catalog_z3950();

    server_z3950* get_server();

protected:
    int get_by_isbn( book& b );
    int get_by_lccn( book& b );
    int get_by_lc_call( book& b );
    int get_by_title_author( book& b );
    int get_by_title( book& b );

    int search( const char* q );

    int connect();
    void close();

    server_z3950* server;
    ZOOM::connection* conn;
};

#endif

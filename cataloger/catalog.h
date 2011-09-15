/*
 * $Id: catalog.h,v 1.19 2006/04/04 02:50:16 adamw Exp $
 */

#ifndef CATALOG_H
#define CATALOG_H

#include <string>
#include <vector>
using namespace std;

#include "book.h"
#include "result.h"
#include "query.h"

class catalog
{
public:
    catalog() : db_query(NULL) {}
    virtual ~catalog() {}

    virtual int get_info(book& b) = 0;
    int do_query(query* q);

    int n_results() const { return result_set().size(); }
    const vector<unsigned long>& result_set() const { return results.result_set; }
    string result_string() const;

public:
    static void urlescape( string& s );
    static void striptags( string& s );

    result results;
    string failure_reason;

protected:
    query* db_query;
};

#endif

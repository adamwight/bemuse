/*
 * $Id: metacatalog.h,v 1.7 2006/03/27 10:22:51 adamw Exp $
 */

#ifndef METACATALOG_H
#define METACATALOG_H

#include <vector>

#include "catalog_base.h"
#include "book.h"

class metacatalog : public catalog
{
public:
    metacatalog();
    virtual ~metacatalog();

    int get_info( const char* isbn );
    int get_info( book& b );

    void lookup_file( const char* path );
    void refine_all();
    void refine(book& b, const vector<unsigned long>& r);

    void wait();

    class catalog_parallel* z;
};

#endif

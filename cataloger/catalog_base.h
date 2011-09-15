/*
 * $Id: catalog_base.h,v 1.5 2006/04/04 02:56:25 adamw Exp $
 */

#ifndef CATALOG_BASE_H
#define CATALOG_BASE_H

#include <string>
#include <vector>
using namespace std;

#include "catalog.h"

// with the specific lookups
class catalog_base : public catalog
{
public:
    catalog_base() : catalog() {}
    virtual ~catalog_base() {}

    virtual int get_info(book& b);
    // XXX why the fuck can't this go away?
    //virtual int get_info(class query* q) { return catalog::get_info(q); }

public:
    virtual int get_by_isbn( book& b ) { return 0; }
    virtual int get_by_lccn( book& b ) { return 0; }
    virtual int get_by_lc_call( book& b ) { return 0; }
    virtual int get_by_title_author( book& b ) { return 0; }
    virtual int get_by_title( book& b ) { return 0; }
};

#endif

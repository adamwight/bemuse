/*
 * $Id: catalog_amazon.h,v 1.5 2006/02/14 03:21:09 adamw Exp $
 */

#ifndef CATALOG_AMAZON_H
#define CATALOG_AMAZON_H

#include "catalog_base.h"

class catalog_amazon : public catalog_base
{
public:
    virtual int get_by_isbn(book& b);

private:
    char* fetch_redirected(const string& url);
};

#endif

/*
 * $Id: query.h,v 1.1 2006/03/30 23:05:02 adamw Exp $
 */

#ifndef QUERY_H
#define QUERY_H

#include "db_entity.h"
#include "book.h"

class query : public db_entity
{
public:
    query(const class book& src);
    query(unsigned long id);
    virtual ~query();

    book get_book();

protected:
    void commit();
    int db_get();

    book source_book;
    int free_book;
};

#endif

// $Id

#include <stdio.h>
#include <stdlib.h>

#include "query.h"
#include "mysql_db.h"
#include "book.h"
#include "globals.h"

query::query(const book src)
{
    // XXX and the book should be db copied? one src, one as-was?
    source_book = new book(src);
    commit();
}

query::query(unsigned long id)
{
    db_id = id;
    db_get();
}

query::query(const query& q)
{
    db_id = q.db_id;
    db_get();
}

query::~query()
{
    // XXX wtf am i forgetting?
}

void query::commit()
{
    char sql[64];
    snprintf(sql, 64, "INSERT INTO query SET source_book_id=%lu",
	source_book->get_id());
    db->execute(sql);

    db_id = db->last_insert_id();
}

int query::db_get()
{
    if (!db_id) return 0;

    char sql[64];
    snprintf(sql, 64, "SELECT source_book_id FROM query WHERE query_id=%lu", db_id);

    MYSQL_RES* result = db->query(sql);

    if (MYSQL_ROW row = mysql_fetch_row(result))
    {
	if (row[0])
	{
	    source_book = new book(strtoul(row[0], NULL, 10));
	    // XXX db_get?
	    return 1;
	}
    }
    return 0;
}

book* query::get_book()
{
    return source_book;
}

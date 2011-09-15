/*
 * $Id: book.cc,v 1.32 2006/04/04 02:48:34 adamw Exp $
 */

#include <stdio.h>
#include <string.h>
#include <stdlib.h>

#include "book.h"
#include "mysql_db.h"
#include "marc.h"
#include "query.h"
#include "note.h"
#include "subject.h"
#include "source.h"

extern mysql_db* db;

book::book() :
    db_entity(0),
    note_head(NULL),
    subject_head(NULL),
    source_query(NULL),
    src(NULL)
{
}

book::~book()
{
    if (subject_head) delete subject_head;
    if (note_head)    delete note_head;
    // XXX we only sometimes own this data
    //if (source)	      delete source;
    //if (source_query) delete source_query;
}

int book::db_get()
{
    if (!get_id()) return 0;

    char sql[1024];
    char fields[] = "isbn, title, subtitle, "
		    "responsible, author, author_dates, fuller_name, "
		    "d_class_number, d_item_number, "
		    "lc_class_number, lc_item_number, "
		    "lccn, subtitle, "
		    "pub_place, pub_name, pub_dates, "
		    "extent, dimensions, other_phys, "
		    "language, source_id, source_query_id";

    snprintf( sql, 1024,
	      "SELECT %s FROM book WHERE n=%lu", fields, db_id );

    MYSQL_RES *result = db->query(sql);

    if (MYSQL_ROW row = mysql_fetch_row( result ))
    {
	isbn            = (row[0] ? row[0] : "");
	title           = (row[1] ? row[1] : "");
	subtitle        = (row[2] ? row[2] : "");
	responsible     = (row[3] ? row[3] : "");
	author          = (row[4] ? row[4] : "");
	author_dates    = (row[5] ? row[5] : "");
	fuller_name     = (row[6] ? row[6] : "");
	d_class_number  = (row[7] ? row[7] : "");
	d_item_number   = (row[8] ? row[8] : "");
	lc_class_number = (row[9] ? row[9] : "");
	lc_item_number  = (row[10] ? row[10] : "");
	lccn            = (row[11] ? row[11] : "");
	pub_place       = (row[13] ? row[13] : "");
	pub_name        = (row[14] ? row[14] : "");
	pub_dates       = (row[15] ? row[15] : "");
	extent          = (row[16] ? row[16] : "");
	dimensions      = (row[17] ? row[17] : "");
	other_phys      = (row[18] ? row[18] : "");
	language        = (row[19] ? row[19] : "");

	src		= (row[20] ?
	    new source(strtoul(row[20], NULL, 10)) : NULL);
	source_query	= (row[21] ?
	    new query(strtoul(row[21], NULL, 10)) : NULL);

	calculated_fields();

	if (subject_head) delete subject_head;
	subject_head = subject_heading::db_get( db_id );

	if (note_head) delete note_head;
	note_head = note::db_get( db_id );

	return 1;
    }

    return 0;
}

// try to match this book with one already in the db
unsigned long book::db_get_id()
{
    calculated_fields();

    string where;

    if (!isbn.empty())
    {
	where = "isbn='" + isbn + "'";
    }
    else if (!lccn.empty())
    {
	where = "lccn='" + lccn + "'";
    }
    else if (!title_sort.empty())
    {
	string t_q = db->quote(title_sort);

	where = "title='" + t_q + "'";

	if (!author.empty())
	{
	    string a_q = db->quote(author);

	    where += " AND author='" + a_q + "'";
	}
    }
    else if (!call_lc.empty())
    {
	where = "call_lc='" + call_lc + "'";
    }
    else
    {
	return 0;
    }

    char query[1024];
    snprintf( query, 1024,
	      //"SELECT n FROM book WHERE %s AND NOT modified",
	      "SELECT n FROM book WHERE %s",
	      where.c_str() );

    MYSQL_RES *result = db->query( query );

    if (MYSQL_ROW row = mysql_fetch_row( result ))
    {
	db_id = strtoul( row[0], NULL, 10 );
    }

    return db_id;
}

int book::informed() const
{
    return !(title_full.empty() || call_lc.empty());
}

void book::calculated_fields()
{
    title_full = title;
    if (!subtitle.empty())
    {
	(title_full += ": ") += subtitle;
    }

    title_sort = title;
    const char* prefix[] = {
	"a ", "an ", "the ", "le ", "la ", "lo ", "les ", "las ", "los ",
	"l'", "gli ", "das ", ""
    };
    erase_prefix( title_sort, prefix );

    if (lc_item_number.empty())
	call_lc = lc_class_number;
    else if (lc_item_number[0] == '.') 
	call_lc = lc_class_number + lc_item_number;
    else
	call_lc = lc_class_number + " " + lc_item_number;

    call_dewey = d_class_number + d_item_number;

    if (src)
    {
	char s[16];
	snprintf(s, 16, "%lu", src->get_id());
	source_id = s;
    }
    else
	source_id = "null";

    if (source_query)
    {
	char s[16];
	snprintf(s, 16, "%lu", source_query->get_id());
	source_query_id = s;
    }
    else
	source_query_id = "null";
}

/* static */ void book::erase_prefix( string& s, const char* list[] )
{
    if (s.empty()) return;

    for (int i = 0; list[i][0]; i++)
    {
	if (strncasecmp( s.c_str(), list[i], strlen( list[i] ) ) == 0)
	{
	    s.erase( 0, strlen( list[i] ) );
	    return;
	}
    }
}

void book::commit()
{
    string command;
    char tail[64] = "";

    if (db_id)
    {
	command = "UPDATE";
	snprintf( tail, 64, " WHERE n=%lu", db_id );
    }
    else
    {
	command = "INSERT INTO";
	snprintf( tail, 64, ", created=now()" );
    }

    calculated_fields();

    string query =
	command + " book SET "
	    "title_full='"	+ db->quote(title_full) + "', "
	    "title_sort='"	+ db->quote(title_sort) + "', "
	    "title='"		+ db->quote(title) + "', "
	    "subtitle='"	+ db->quote(subtitle) + "', "
	    "responsible='"	+ db->quote(responsible) + "', "
	    "author='"		+ db->quote(author) + "', "
	    "author_dates='"    + db->quote(author_dates) + "', "
	    "fuller_name='"     + db->quote(fuller_name) + "', "
	    "call_dewey='"      + db->quote(call_dewey) + "', "
	    "d_class_number='"  + db->quote(d_class_number) + "', "
	    "d_item_number='"   + db->quote(d_item_number) + "', "
	    "call_lc='"         + db->quote(call_lc) + "', "
	    "lc_class_number='" + db->quote(lc_class_number) + "', "
	    "lc_item_number='"	+ db->quote(lc_item_number) + "', "
	    "isbn='"		+ db->quote(isbn) + "', "
	    "lccn='"		+ db->quote(lccn) + "', "
	    "pub_place='"	+ db->quote(pub_place) + "', "
	    "pub_name='"	+ db->quote(pub_name) + "', "
	    "pub_dates='"	+ db->quote(pub_dates) + "', "
	    "extent='"		+ db->quote(extent) + "', "
	    "dimensions='"	+ db->quote(dimensions) + "', "
	    "other_phys='"	+ db->quote(other_phys) + "', "
	    "language='"	+ db->quote(language) + "', "
	    "source_id="	+ source_id + ", "
	    "source_query_id="	+ source_query_id + ", "
	    "informed="		+ (informed() ? '1' : '0') + tail;

    db->execute( query.c_str() );

    if (!db_id) db_id = db->last_insert_id();

    note* n = note_head;
    while (n) { n->commit( db_id ); n = n->next; }

    subject_heading* sub = subject_head;
    while (sub) { sub->commit( db_id ); sub = sub->next; }
}

void book::print()
{
    calculated_fields();

    printf( "\nauthor    : %s\n",  author.c_str() );
    printf( "title     : %s\n",  title.c_str() );
    printf( "call_dewey: %s\n",  call_dewey.c_str() );
    printf( "call_lc   : %s\n",  call_lc.c_str() );
    printf( "isbn      : %s\n",  isbn.c_str() );
    printf( "lccn      : %s\n",  lccn.c_str() );
    printf( "book_id   : %lu\n", db_id );

    subject_heading* cur_sub = subject_head;
    while (cur_sub)
    {
	printf( "subject: %s -- %s\n",
		cur_sub->topic.c_str(), cur_sub->form.c_str() );
	cur_sub = cur_sub->next;
    }

    note* cur_note = note_head;
    while (cur_note)
    {
	printf( "note: %s\n", cur_note->contents.c_str() );
	cur_note = cur_note->next;
    }
}

void book::refine( book& m )
{
    if (!m.author.empty())
    {
	author = m.author;
	if (!m.author_dates.empty()) author_dates = m.author_dates;
	if (!m.fuller_name.empty()) fuller_name = m.fuller_name;
    }
    if (!m.responsible.empty()) responsible = m.responsible;

    if (!m.title.empty())
    {
	title = m.title;
	if (!m.subtitle.empty())
	    subtitle = m.subtitle;
    }

    if (!m.lc_class_number.empty())
    {
	lc_class_number = m.lc_class_number;
	if (!m.lc_item_number.empty())
	    lc_item_number = m.lc_item_number;
    }

    if (!m.d_class_number.empty())
    {
	d_class_number = m.d_class_number;
	if (!m.d_item_number.empty())
	    d_item_number = m.d_item_number;
    }

    if (!m.isbn.empty() && isbn.empty()) isbn = m.isbn;
    if (!m.lccn.empty() && lccn.empty()) lccn = m.lccn;

    if (!m.extent.empty()) extent = m.extent;
    if (!m.dimensions.empty()) dimensions = m.dimensions;
    if (!m.other_phys.empty()) other_phys = m.other_phys;

    if (!m.pub_name.empty()) pub_name = m.pub_name;
    if (!m.pub_place.empty()) pub_place = m.pub_place;
    if (!m.pub_dates.empty()) pub_dates = m.pub_dates;

    if (!m.language.empty()) language = m.language;

    if (m.src) src = m.src;

    if (m.source_query && !source_query)
	source_query = m.source_query;

    if (m.note_head)
    {
	if (note_head) delete note_head;
	note_head = NULL;
	note* cur = m.note_head;

	while (cur)
	{
	    note_head = new note( note_head );
	    *note_head = *cur;
	    cur = cur->next;
	}
    }

    if (m.subject_head)
    {
	if (subject_head) delete subject_head;
	subject_head = NULL;
	subject_heading* cur = m.subject_head;

	while (cur)
	{
	    subject_head = new subject_heading( subject_head );
	    *subject_head = *cur;
	    cur = cur->next;
	}
    }

    calculated_fields();
}

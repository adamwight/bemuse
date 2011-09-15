#include <stdlib.h>
#include <stdio.h>

#include "note.h"

#include "mysql_db.h"

extern mysql_db* db;

note::note( note* next )
{
    this->next = next;
    db_id = 0;
}

note::~note()
{
    if (next) delete next;
}

unsigned long note::db_get_id()
{
    string query =
	"SELECT n FROM note "
	    "WHERE type='"     + db->quote(type) + "' AND "
		  "contents='" + db->quote(contents) + "'";

    MYSQL_RES *result = db->query( query.c_str() );

    if (MYSQL_ROW row = mysql_fetch_row( result ))
    {
	db_id = strtoul( row[0], NULL, 10 );
    }

    return db_id;
}

/* static */ note* note::db_get( unsigned long book_id )
{
    note* head = NULL;

    char query[128];
    snprintf( query, 128,
	"SELECT n, type, contents FROM note, note_link "
	    "WHERE book_id=%lu AND note_id=n", book_id );

    MYSQL_RES *result = db->query( query );

    if (MYSQL_ROW row = mysql_fetch_row( result ))
    {
	head = new note( head );

	head->db_id    = strtoul( row[0], NULL, 10 );
	head->type     = (row[1] ? row[1] : "");
	head->contents = (row[2] ? row[2] : "");
    }

    return head;
}

void note::commit( unsigned long book_id )
{
    string command;
    char tail[64] = "";

    if (get_id())
    {
	command = "UPDATE";
	snprintf( tail, 64, " WHERE n=%lu", db_id );
    }
    else
    {
	command = "INSERT INTO";
    }

    char id[16], nid[16];
    snprintf( id, 16, "%lu", book_id );
    snprintf( nid, 16, "%lu", db_id );

    string query =
	command + " note SET "
	    "type='"	    + db->quote(type) + "', "
	    "contents='"    + db->quote(contents) + "'" + tail;

    db->execute( query.c_str() );

    if (!db_id) db_id = db->last_insert_id();

    query = "REPLACE INTO note_link SET book_id=";
    query += id;
    query += ", note_id=";
    query += nid;
    db->execute( query.c_str() );
}

note& note::operator=(const note& n)
{
	contents = n.contents;
	type = n.type;
	db_id = n.db_id;

	return *this;
}

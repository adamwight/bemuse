/*
 * $Id: mysql_db.cc,v 1.14 2006/04/04 03:01:28 adamw Exp $
 */

#include <stdio.h>
#include <iostream>
#include <mysql/mysql.h>
#include <pthread.h>

#include "mysql_db.h"
#include "globals.h"

pthread_mutex_t conn_mutex;
MYSQL* mymy;

mysql_db::mysql_db()
{
    pthread_mutex_init(&conn_mutex, NULL);
}

mysql_db::~mysql_db()
{
    // XXX
    pthread_mutex_destroy(&conn_mutex);
}

void mysql_db::connect(const string& host, const string& user, const string& passwd, const string& db )
{
    this->host = host;
    this->user = user;
    this->passwd = passwd;
    this->db = db;

    mymy = mysql_init(NULL);

    mysql_real_connect(conn(),
	host.c_str(), user.c_str(), passwd.c_str(), db.c_str(),
	0, NULL, 0);

    int err;
    if ((err = mysql_errno(conn())))
	cerr << "mysql error on connect: " << mysql_error(conn()) << "\n";
}

string mysql_db::quote(const string& s)
{
    if (s.empty()) return s;

    char* buffer = new char[s.length() * 2 + 1]; // XXX real maximum is?
    mysql_real_escape_string( conn(), buffer, s.c_str(), s.length() );

    string ret( buffer );
    delete buffer;

    return ret;
}

void mysql_db::execute( const char* q )
{
    pthread_mutex_lock(&conn_mutex);
    bare_query(q);
    pthread_mutex_unlock(&conn_mutex);
}

void mysql_db::bare_query(const char* q)
{
    if (verbose > 0)
	cerr << q << "\n";

    mysql_query( conn(), q );
    
    int err;
    if ((err = mysql_errno( conn() )))
    {
	cerr << "mysql error on query, db instance " << id
	     << ": " << mysql_error( conn() ) << "\n";
	cerr << "query: " << q;
    }
}

MYSQL_RES* mysql_db::query(const char* q)
{
    pthread_mutex_lock(&conn_mutex);
    bare_query(q);
    MYSQL_RES* out = mysql_store_result(conn());
    pthread_mutex_unlock(&conn_mutex);
    return out;
}

string mysql_db::query_cell(const char* q)
{
    string out;
    MYSQL_RES* result = query(q);
    if (MYSQL_ROW row = mysql_fetch_row(result))
	out = row[0];
    mysql_free_result(result);
    return out;
}

// create another connection to the same db
mysql_db* mysql_db::clone()
{
    mysql_db* out = new mysql_db();
    out->connect(host, user, passwd, db);
    return out;
}

MYSQL* mysql_db::conn()
{
    return mymy;
}

unsigned long mysql_db::last_insert_id()
{
    return mysql_insert_id(conn());
}

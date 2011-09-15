/*
 * $Id: catalog_parallel.cc,v 1.8 2007/02/20 07:29:20 adamw Exp $
 */

#include <pthread.h>
#include <signal.h>
#include <iostream>
#include <stdio.h>

#include "catalog_z3950.h"
#include "catalog_parallel.h"
#include "globals.h"
#include "result.h"
#include "query.h"

const int MAX_RESULTS = 50;

struct thread_arg
{
    catalog_z3950* server;
    query* q;
};

catalog_parallel::catalog_parallel() : catalog()
{
    if (!mysql_thread_safe())
    {
	cerr << "forget about it, your libmysqlclient isn't thread-safe.\n";
	exit(1);
    }

    add_servers();
}

catalog_parallel::~catalog_parallel()
{
    for (unsigned i = 0; i < server_list.size(); i++)
	delete server_list[i];
    for (unsigned i = 0; i < children.size(); i++)
	delete children[i];
}

void catalog_parallel::add_servers()
{
    MYSQL_RES* result = db->query("SELECT n, host, port, db FROM z_server WHERE enabled");
    while (MYSQL_ROW row = mysql_fetch_row(result))
    {
	add(new server_z3950(atol(row[0]), row[1], atol(row[2]), row[3]));
    }
}

void catalog_parallel::add(server_z3950* s)
{
    server_list.push_back(new catalog_z3950(s));
}

int catalog_parallel::get_info( book& b )
{
    for (unsigned i = 0; i < server_list.size(); i++)
    {
	catalog_z3950* cur = server_list[i];
	forked_search(cur, db_query);
    }

    // time out, kill all, or bg some

    if (fast)
	wait_none();
    else
	wait_all();
    
    load_results();

    if (verbose)
	cout << "Got " << n_results() << " from all servers.\n";
    return n_results();
}

void catalog_parallel::load_results()
{
    char s[256];
    // TODO: sort by score
    snprintf(s, 256,
	"SELECT n FROM book WHERE source_query_id = %lu", db_query->get_id());
    MYSQL_RES* result = db->query(s);

    results.clear();
    while (MYSQL_ROW row = mysql_fetch_row(result))
    {
	results.add(atol(row[0]));
    }
}

void catalog_parallel::forked_search(catalog_z3950* server, query* q)
{
    pthread_t* child = new pthread_t;
    thread_arg* arg = new thread_arg;
    arg->server = server;
    arg->q = q;
    pthread_create(child, NULL, thread_entry, arg);
    children.push_back(child);
}

void catalog_parallel::wait_none()
{
    for (unsigned i = 0; i < children.size(); i++)
    {
	// How can we background threads?
	//pthread_detach(*children[i]);
    }
}

void catalog_parallel::wait_some()
{
    wait_all();
}

void catalog_parallel::wait_all()
{
    int total = 0;
    for (unsigned i = 0; i < children.size(); i++)
    {
	int* num;
	pthread_join(*children[i], (void**)&num);
	total += *num;
	delete num;
#if 0
	if (total >= MAX_RESULTS)
	{
	    while (i < children.size())
	    {
		pthread_kill(*children[i++], SIGKILL);
	    }
	}
#endif
    }
}

void* catalog_parallel::thread_entry(void* arg)
{
    thread_arg* t_arg = (thread_arg *)arg;

    t_arg->server->do_query(t_arg->q);
    int* num = new int(t_arg->server->n_results());
    delete t_arg;

    pthread_exit(num);
}

int catalog_parallel::serial_search(catalog_z3950* server, book& b)
{
    return server->get_info(b);
}

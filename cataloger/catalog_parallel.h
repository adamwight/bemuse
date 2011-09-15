/*
 * $Id: catalog_parallel.h,v 1.5 2006/04/04 02:57:46 adamw Exp $
 */

#ifndef CATALOG_PARALLEL_H
#define CATALOG_PARALLEL_H

#include <pthread.h>
#include <vector>

#include "catalog.h"

class catalog_parallel : public catalog
{
public:
    catalog_parallel();
    virtual ~catalog_parallel();

    virtual int get_info(book& b);

    void wait_all();
protected:
    void add_servers();
    void add(class server_z3950* s);
    void forked_search(class catalog_z3950* server, class query* q);
    int serial_search(class catalog_z3950* server, book& b);

    void wait_some();
    void wait_none();

    void load_results();

    static void* thread_entry(void* arg);

public:
    vector<class catalog_z3950*> server_list;
    vector<pthread_t*> children;
};

#endif

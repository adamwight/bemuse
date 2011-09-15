/*
 * $Id: source.h,v 1.2 2006/04/04 03:02:40 adamw Exp $
 */

#ifndef SOURCE_H
#define SOURCE_H

#include "db_entity.h"
#include <string>
using namespace std;

class source : public db_entity
{
public:
    source(unsigned long id) : db_entity(id) {}

    int is_defined() { return db_id != 0; }
    virtual string get_name();
};

#endif

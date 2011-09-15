#ifndef DB_ENTITY_H
#define DB_ENTITY_H

class db_entity
{
public:
    db_entity(unsigned long id = 0) : db_id(id) {}
    virtual ~db_entity() {}

    virtual unsigned long get_id()
    {
	if (db_id)
	    return db_id;
	else
	    return db_get_id();
    }

    virtual unsigned long db_get_id() { return 0; };
    virtual void commit() {};
    virtual int db_get() { return 0; };

    unsigned long db_id;
protected:
    //int modified;
};

#endif

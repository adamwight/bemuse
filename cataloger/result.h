/*
 * $Id: result.h,v 1.4 2006/04/04 03:01:56 adamw Exp $
 * todo: persistence, time, per-src errors?
 */

#ifndef RESULT_H
#define RESULT_H

#include <vector>
#include "book.h"

class result
{
public:
    vector<unsigned long> result_set;

    result() { };

    result(const result& src)
    {
	result_set = src.result_set;
    }

    void add(const result& more)
    {
	add_all(result_set, more.result_set);
    }

    void add(book& b)
    {
	add(b.get_id());
    }

    void add(unsigned long id)
    {
	result_set.push_back(id);
    }

    void clear()
    {
	vector<unsigned long> cleared;
	result_set = cleared;
    }

private:
    static void add_all(
	vector<unsigned long>& base, const vector<unsigned long> appendage)
    {
	for (unsigned i = 0; i < appendage.size(); i++)
	{
	    base.push_back(appendage[i]);
	}
    }
};

class result_store
{
public:
    static vector<book> get_results(const vector<unsigned long>& result_set)
    {
	vector<book> results;
	for (unsigned i = 0; i < result_set.size(); i++) {
	    results.push_back(book(result_set[i]));
	}
	return results;
    }

};

#endif

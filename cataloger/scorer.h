/*
 * $Id: scorer.h,v 1.1 2006/07/08 20:37:04 adamw Exp $
 */

#ifndef SCORER_H
#define SCORER_H

#include <string>
using namespace std;

class scorer
{
public:
    scorer(const char* col) : db_column(col) {}

    int score(book&);

protected:
    void store_score();

    book& b;
    int score;
    string db_column;
};

#endif

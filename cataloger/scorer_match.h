/*
 * $Id: scorer_match.h,v 1.1 2006/07/08 20:37:04 adamw Exp $
 */

#ifndef SCORER_MATCH_H
#define SCORER_MATCH_H

#include "scorer.h"
#include <string>
using namespace std;

class scorer_match : public scorer
{
    book* qb;
public:
    scorer_match() : scorer("score_match") {}

    int score(book&);
};

#endif

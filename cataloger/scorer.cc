#include "scorer.h"

#include <stdio.h>

void scorer::store_score(book& b, int score)
{
    char s[256];
    snprintf(s, 256, "UPDATE book SET %s=%d WHERE n=%u",
	db_column.c_str(), score, b.get_id())
    db->execute(s);
}

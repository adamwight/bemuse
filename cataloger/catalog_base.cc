/*
 * $Id: catalog_base.cc,v 1.3 2006/03/26 19:15:33 adamw Exp $
 */

#include "catalog_base.h"
#include "query.h"

int catalog_base::get_info( book& b )
{
    if (!b.call_lc.empty() && get_by_lc_call( b )) return n_results();
    if ((b.lccn.length() > 5) && get_by_lccn( b )) return n_results();
    if (!b.isbn.empty()	      && get_by_isbn( b )) return n_results();
    if (!b.title.empty())
    {
	if (!b.author.empty() && get_by_title_author( b )) return n_results();
	else if (			get_by_title( b )) return n_results();
    }

    return 0;
}

/*
 * $Id: scan_word.cc,v 1.2 2007/02/20 07:26:30 adamw Exp $
 */

#include <unistd.h>
#include <stdio.h>
#include <string.h>

#include "scan_word.h"

scan_word::scan_word()
{
    initcodetable();
}

void scan_word::decode(char *in, string& out)
{
    if (in == NULL)
	return;
    int length = strlen( in );
    char *ret = new char[length];
    char *txt = new char[length + 50];
    memset( txt, 0, length + 50 );
    memcpy( txt, in, length );

    int l = 4 - (length % 4);
    int j = 0;
    int k = 0;

    while (1)
    {
	unsigned char a[4], b[4], c[4];
	memset( a, 0, 4 );
	memset( b, 0, 4 );
	memset( c, 0, 4 );

	int n = 0;
	int x = 0;
	for (int i = 0; i < 4; i++)
	{
	    x++;
	    if (x > length)
	    {
		delete ret;
		delete txt;
		out = "";
		return;
	    }

	    if (codetable[(int)txt[j + 1]] & 0x80)
	    {
		i--;
		continue;
	    }
	    b[i] = codetable[(int)txt[j + i]];
	}

	n = ((b[0] << 6 | b[1]) << 6 | b[2]) << 6 | b[3];
	ret[k]   = (char)((n >> 16) ^ 67);
	ret[k+1] = (char)((n >> 8 & 255) ^ 67);
	ret[k+2] = (char)((n & 255) ^ 67);
	ret[k+3] = 0;

	j += 4;
	k += 3;

	if (j >= length)
	{
	    if (l != 4)
	    {
		ret[strlen( ret ) - l] = 0;
	    }

	    delete txt;
	    out = ret;
	    return;
	}
    }
}

void scan_word::initcodetable( )
{
    int i;

    for (i = 0; i < 256; i++) codetable[i] = 0x80;
    for (i = 'a'; i <= 'z'; i++) codetable[i] = (i - 'a');
    for (i = 'A'; i <= 'Z'; i++) codetable[i] = 26 + (i - 'A');
    for (i = '0'; i <= '9'; i++) codetable[i] = 52 + (i - '0');
    codetable[(int)'+'] = 62;
    codetable[(int)'/'] = 63;
}

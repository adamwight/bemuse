/*
 * $Id: scan_word.h,v 1.1 2006/01/23 08:56:32 adamw Exp $
 *
 * The decoding algorithm for cuecat words
 */

#ifndef SCAN_WORD_H
#define SCAN_WORD_H

#include <string>
using namespace std;

class scan_word
{
public:
    scan_word();

    void decode(char* in, string& out);
    void print( );

    string raw, serial, ctype, data, formatted;

private:
    void initcodetable();

    char codetable[256];
};

#endif

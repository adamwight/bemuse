/*
 * $Id: scandata.h,v 1.7 2007/02/20 07:26:56 adamw Exp $
 */

#ifndef SCANDATA_H
#define SCANDATA_H

#include "scan_word.h"

class scandata
{
public:
    scandata(const char*);

    bool valid();
    void print( );

    string isbn, ib5;

private:
    void parse_data();
    void parse_isbn();
    void ean_to_isbn();
    char checkdigit_ean(const char *ean);

    string raw, serial, code_type, data, formatted;
    char ean[14];
    bool is_valid;

    scan_word decoder;
};

#endif

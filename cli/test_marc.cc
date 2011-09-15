#include <fcntl.h>
#include <stdio.h>
#include <stdlib.h>

#include "marc.h"

void marc_read(marc& m, const char* path);

int test_marc8()
{
    marc m;

    marc_read(m, "../doc/marc8.marc-raw");
    m.print();
}

int test_utf8()
{
    marc m;

    marc_read(m, "../doc/utf8.marc-raw");
    m.print();
}

int test_text()
{
    marc m;

    //marc_read(m, "../doc/larousse-8859-1.marc");
    m.print();
}

void marc_read(marc& m, const char* path)
{
    int fd = open( path, O_RDONLY );
    if (fd == -1) perror( NULL );
    
    off_t size = lseek( fd, 0, SEEK_END );
    lseek( fd, 0, SEEK_SET );
    char* data = (char*)malloc( size + 1 );
    off_t nb = read( fd, data, size );
    if (nb == -1) perror( NULL );
    close( fd );

    m.parse_raw( data );
    free( data );
}


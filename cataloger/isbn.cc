#include "isbn.h"

char checkdigit_isbn( const char *isbn )
{
    int sum = 0;

    for (int i = 0; i < 9; i++) sum += (isbn[i] - '0') * (10 - i);
    sum %= 11;

    if (sum == 0) return ('0');
    else if (sum == 1) return ('X');
    else return ('0' + 11 - sum);
}

int valid_isbn( const char *isbn )
{
    return (isbn[9] == checkdigit_isbn( isbn ));
}

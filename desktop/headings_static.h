#include <string.h>
#include <regex.h>

typedef struct
{
    const char* regex;
    const char* title;
} shelf;

typedef struct
{
    const char* heading;
    const char* title;
} classification;

typedef struct
{
    const char* call_major_re;
    const shelf* headings;
    const classification* classifications;

    const char* get_heading(const char* call)
    {
        regex_t head_re;
        regcomp(&head_re, call_major_re, REG_EXTENDED);

        regmatch_t match[2];
        if (regexec(&head_re, call, 2, match, 0) != REG_NOMATCH)
        {
            int len = match[1].rm_eo - match[1].rm_so + 1;
            char* heading = (char*)malloc(len+1);
            memcpy(heading, call + match[1].rm_so, len);
            heading[len-1] = '\0';
            return lookup_heading(heading);
        }
        return NULL;
    }

    const char* lookup_heading(const char* heading)
    {
        for (int i = 0; classifications[i].heading; i++)
        {
            if (strcmp(classifications[i].heading, heading) == 0) {
                return classifications[i].title;
            }
        }
        return NULL;
    }
} headings_static;

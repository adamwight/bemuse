#include <string.h>
#include <set>

class toggle_set
{
    struct ltstr
    {
      bool operator()(const char* s1, const char* s2) const
      {
        return strcmp(s1, s2) < 0;
      }
    };
    set<const char*, ltstr> string_set;

    void print()
    {
        fprintf(stderr, "{ ");
        for (set<const char*,ltstr>::iterator i = string_set.begin();
            i != string_set.end(); i++)
        {
            fprintf(stderr, "\"%s\" ", *i);
        }
        fprintf(stderr, "}\n");
    }

public:
    void toggle(const char* s)
    {
        if (is_toggled(s) > 0)
        {
            char* mem = (char*) *string_set.find(s);
            string_set.erase(s);
            free(mem);
        }
        else
        {
            string_set.insert(strdup(s));
        }
    }

    bool is_toggled(const char *s)
    {
        return string_set.count(s) > 0;
    }
};

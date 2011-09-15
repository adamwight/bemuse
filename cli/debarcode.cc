#include <iostream>
#include "scandata.h"

int main(int argc, const char* argv[])
{
	for (string s; cin >> s ;)
	{
		scandata parsed(s.c_str());
		if (!parsed.valid())
			cout << s;
		else
			cout << parsed.isbn;
		if (parsed.ib5.length())
			cout << "-" << parsed.ib5;
		cout << endl;
	}
}

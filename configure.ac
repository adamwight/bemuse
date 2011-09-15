#TODO mysql, gtk
#                                               -*- Autoconf -*-
# Process this file with autoconf to produce a configure script.

#TODO include m4/

AC_PREREQ(2.61)
AC_INIT([bemuse], [0.2.0], [bemuse@ludd.net])
AC_CONFIG_SRCDIR([cataloger/catalog_z3950.h])

AC_PREFIX_DEFAULT(`pwd`/web)

AC_LANG(C++)

# Checks for programs.
AC_PROG_CXX
AC_PROG_CC
#AC_PROG_INSTALL
AC_PROG_MAKE_SET

# Checks for libraries.
AC_CHECK_LIB([pthread], [pthread_exit], [], AC_MSG_ERROR([Requires libpthread]))

AC_CHECK_HEADER([mysql/mysql.h], [], AC_MSG_ERROR([Requires mysql headers]))

# check for gtk2-devel, determines if we build the GUI programs
gtk2=false
AC_MSG_CHECKING(for gtk2)
AC_ARG_ENABLE(gtk2,
    AC_HELP_STRING([--enable-gtk2],[build the gtk2 gui]),
    [if test "x$enable_gtk2" = "xyes"; then
        AM_PATH_GTK_2_0([2.12.8], gtk2=true)
    fi])
if test "x$gtk2" = "xfalse"
then
    AC_MSG_RESULT([fail])
else
    AC_MSG_RESULT([ok])
    AC_CONFIG_FILES([desktop/Makefile])
fi

YAZ_INIT([threads],[3.0.30])
if test -z "$YAZLIB"; then
    AC_MSG_ERROR([YAZ development libraries missing])
fi

YAZPP_INIT([threads])
if test -z "$YAZPPLIB"; then
    AC_MSG_ERROR([YAZPP development libraries missing])
fi

# Checks for header files.
AC_HEADER_STDC
AC_CHECK_HEADERS([fcntl.h limits.h netdb.h netinet/in.h stdlib.h string.h sys/socket.h sys/time.h unistd.h pthread.h mysql/mysql.h])

AC_CHECK_FUNCS([strstr])

AC_CONFIG_FILES([Makefile
                 cataloger/Makefile
                 cli/Makefile])
AC_OUTPUT

if test "x$gtk" = "x0"
then
    AC_MSG_NOTICE([gtk2 not found, desktop gui will not be built])
fi

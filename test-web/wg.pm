#!/usr/bin/perl -w

use Socket;
use IO::Handle;

sub openhost
{
    my ($host, $port) = @_;
    my ($iaddr, $proto, $sin);

    $proto = getprotobyname( 'tcp' );
    $iaddr = gethostbyname( $host );
    $sin = sockaddr_in( $port, $iaddr );
    local *SOCK;
    socket( SOCK, PF_INET, SOCK_STREAM, $proto ) || die "socket: $!";
    connect( SOCK, $sin ) || die "connect: $!";

    return *SOCK;
}

sub wg
{
    my ($host, $doc) = @_;

    if ($host =~ m{http://([^/]+)(/.*$)})
    {
	$host = $1; $doc = $2;
    }

    my $sock = openhost( $host, 80 );
    send( $sock, <<"EOF", 0 );
GET $doc HTTP/1.0\r
Accept: image/gif, image/x-xbitmap, image/jpeg, image/pjpeg, application/vnd.ms-excel, application/msword, application/vnd.ms-powerpoint, application/x-comet, */*\r
User-Agent: Mozilla/4.0 (compatible; MSIE 5.5; Windows NT 4.0)\r
Host: $host\r
\r
EOF

    return $sock;
}

1;;

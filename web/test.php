<?
include( "common.php" );
?>
<html>
<head>
    <title>Batch Book Entry</title>
    <style TYPE="text/css">
	td {
	    font-family: arial, helvetica, sans-serif;
	}
    </style>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body bgcolor="#FFFFFF" text="#000000" link="#003366" vlink="#666666" alink="#003366">

<? sidebar_header(); ?>

about to run
<p>
<?
    $a = `/tmp/test arg1 arg2`;
    foreach (split( "\n", $a ) as $line) echo "line $line<br>";
    echo "got $a";
    $arg = "barf !a $a '\\\" what ? / ' '' \" ";
    echo "$arg<br>";
    echo escapeshellarg($arg) . "<br>";
?>
<p>
done

<? print_footer(); ?>

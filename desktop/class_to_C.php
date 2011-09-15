<?
set_include_path("../web/");

$call_type = $argv[1];
include_once("classifications.php");
//include_once("classifications_$call_type.php");
//include_once("classifications_lc.php");

?>/* FILE GENERATED FROM ../web/classifications.php */

shelf <?=$call_type?>_shelves[] =
 {
<?
foreach ($call_system->shelf_labels as $shelf => $re) {
?>  { "<?=$shelf?>", "<?=$re?>" },
<? }

?> { NULL, NULL} };
classification <?=$call_type?>_classifications[] =
 {
<?
foreach ($call_system->classifications as $head => $name) {
?>  { "<?=$head?>", "<?=$name?>" },
<? }

?>
 { NULL, NULL } };

static headings_static classifications_<?=$call_type?> = {
   "<?=trim($call_system->call_major_re,"/")?>",
   <?=$call_type?>_shelves, <?=$call_type?>_classifications
  };

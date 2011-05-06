<?
 /*
  *  +----------------------------------------------------------------------------------------------------------------------------+
  *  | EXAMPLE WIDGET                                                                                                             |
  *  | --------------                                                                                                             |
  *  | Sometimes a projects needs custom widgets. That is, widgets which are not part of an module but need to be used for one    |
  *  | particular project. You can invoke the widget by calling {widget file="custom/widget/exampleWidget.php"} in your template. |
  *  +----------------------------------------------------------------------------------------------------------------------------+
  */
$sutra  = sutra::get();
$result = $sutra->db->getArray("SELECT count(*) FROM `sutra_page`",false);
//echo $sutra->tpl->fetch("sometemplate.tpl");
echo "custom/widget/exampleWidget.php: there are {$result[0]['count(*)']} pages in the database";
?>

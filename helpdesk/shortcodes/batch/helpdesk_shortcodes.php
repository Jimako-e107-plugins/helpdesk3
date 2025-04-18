<?php
//include_once(e_HANDLER . 'shortcode_handler.php');
//$hdu_shortcodes = $tp->e_sc->parse_scbatch(__FILE__);
if (!defined('e107_INIT')) { exit; }

class helpdesk_shortcodes extends e_shortcode
{

	function sc_hdu_title()
  {
global $tp, $HELPDESK_PREF;
return $tp->toHTML($HELPDESK_PREF['hduprefs_title'], false, "no_make_clickable emotes_off");
  }


	function sc_hdu_messagetop()
  {
global $tp, $HELPDESK_PREF;
return $tp->toHTML($HELPDESK_PREF['hduprefs_messagetop'], true, 'no_make_clickable emotes_off');
}

	function sc_hdu_message()
  {
global $hdu_savemsg;
return $hdu_savemsg;
}

	function sc_hdu_phone()
  {
global $tp, $HELPDESK_PREF;
if(!empty($HELPDESK_PREF['hduprefs_phone']))
{
	return HDU_102 . ' ' . $tp->toHTML($HELPDESK_PREF['hduprefs_phone'], false, 'no_make_clickable emotes_off');
}
else
{
	return '&nbsp;';
}
}

	function sc_hdu_faq()
  {
global $tp, $HELPDESK_PREF;
if(!empty($HELPDESK_PREF['hduprefs_faq']))
{
return "<a href='" . $tp->toHTML($HELPDESK_PREF['hduprefs_faq']) . "' >" . HDU_207 . "</a>";
}
else
{
	return '&nbsp;';
}
}

	function sc_hdu_newticket()
  {
global $helpdesk_obj,$show;
if ($helpdesk_obj->hdu_poster)
{
	return "<a href ='".e_PLUGIN.HELPDESK_FOLDER . "/helpdesk.php?0.newticket.0' ><img src='./images/new.gif' style='border:0;' alt='' title='" . HDU_52 . "' /></a>";
}
else
{
	return;
}
}

	function sc_hdu_reports()
  {
global $helpdesk_obj,$from,$id;
if ($helpdesk_obj->hdu_super || $helpdesk_obj->hdu_technician)
{
    return "<a href ='".e_PLUGIN.HELPDESK_FOLDER . "/helpdesk.php?$from.repmenu.$id' ><img src='".e_PLUGIN.HELPDESK_FOLDER . "/images/print.gif' style='border:0;' alt='' title='" . HDU_101 . "' /></a>";
}
}

	function sc_hdu_filter()
  {
global $hdu_filtselect;
return $hdu_filtselect;
}

	function sc_hdu_gotorec()
  {
global $hdu_goto;
return "<input type ='text' name ='goto' maxlength ='5' value ='" . $hdu_goto . "' size ='10' class ='tbox' />";
}

	function sc_hdu_dofilter()
  {
return "<input type ='submit' class='button' style='border:0;' name ='filterit' value ='" . HDU_74 . "' alt='" . HDU_74 . "' title='" . HDU_74 . "' />";
}

	function sc_hdu_ticket_status()
  {
global $hdu_imgtag;
return $hdu_imgtag;
}

	function sc_hdu_ticket_id()
  { 
global $hdu_id;
return $hdu_id;
}

	function sc_hdu_ticket_summary()
  {
global $tp, $hdu_id, $hdu_summary,$from;
return "<a href ='".e_PLUGIN.HELPDESK_FOLDER . "/helpdesk.php?$from.show." . $hdu_id  . "' > " . $tp->toFORM($hdu_summary) . "</a>";
}

	function sc_hdu_ticket_posted($parm = null)
  {
global $helpdesk_obj, $hdu_datestamp;
if ($hdu_datestamp>0)
{
	return date($parm,$hdu_datestamp);
}
else
{
	return "";
}
}

	function sc_hdu_ticket_category()
  {
global $hducat_category, $tp;
return $tp->toHTML($hducat_category, false);
}

	function sc_hdu_ticket_poster()
  {
global $poster;
return $poster;
}

	function sc_hdu_ticket_resolution()
  {
global $tp, $hdures_resolution,$hdures_help;
if(!empty($hdures_help))
{
$text_to_pop=$tp->toFORM($hdures_help);
return "<span style='border-bottom: 3px double;' onmouseout=\"hdu_hideTooltip()\" onmouseover=\"hdu_showTooltip(event,'" .$text_to_pop . "');return false\">".$tp->toHTML($hdures_resolution, false)."</span>";
}
else
{
return $tp->toHTML($hdures_resolution, false);
}
}

	function sc_hdu_ticket_helpdesk()
  {
global $tp, $hdudesk_name;
return $tp->toHTML($hdudesk_name, false);
}

	function sc_hdu_messagebottom()
  {
global $tp, $HELPDESK_PREF;
return $tp->toHTML($HELPDESK_PREF['hduprefs_messagebottom'], false);
}

	function sc_hdu_rights()
  {
global $hdu_rights;
return $hdu_rights;
}

	function sc_hdu_nextprev()
  {
global $hdu_nextprev;
return $hdu_nextprev;
}

	function sc_hdu_prioritycolour()
  {
global $helpdesk_obj,$hdu_priority;
 return $helpdesk_obj->hduprefs_colours[$hdu_priority];
}

}
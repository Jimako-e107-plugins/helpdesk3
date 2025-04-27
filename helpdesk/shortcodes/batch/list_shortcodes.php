<?php
 
if (!defined('e107_INIT'))
{
	exit;
}

class plugin_helpdesk_list_shortcodes extends e_shortcode
{

	private $pluginPrefs = array();
	private $tp;

	function __construct()
	{
		$this->pluginPrefs = e107::pref('helpdesk');
		$this->tp = e107::getParser();
	}

	function sc_hdu_title()
	{

		return $this->tp->toHTML($this->pluginPrefs['hduprefs_title'], false, "no_make_clickable emotes_off");
	}


	function sc_hdu_messagetop()
	{

		return $this->tp->toHTML($this->pluginPrefs['hduprefs_messagetop'], true, 'no_make_clickable emotes_off');
	}

	function sc_hdu_message()
	{
		global $hdu_savemsg;
		return $hdu_savemsg;
	}

	function sc_hdu_phone()
	{
 
		if (!empty($this->pluginPrefs['hduprefs_phone']))
		{
			return HDU_102 . ' ' . $this->tp->toHTML($this->pluginPrefs['hduprefs_phone'], false, 'no_make_clickable emotes_off');
		}
//		else
//		{
			return '&nbsp;';
//		}
	}

	function sc_hdu_faq()
	{

		if (!empty($this->pluginPrefs['hduprefs_faq']))
		{
			return "<a href='" . $this->tp->toHTML($this->pluginPrefs['hduprefs_faq']) . "' >" . HDU_207 . "</a>";
		}
//		else
//		{
			return '&nbsp;';
//		}
	}

	function sc_hdu_newticket()
	{
		global $helpdesk_obj, $show;
/*
		var_dump (defined('BOOTSTRAP'));
		var_dump (deftrue('BOOTSTRAP'));
		if (deftrue('BOOTSTRAP'))
		{
			$img = e107::getParser()->toGlyph('fa-download', false);
			//	$img = '<i class="icon-download"></i>';
		}
*/


		if ($helpdesk_obj->hdu_poster)
		{
			return "<a class='btn btn-primary' href ='" . e_PLUGIN . HELPDESK_FOLDER . "/helpdesk.php?0.newticket.0' >".HDU_52."</a>";
			
//			return "<a href ='" . e_PLUGIN . HELPDESK_FOLDER . "/helpdesk.php?0.newticket.0' ><img src='./images/new.gif' style='border:0;' alt='' title='" . HDU_52 . "' /></a>";
		}
//		else
//		{
			return false;
//		}
	}

	function sc_hdu_reports()
	{
		global $helpdesk_obj, $from, $id;
		if ($helpdesk_obj->hdu_super || $helpdesk_obj->hdu_technician)
		{
			return "<a href ='" . e_PLUGIN . HELPDESK_FOLDER . "/helpdesk.php?$from.repmenu.$id' ><img src='" . e_PLUGIN . HELPDESK_FOLDER . "/images/print.gif' style='border:0;' alt='' title='" . HDU_101 . "' /></a>";
		}
	}

	function sc_hdu_filter()
	{
//		global $hdu_filtselect;
global $helpdesk_obj, $R1;
/*
		$hdu_filtselect = "
		<select name ='R1' class ='tbox' onchange='this.form.from.value=0;this.form.submit()'>
			<option value='all'" . ($R1 == "all"?" selected ='selected'":"") . " >" . HDU_187 . "</option>
			<option value='open'" . ($R1 == "open"?" selected ='selected'":"") . " >" . HDU_182 . "</option>
			<option value='closed'" . ($R1 == "closed"?" selected ='selected'":"") . " >" . HDU_183 . "</option>
			<option value='allocated'" . ($R1 == "allocated"?" selected ='selected'":"") . " >" . HDU_184 . "</option>
			<option value='unallocated'" . ($R1 == "unallocated"?" selected ='selected'":"") . " >" . HDU_185 . "</option>
			<option value='escalate'" . ($R1 == "escalate"?" selected ='selected'":"") . " >" . HDU_186 . "</option>";
	if (!$helpdesk_obj->hduprefs_posteronly || $helpdesk_obj->hdu_super || $helpdesk_obj->hdu_technician)
	{
		$hdu_filtselect .= "<option value='mine'" . ($R1 == "mine"?" selected ='selected'":"") . " >" . HDU_208 . "</option>";
	}
	$hdu_filtselect .= "</select>";

*/

$hdu_filtselect = '<label for="R1">'.HDU_77.'</label>';

	$array["all"]=HDU_187;
	$array["open"]=HDU_182;
	$array["closed"]=HDU_183;
	$array["allocated"]=HDU_184;
	$array["unallocated"]=HDU_185;
	$array["escalate"]=HDU_186;
	if (!$helpdesk_obj->hduprefs_posteronly || $helpdesk_obj->hdu_super || $helpdesk_obj->hdu_technician)
	{
		$array["mine"]=HDU_208;
	}

	$hdu_filtselect .= e107::getForm()->select('R1', $array, $R1, array("class"=>"tbox form-select w-25"));

//e107::js("inline", '$(".r1").on("change", function () {    this.form.from.value=0;this.form.submit();});');

$hdu_filtselect .= '
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var select = document.getElementById("r1");
    if (select) {
      select.addEventListener("change", function() {
        this.form.from.value=0;
		this.form.submit();
      });
    }
  });
</script>
';
		return $hdu_filtselect;
	}

	function sc_hdu_gotorec()
	{
		global $hdu_goto;
		return '<label for="goto">'.HDU_78."</label><input type ='text' name ='goto' id ='goto' maxlength ='5' value ='" . $hdu_goto . "' size ='10' class ='tbox' />";
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
		global   $hdu_id, $hdu_summary, $from;
		return "<a href ='" . e_PLUGIN . HELPDESK_FOLDER . "/helpdesk.php?$from.show." . $hdu_id  . "' > " . $this->tp->toFORM($hdu_summary) . "</a>";
	}

	function sc_hdu_ticket_posted($parm = null)
	{
		global $hdu_datestamp;
		if ($hdu_datestamp > 0)
		{
			return date($parm, $hdu_datestamp);
		}
//		else
//		{
			return "";
//		}
	}

	function sc_hdu_ticket_category()
	{
		global $hducat_category;
		return $this->tp->toHTML($hducat_category, false);
	}

	function sc_hdu_ticket_poster()
	{
		global $poster;
		return $poster;
	}

	function sc_hdu_ticket_resolution()
	{
		global   $hdures_resolution, $hdures_help;
		if (!empty($hdures_help))
		{
			$text_to_pop = $this->tp->toFORM($hdures_help);
			return "<span style='border-bottom: 3px double;' onmouseout=\"hdu_hideTooltip()\" onmouseover=\"hdu_showTooltip(event,'" . $text_to_pop . "');return false\">" . $this->tp->toHTML($hdures_resolution, false) . "</span>";
		}
//		else
//		{
			return $this->tp->toHTML($hdures_resolution, false);
//		}
	}

	function sc_hdu_ticket_helpdesk()
	{
		global   $hdudesk_name;
		return $this->tp->toHTML($hdudesk_name, false);
	}

	function sc_hdu_messagebottom()
	{

		return $this->tp->toHTML($this->pluginPrefs['hduprefs_messagebottom'], false);
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
		global $helpdesk_obj, $hdu_priority;
//		return $helpdesk_obj->hduprefs_colours[$hdu_priority];
		return $this->pluginPrefs['hduprefs_p'.$hdu_priority.'col'];
}

	
	function sc_hdu_prefscolours($parm = null)
	{
		return $this->tp->toHTML($this->pluginPrefs['hduprefs_p'.$parm.'col'], false, "no_make_clickable emotes_off");
	}
}

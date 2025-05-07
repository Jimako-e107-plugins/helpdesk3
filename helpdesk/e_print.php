<?php

class helpdesk_print // plugin-folder + '_print'
{
	public function render($id)
	{
		global $helpdesk_obj, $hdu_shortcodes,
		$hdupostername, $hdu_datestamp, $hdu_category, $hdu_summary, $hdu_tagno, $hdu_email, $hdu_resolution, $hdures_resolution, $hdu_description,
		$hdu_tech, $hdu_allocated, $hdu_closed, $hdu_hours, $hdu_fixcost, $hdu_hrate, $hdu_hcost, $hdu_distance, $hdu_fixother,
		$hdu_drate, $hdu_dcost, $hdu_eqptcost, $hdu_callout, $hduc_date, $hduc_postername, $hduc_comment, $hdu_priority, $hdu_savemsg, $hdu_totalcost,
		$hdupostername;
		
		$sql = e107::getdb();
		$tp = e107::getParser();
	
//		require_once(e_PLUGIN . HELPDESK_FOLDER . "/shortcodes/batch/list_shortcodes.php");
		$hdu_shortcodes = e107::getScBatch('show', 'helpdesk');
		if (!is_object($helpdesk_obj))
		{
			require_once(e_PLUGIN . HELPDESK_FOLDER . "/includes/helpdesk_class.php");
			$helpdesk_obj = new helpdesk;
		}
		if (!$helpdesk_obj->hdu_read)
		{
			exit();
		}
		$helpdesk_obj->hdu_print = true;
	 
		$HDU_PRINTTEMPLATE = e107::getTemplate('helpdesk', 'helpdesk_print');
//		var_dump($HDU_PRINTTEMPLATE);
/*		
		if (file_exists(e_THEME . "helpdesk_print_template.php"))
		{
			define(HDU_TEMPLATE, e_THEME . "helpdesk_print_template.php");
		}
		else
		{
			define(HDU_TEMPLATE, e_PLUGIN . HELPDESK_FOLDER . "/templates/helpdesk_print_template.php");
		}
*/	 
		$hdu_arg = "
	select * from #hdunit
			left join #hdu_categories on hdu_category=hducat_id
			left join #hdu_helpdesk on hducat_helpdesk=hdudesk_id
			left join #hdu_resolve on  hdu_resolution=hdures_id
			where hdu_id = $id";
		$sql->gen($hdu_arg);
		extract($sql->fetch());
		$hdu_temp = explode(".", $hdu_poster, 2);
		$hdupostername = $hdu_temp[1];
//		var_dump($hdu_summary);
//		require_once(HDU_TEMPLATE);
		$hdu_text .= $tp->parsetemplate($HDU_PRINTTEMPLATE['ticket'], false, $hdu_shortcodes);
		$sql->select("hdu_comments", "*", "where hduc_ticketid=$id order by hduc_date", "nowhere");
		while ($hdu_comrow = $sql->fetch())
		{
			extract($hdu_comrow);
			$hdu_temp = explode(".", $hduc_poster, 2);
			$hduc_postername = $hdu_temp[1];
			$hdu_text .= $tp->parsetemplate($HDU_PRINTTEMPLATE['detail'], false, $hdu_shortcodes);
		} // while
		$hdu_text .= $tp->parsetemplate($HDU_PRINTTEMPLATE['footer'], false, $hdu_shortcodes);
		return $hdu_text;
		}	
}
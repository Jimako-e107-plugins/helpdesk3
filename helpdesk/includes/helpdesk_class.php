<?php

e107::lan("helpdesk", true, true);  //fix me 
e107::lan("helpdesk", false, true); //fix me


$admin_style_fix = '
tr td.forumheader3:nth-of-type(2) {
    display: flex;
    align-items: center;
    gap: 10px; /* Adjust spacing between elements */
    flex-wrap: wrap; /* Allows wrapping if needed */
}

tr td.forumheader3:nth-of-type(2) input[type="radio"],
tr td.forumheader3:nth-of-type(2) input[type="checkbox"] {
    margin-right: 5px; /* Space between input and label */
}

tr td.forumheader3:nth-of-type(2) label {
    margin-right: 15px; /* Space between label groups */
}

';

if(defset("ADMIN_AREA")) {
	e107::css('inline', $admin_style_fix);
}



require_once(e_HANDLER . "userclass_class.php");
class helpdesk
{
    var $hdu_read = false; // allowed to access and read
    var $hdu_super = false; // supervisor
    var $hdu_technician = false;
    var $hdu_poster = false; //allowed to post tickets
    var $hduprefs_autoclosedays = 0; // auto close tickets after days
    var $hduprefs_autocloseres = 0; // the resolution/status when auto closed
  //  var $hduprefs_colours = array(); // colours used for priority
    var $hduprefs_rows = 10;
    var $hduprefs_memberof = "";
    var $hduprefs_posteronly = false;
 
    var $hdu_new = false;
    var $hdu_edit = false;
    var $hdu_quick = false;
    var $hduprefs_showassettag = false;
    var $hdu_showemail = false;
    var $hdu_showfixes = false;
    var $hduprefs_showfinance = false;
    var $hduprefs_defaultres = 0;
    var $hduprefs_callout = 0;
    var $hduprefs_autoassign = false;
    var $hduprefs_assigned = 0;
 //   var $hduprefs_statcloses = false;
    var $hduprefs_reopen = false;
    var $hduprefs_mailuser = 0;
    var $hduprefs_mailhelpdesk = 0;
    var $hduprefs_sendas = 0;
    var $hduprefs_pmfrom = 0;
    var $hduprefs_emailfrom = "";
    var $hduprefs_usersubject = "";
    var $hduprefs_userupsubject = "";
    var $hduprefs_usertext = "";
    var $hduprefs_updateuser = "";
    var $hduprefs_helpdeskemail = "";
    var $hduprefs_helpupsubject = "";
    var $hduprefs_techniciansubject = "";
    var $hduprefs_helpdesktext = "";
    var $hduprefs_updatehelpdesk = "";
    var $hduprefs_helpdesksubject = "";
    var $hduprefs_updatetechnician = "";
    var $hduprefs_techniciantext = "";
    var $hduprefs_menutitle = "";
    var $hduprefs_title = "";
    var $hduprefs_mailpdf = false;
    var $hduprefs_distancerate = 0;
    var $hduprefs_hourlyrate = 0;
    var $hduprefs_closestat = 0;
    var $hduprefs_assignto = 0;
    var $hduprefs_escalatedays = 0;
    var $hduprefs_allread = false;
    var $hdu_print = false;
    var $hduprefs_showfixes = false;
    var $hduprefs_escalateon = 0;

	var $hdu_memberof = ""; //php 8 warning

	public $pluginPrefs = array();
	private $tp;
	private $sql;
    function __construct()
    {
//        global $sql;

		$this->pluginPrefs = e107::pref('helpdesk');
		$this->tp = e107::getParser();
		$this->sql = e107::getDb();
 
        $this->hduprefs_posteronly = ($this->pluginPrefs['hduprefs_posteronly'] == 1);
/*
        $this->hduprefs_colours = array("1" => $this->pluginPrefs['hduprefs_p1col'],
            "2" => $this->pluginPrefs['hduprefs_p2col'],
            "3" => $this->pluginPrefs['hduprefs_p3col'],
            "4" => $this->pluginPrefs['hduprefs_p4col'],
            "5" => $this->pluginPrefs['hduprefs_p5col']);
            */
        // is this person a technician in any helpdesk
        $this->sql->select("hdu_helpdesk", "hdudesk_id", "where find_in_set(hdudesk_class,'" . USERCLASS_LIST . "')", "nowhere", false);
        while ($hdu_row = $this->sql->fetch())
        {
            // get the helpdesks this person belongs to
            $this->hdu_memberof .= $hdu_row['hdudesk_id'] . ",";
        } // while
        $this->hdu_technician = (empty($this->hdu_memberof)?false:true);
        $this->hdu_super = check_class($this->pluginPrefs['hduprefs_supervisorclass']);
        // all in read class, supervisor or technicians can access
        $this->hdu_poster = check_class($this->pluginPrefs['hduprefs_postclass']) || $this->hdu_super || $this->hdu_technician;
        $this->hdu_read = check_class($this->pluginPrefs['hduprefs_userclass']) || $this->hdu_poster;
        $this->hduprefs_autoclosedays = $this->pluginPrefs['hduprefs_autoclosedays'];
        $this->hduprefs_autocloseres = $this->pluginPrefs['hduprefs_autocloseres'];
        $this->hduprefs_rows = $this->pluginPrefs['hduprefs_rows'];
        $this->hduprefs_showassettag = $this->pluginPrefs['hduprefs_showassettag'] == 1;
        $this->hduprefs_showfixes = $this->pluginPrefs['hduprefs_showfixes'] == 1;
        $this->hduprefs_autoassign = $this->pluginPrefs['hduprefs_autoassign'] == 1;
 
        $this->hduprefs_reopen = $this->pluginPrefs['hduprefs_reopen'] == 1;
        $this->hduprefs_mailpdf = $this->pluginPrefs['hduprefs_mailpdf'] == 1;
        $this->hduprefs_allread = $this->pluginPrefs['hduprefs_allread'] == 1;
 
        // if show finance and supervisor or technician
        // or show finance and show to users
        if (($this->pluginPrefs['hduprefs_showfinance'] == 1 && ($this->hdu_super || $this->hdu_technician)) || ($this->pluginPrefs['hduprefs_showfinance'] == 1 && $this->pluginPrefs['hduprefs_showfinusers'] == 1))
        {
            $this->hduprefs_showfinance = true;
        }
		$this->hduprefs_defaultres = $this->pluginPrefs['hduprefs_defaultres'];
		$this->hduprefs_mailuser         = $this->pluginPrefs['hduprefs_mailuser'];
		$this->hduprefs_helpdeskemail    = $this->pluginPrefs['hduprefs_helpdeskemail'];
		$this->hduprefs_emailfrom        = $this->pluginPrefs['hduprefs_emailfrom'];
		$this->hduprefs_usersubject      = $this->pluginPrefs['hduprefs_usersubject'];
		$this->hduprefs_userupsubject    = $this->pluginPrefs['hduprefs_userupsubject'];
		$this->hduprefs_usertext         = $this->pluginPrefs['hduprefs_usertext'];
		$this->hduprefs_updateuser       = $this->pluginPrefs['hduprefs_updateuser'];
		$this->hduprefs_mailhelpdesk     = $this->pluginPrefs['hduprefs_mailhelpdesk'];
		$this->hduprefs_helpupsubject    = $this->pluginPrefs['hduprefs_helpupsubject'];
		$this->hduprefs_techniciansubject = $this->pluginPrefs['hduprefs_techniciansubject'];
		$this->hduprefs_helpdesktext     = $this->pluginPrefs['hduprefs_helpdesktext'];
		$this->hduprefs_updatehelpdesk   = $this->pluginPrefs['hduprefs_updatehelpdesk'];
		$this->hduprefs_helpdesksubject  = $this->pluginPrefs['hduprefs_helpdesksubject'];
		$this->hduprefs_updatetechnician = $this->pluginPrefs['hduprefs_updatetechnician'];
		$this->hduprefs_techniciantext   = $this->pluginPrefs['hduprefs_techniciantext'];
		$this->hduprefs_sendas           = $this->pluginPrefs['hduprefs_sendas'];
		$this->hduprefs_pmfrom           = $this->pluginPrefs['hduprefs_pmfrom'];
		$this->hduprefs_title = $this->pluginPrefs['hduprefs_title'];
		$this->hduprefs_callout = $this->pluginPrefs['hduprefs_callout'];
		$this->hduprefs_distancerate = $this->pluginPrefs['hduprefs_distancerate'];
		$this->hduprefs_hourlyrate = $this->pluginPrefs['hduprefs_hourlyrate'];
		$this->hduprefs_menutitle = $this->pluginPrefs['hduprefs_menutitle'];
		$this->hduprefs_closestat = $this->pluginPrefs['hduprefs_closestat'];
		$this->hduprefs_assignto = $this->pluginPrefs['hduprefs_assignto'];
		$this->hduprefs_assigned = $this->pluginPrefs['hduprefs_assigned'];
		$this->hduprefs_escalatedays = $this->pluginPrefs['hduprefs_escalatedays'];
		$this->hduprefs_escalateon = $this->pluginPrefs['hduprefs_escalateon'];

    }
   
    // **********************************************************************************************
    // *
    // *	Function	:	auto_close()
    // *
    // *	Parameters	:
    // *
    // *	Returns		:	void
    // *
    // *	Description	:	Processes auto close on any open tickets
    // *
    // *
    // **********************************************************************************************
    function auto_close()
    {
//        global $sql;
        if ($this->hduprefs_autoclosedays > 0)
        {
            // if we do default close then check for last activity and if more than hdu_defclose days ago
            // close the ticket.  Reopening restarts the counter.
            $hdu_timecheck = time() - ($this->hduprefs_autoclosedays * 86400);
            $hdu_args = "hdu_closed='" . time() . "', hdu_resolution ='" . $this->hduprefs_autocloseres . "' where hdu_lastchanged < '$hdu_timecheck' and hdu_closed ='0' ";
            $this->sql->update("hdunit", $hdu_args, false);
        }
    }
    // **********************************************************************************************
    // *
    // *	Function	:	helpdesk_cache_clear()
    // *
    // *	Parameters	:
    // *
    // *	Returns		:	void
    // *
    // *	Description	:	Clear any helpdesk caches
    // *
    // *
    // **********************************************************************************************
    function helpdesk_cache_clear()
    {
		e107::getCache()->clear("nq_helpdesk");
  
    }

    // **********************************************************************************************
    // *
    // *	Function	:	hdu_makedate($hdu_cal_date)
    // *
    // *	Parameters	:	$hdu_cal_date
    // *
    // *	Returns		:	void
    // *
    // *	Description	:	Make a date from calendar format
    // *
    // *
    // **********************************************************************************************
    // function hdu_makedate($hdu_cal_date)
    // {
    // global $pref;
    // if ($hdu_cal_date > 0)
    // {
    // switch ($pref['eventguide_dateformat'])
    // {
    // case 1:
    // $hdu_cal_retval = date("m-d-Y", $hdu_cal_date);
    // break;
    // case 2:
    // $hdu_cal_retval = date("Y-m-d", $hdu_cal_date);
    // break;
    // case 0:
    // default:
    // $hdu_cal_retval = date("d-m-Y", $hdu_cal_date);
    // } // switch
    // }
    // else
    // {
    // $hdu_cal_retval = 0;
    // }
    // return $hdu_cal_retval;
    // }

    function hdu_indate($hdu_cal_date)
    {
    global $pref;
    if (!empty($hdu_cal_date))
    {
    $hdu_cal_tmp = explode("-", $hdu_cal_date);
    switch ($pref['eventguide_dateformat'])
    {
    case 1:
    $hdu_cal_retval = mktime(0, 0, 0, $hdu_cal_tmp[0], $hdu_cal_tmp[1], $hdu_cal_tmp[2]);
    break;
    case 2:
    $hdu_cal_retval = mktime(0, 0, 0, $hdu_cal_tmp[1], $hdu_cal_tmp[2], $hdu_cal_tmp[0]);
    break;
    case 0:
    default:
    $hdu_cal_retval = mktime(0, 0, 0, $hdu_cal_tmp[1], $hdu_cal_tmp[0], $hdu_cal_tmp[2]);
    } // switch
    }
    else
    {
    $hdu_cal_retval = 0;
    }
    return $hdu_cal_retval;
    }


    // **********************************************************************************************
    // *
    // *	Function	:	display_priority()
    // *
    // *	Parameters	:	void
    // *
    // *	Returns		:	void
    // *
    // *	Description	:	Return a table with priority key
    // *
    // *
    // **********************************************************************************************
    /*
    function display_priority() // To be removed after templatization of this output....
    {
        $retval = "
	<table style='" . USER_WIDTH . "' >
		<tr>
			<td style='text-align:Left;'>" . HDU_190 . "&nbsp;
	" . HDU_191 . " <img src='" . e_PLUGIN . HELPDESK_FOLDER . "/images/green.gif' style='border:0;' alt='" . HDU_191 . "' title='" . HDU_191 . "' />&nbsp;
	" . HDU_192 . " <img src='" . e_PLUGIN . HELPDESK_FOLDER . "/images/yellow.gif' style='border:0;' alt='" . HDU_192 . "' title='" . HDU_192 . "' />&nbsp;
	" . HDU_193 . " <img src='" . e_PLUGIN . HELPDESK_FOLDER . "/images/red.gif' style='border:0;' alt='" . HDU_193 . "' title='" . HDU_193 . "' />
			</td>
			<td style='text-align:right;'>" . HDU_189 . "&nbsp; </td>
			<td style='text-align:center; width:20px; border: #C3BDBD 1px solid; background-color: " . $this->hduprefs_colours[1] . "'>1</td>
    		<td style='text-align:center; width:20px; border: #C3BDBD 1px solid; background-color: " . $this->hduprefs_colours[2] . "'>2</td>
    		<td style='text-align:center; width:20px; border: #C3BDBD 1px solid; background-color: " . $this->hduprefs_colours[3] . "'>3</td>
    		<td style='text-align:center; width:20px; border: #C3BDBD 1px solid; background-color: " . $this->hduprefs_colours[4] . "'>4</td>
    		<td style='text-align:center; width:20px; border: #C3BDBD 1px solid; background-color: " . $this->hduprefs_colours[5] . "'>5</td>
    	</tr>
	</table>";

        return $retval;
    }
    */
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getstatussel($hdu_res = 0)
    // *
    // *	Parameters	:	$hdu_res integer - current resolution
    // *
    // *	Returns		:	void
    // *
    // *	Description	:	Return a select object with list of resolutions / status
    // *
    // *
    // **********************************************************************************************
    function hdu_getstatussel($hdu_res = 0)
    {
//        global $sql;
        if ($hdu_res == 0)
        {
            $hdu_res = $this->hduprefs_defaultres;
        }
        $retval = "
		<select class='tbox'  onchange=\"changed()\" name='hdu_resolution'>";
        if ($this->sql->select("hdu_resolve", "hdures_id,hdures_resolution", " order by hdures_resolution", "nowhere", false))
        {
            while ($hdu_catrow = $this->sql->fetch())
            {
                extract($hdu_catrow);
                $retval .= "<option value='$hdures_id' " .
                ($hdures_id == $hdu_res?"selected='selected'":"") . ">" . $this->tp->toFORM($hdures_resolution) . "</option>";
            }
        }
        $retval .= "</select>";
        return $retval;
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_statcloses($hdu_id = 0)
    // *
    // *	Parameters	:	$hdu_id integer - the selected status/resolution
    // *
    // *	Returns		:	boolean - true this closes - false doesn't close it.
    // *
    // *	Description	:	Return a whether this status closes the ticket
    // *
    // *
    // **********************************************************************************************
    function hdu_statcloses($hdu_id = 0)
    {
//        global $sql;
        $retval = false;
        if ($this->sql->select("hdu_resolve", "where hdures_closed", "hdures_id='$hdu_id'", 'nowhere', false))
        {
            $hdu_row = $this->sql->fetch();
            if ($hdu_row['hdures_closed'] == 1)
            {
                $retval = true;
            }
        }
        return $retval;
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getfixcost($hdu_fixid = 0)
    // *
    // *	Parameters	:	$hdu_id integer - the id of the fix
    // *
    // *	Returns		:	number - the value of the fix or false if not found
    // *
    // *	Description	:	Return the amount that this fix costs
    // *
    // *
    // **********************************************************************************************
    function hdu_getfixcost($hdu_fixid = 0)
    {
//        global $sql;
        // Get the fix cost for the fix if a fix is selected and there is no fix cost entered on the form
        if ($this->sql->select("hdu_fixes", "hdufix_fixcost", "where hdufix_id = '" . $hdu_fixid . "'", 'nowhere', false))
        {
            $hdu_row = $this->sql->fetch() ;
            return $this->tp->toFORM($hdu_row['hdufix_fixcost']);
        }
        else
        {
            return false;
        }
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getcat($hdu_id)
    // *
    // *	Parameters	:	$hdu_id integer - the id of the category
    // *
    // *	Returns		:	number - the value of the fix or false if not found
    // *
    // *	Description	:	Return the amount that this fix costs
    // *
    // *
    // **********************************************************************************************
    function hdu_getcat($hdu_id)
    {
//        $hdu_repdb = new DB;
//        if ($hdu_repdb->select("hdu_categories", "where hducat_category", "hducat_id='$hdu_id'", 'nowhere', false))
        if ($this->sql->select("hdu_categories", "where hducat_category", "hducat_id='$hdu_id'", true, false))
        {
//            $hdu_reprow = $hdu_repdb->fetch();
            $hdu_reprow = $this->sql->fetch();

            return $hdu_reprow['hducat_category'];
        }
        else
        {
            return HDU_256; //No category defined
        }
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getstat($hdu_id)
    // *
    // *	Parameters	:	$hdu_id integer - the id of the status
    // *
    // *	Returns		:	number - the value of the fix or false if not found
    // *
    // *	Description	:	Return the amount that this fix costs
    // *
    // *
    // **********************************************************************************************
    function hdu_getstat($hdu_id)
    {
//        $hdu_repdb = new DB;
//        if ($hdu_repdb->select("hdu_resolve", " hdures_resolution", "where hdures_id='$hdu_id'", 'nowhere', false))
        if ($this->sql->select("hdu_resolve", " hdures_resolution", "where hdures_id='$hdu_id'", true, false))
        {
//            $hdu_reprow = $hdu_repdb->fetch();
            $hdu_reprow = $this->sql->fetch();
            return $hdu_reprow['hdures_resolution'];
        }
        else
        {
            return HDU_214; // no status defined
        }
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_get_colours()
    // *
    // *	Parameters	:	void
    // *
    // *	Returns		:	array - of colours (#000000)
    // *
    // *	Description	:	Return array of colour codes
    // *
    // *
    // **********************************************************************************************
    function hdu_get_colours()
    {
        global $hduprefs_p1col, $hduprefs_p2col, $hduprefs_p3col, $hduprefs_p4col, $hduprefs_p5col;
        $retval = array($hduprefs_p1col, $hduprefs_p2col, $hduprefs_p3col, $hduprefs_p4col, $hduprefs_p5col);
        return $retval;
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_gethemail($hdu_gete)
    // *
    // *	Parameters	:	integer - id of the helpdesk
    // *
    // *	Returns		:	string - email address
    // *
    // *	Description	:	Return email address for the helpdesk
    // *
    // *
    // **********************************************************************************************
    function hdu_gethemail($hdu_gete)
    {
//        $hdu_gete_db = new DB;
        // get from the category which helpdesk is using it and then get the email address:
        $hdu_gete_args = "select hdudesk_email from #hdu_helpdesk left join #hdu_categories on hducat_helpdesk=hdudesk_id
		where hducat_id='{$hdu_gete}'";
//        if ($hdu_gete_db->gen($hdu_gete_args, false))
        if ($this->sql->gen($hdu_gete_args, false))
        {
//            $hdu_gete_row = $hdu_gete_db->fetch();
            $hdu_gete_row = $this->sql->fetch();

            return $hdu_gete_row['hdudesk_email'];
        }
        else
        {
            return "";
        }
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getuserselect($hdu_id)
    // *
    // *	Parameters	:	integer - $hdu_id current id
    // *
    // *	Returns		:	string - select with list of users
    // *
    // *	Description	:	Return select with list of users
    // *
    // *
    // **********************************************************************************************
    function hdu_getuserselect($hdu_id = 0)
    {
//        $hdu_seldb = new DB;
        $retval = "<select name='hduposterqname' class='tbox'><option value='0'>" . HDU_136 . "</option>";
//        if ($hdu_seldb->select("user", "where user_id,user_name", 'nowhere', false))
        if ($this->sql->select("user", "where user_id,user_name", true, false))
        {
//            while ($hdu_selrow = $hdu_seldb->fetch())
            while ($hdu_selrow = $this->sql->fetch())
            {
                extract($hdu_selrow);
                $retval .= "<option value='{$user_id}' " . ($hdu_id == $user_id?"selected'selected'":"") . " >{$user_name}</option>";
            } // while
        }
        $retval .= "</select>";
        return $retval;
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getuseremail($hdu_userid = 0)
    // *
    // *	Parameters	:	integer - $hdu_userid user's id
    // *
    // *	Returns		:	string - users email address
    // *
    // *	Description	:	Return email address for specified user
    // *
    // *
    // **********************************************************************************************
    function hdu_getuseremail($hdu_userid = 0)
    {
//        $hdu_userdb = new DB;
//        $hdu_userdb->select("user", "user_email", "where user_id='$hdu_userid'", 'nowhere', false);
//        $hdu_row = $hdu_userdb->fetch();
        $this->sql->select("user", "user_email", "where user_id='$hdu_userid'", true, false);
        $hdu_row = $this->sql->fetch();

        return $hdu_row['user_email'];
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_getposterdetails($hdu_userid = 0)
    // *
    // *	Parameters	:	integer - $hdu_userid user's id
    // *
    // *	Returns		:	string - users email address
    // *
    // *	Description	:	Return email address for specified user
    // *
    // *
    // **********************************************************************************************
    function hdu_getposterdetails($hdu_userid = 0)
    {
//        $hdu_userdb = new DB;
//        if ($hdu_userdb->select("user", "user_name", "where user_id='$hdu_userid'", 'nowhere', false))
        if ($this->sql->select("user", "user_name", "where user_id='$hdu_userid'", true, false))
        {
//            $hdu_row = $hdu_userdb->fetch();
            $hdu_row = $this->sql->fetch();
            extract($hdu_row);

            return $hdu_userid . ".$user_name";
        }
        else
        {
            return false;
        }
    }
    // // **********************************************************************************************
    // // *
    // // *	Function	:	hdu_getposterdetails($hdu_userid = 0)
    // // *
    // // *	Parameters	:	integer - $hdu_userid user's id
    // // *
    // // *	Returns		:	string - users email address
    // // *
    // // *	Description	:	Return email address for specified user
    // // *
    // // *
    // // **********************************************************************************************
    // function hdu_gettechemail($hdu_tech_id)
    // {
    // $hdu_get_db = new DB;
    // if ($hdu_get_db->db_Select("user", "user_email", "where user_id ='$hdu_tech_id'", 'nowhere', false))
    // {
    // $hdu_get_row = $hdu_get_db->db_Fetch() ;

    // $retval = $hdu_get_row['user_email'];
    // }
    // else
    // {
    // $retval = false;
    // }
    // return $retval;
    // }
    // **********************************************************************************************
    // *
    // *	Function	:	post_comment
    // *
    // *	Parameters	:	void
    // *
    // *	Returns		:	void
    // *
    // *	Description	:	Posts a comment
    // *
    // *
    // **********************************************************************************************
    function post_comment()
    {
//        global $sql;
        $hdu_id = intval($_POST['id']);

        if (!empty($_POST['hduc_comment']))
        {
            $hduc_args = "
		'0',
		'$hdu_id',
		'" . USERID . "." . $this->tp->toDB(USERNAME) . "',
		'" . USERID . "',
		'" . time() . "',
		'0',
		'" . $this->tp->toDB($_POST['hduc_comment']) . "'";
            $hduc = $this->sql->insert("hdu_comments", $hduc_args);
			$hduc_msg = $this->tp->lanVars(HDU_92, array($hdu_id));
        }
        $this->sql->select("hdunit", "*", "where hdu_id = $hdu_id", "nowhere");
        $hdu_row = $this->sql->fetch();
        extract($hdu_row);
        // print $hdu_tech . "AL";
        // if ($hdu_tech > 0)
        // {
        // if ($hduprefs_assigned > 0)
        // {
        // $hdu_allocon = ",hdu_resolution = $hduprefs_assigned";
        // }
        // else
        // {
        // $hdu_allocon = ",hdu_resolution = $hduprefs_defaultres";
        // }
        // }
        if ($this->hdu_technician || $this->hdu_super)
        {
            // maybe also need to check helpdesk class too!!!!
            // If technician or supervisor then set return to 1 to show comment is from helpdesk and ensure closed is taken off
            $this->sql->update("hdunit", "hdu_return = '1',hdu_lastcomment='" . time() . "',hdu_lastchanged='" . time() . "' $hdu_allocon where hdu_id = '$hdu_id'");
        }
        else
        {
            $this->sql->update("hdunit", "hdu_return = '0',hdu_lastcomment='" . time() . "',hdu_lastchanged='" . time() . "' $hdu_allocon where hdu_id = '$hdu_id'");
        }
    }
    // **********************************************************************************************
    // *
    // *	Function	:	delete_ticket($id)
    // *
    // *	Parameters	:	integer - $id ID of ticket to delete
    // *
    // *	Returns		:	string - form to confirm deletion
    // *
    // *	Description	:	Create a form to confirm deletion of a ticket
    // *
    // *
    // **********************************************************************************************
    function delete_ticket($id)
    {
        global $from, $hdu_id;
//        require(HDU_THEME);
        $hdu_id = $id;
        $hdu_retval = "
		<form id='hdu_delform' method='post' action='" . e_SELF . "' >
		<div>
			<input type='hidden' name='id' value='$hdu_id' />
			<input type='hidden' name='from' value='$from' />
			<input type='hidden' name='hdu_aaction' value='list' />
		</div>";
        $HDU_DELETE = e107::getTemplate('helpdesk', 'helpdesk_delete');
        $hdu_shortcodes = e107::getScBatch('delete', 'helpdesk');
        if ($this->hdu_super)
        {
            $hdu_retval .= $this->tp->parseTemplate($HDU_DELETE['ok'], false, $hdu_shortcodes);
        }
        else
        {
            $hdu_retval .= $this->tp->parseTemplate($HDU_DELETE['notok'], false, $hdu_shortcodes);
        }
        $hdu_retval .= "</form>";
        return $hdu_retval;
    }
    // **********************************************************************************************
    // *
    // *	Function	:	show($hdu_showid = 0)
    // *
    // *	Parameters	:	integer - $hdu_showid ID of ticket to show
    // *
    // *	Returns		:	string - form to show/edit a ticket
    // *
    // *	Description	:	Create a form to show/edit a ticket
    // *
    // *
    // **********************************************************************************************
    function show($hdu_showid = 0)
    {
        // Create required objects
        // TO DO
        // Check if user is technicial for this ticket - if not then don't allow to do things to it.
        // *
        global $helpdesk_obj, $hdu_posterid, $hdu_sel_users,
        $hdupostername, $hdu_datestamp, $hdu_category, $hdu_summary, $hdu_tagno, $hdu_email, $hdu_resolution, $hdures_resolution, $hdu_description,
        $hdu_tech, $hdu_allocated, $hdu_closed, $hdu_hours, $hdu_fixcost, $hdu_hrate, $hdu_hcost, $hdu_distance, $hdu_fixother, $hdu_fix,
        $hdu_drate, $hdu_dcost, $hdu_eqptcost, $hdu_callout, $hduc_date, $hduc_postername, $hduc_comment, $hdu_priority, $hdu_savemsg, $hdu_totalcost;
        // get the ticket
        if ($this->hdu_quick || $this->hdu_new)
        {
            // Creating new ticket
            $id = 0;
        }
        else
        {
            // Not creating a new one so get the record
            $this->sql->gen("
		select * from #hdunit
		left join #hdu_categories on hdu_category=hducat_id
		left join #hdu_helpdesk on hducat_helpdesk=hdudesk_id
		left join #hdu_resolve on  hdu_resolution=hdures_id
		where hdu_id = $hdu_showid", false);
            extract($this->sql->fetch());
        }
        $hdu_userid = USERID;
        if ($this->hdu_new && $this->hdu_poster)
        {
            $hdu_read = true;
        }
        if ($this->hduprefs_posteronly && ($hdu_posterid == $hdu_userid))
        {
            $hdu_read = true;
        }
        if (!$this->hduprefs_posteronly || $this->hdu_super || $this->hdu_technician)
        {
            $hdu_read = true;
        }
        if ($this->hdu_new || $hdu_action_quick)
        {
            $hdu_read = true;
        }
        if (!$hdu_read)
        {
            $hdu_retval = "
<table class='fborder table' style='" . USER_WIDTH . "'>
	<tr>
		<td class='fcaption'>" . HDU_199 . "</td>
	</tr>
	<tr>
		<td class='forumheader3'><a href='?$from.list.$id.$R1'><img src='./images/updir.png' alt='" . HDU_73 . "' title='" . HDU_73 . "' style='border:0;' /></a></td>
	</tr>
	<tr>
		<td class='forumheader3'>" . HDU_202 . "</td>
	</tr>
	<tr>
		<td class='fcaption'>&nbsp;</td>
	</tr>
</table>";
        }
        else
        {
            // If this is a new ticket and being entered by super or technician
            // then get the list of system users to chose from
            if ($this->hdu_new && ($this->hdu_super || $this->hdu_technician))
            {
                $hdu_sel_users = "<select class='tbox form-handler' name='hdu_selusers' >";
                $this->sql->select("user", "user_id,user_name", "order by user_name", "nowhere", false);
                while ($hdu_urow = $this->sql->fetch())
                {
                    $hdu_sel_users .= "<option value='" . $hdu_urow['user_id'] . "' >" . $this->tp->toFORM($hdu_urow['user_name']) . "</option>";
                }
                $hdu_sel_users .= "</select>";
            } elseif ($this->hdu_new)
            {
                // entered by a user
                $hdupostername = USERNAME;
                $hduposterid = USERID;
                $hdu_email = USEREMAIL;
                $hdu_datestamp = 0;
            }
            else
            {
                // Not a new ticket so get the details from the ticket
                $hduposterdet = explode(".", $hdu_poster, 2);
                $hduposterid = $hduposterdet[0];
                $hdupostername = $hduposterdet[1];
            }
            // If the ticket is closed then don't allow editing
            $hdu_ticket_closed = $hdu_closed > 0;
            // See if poster allows email address to be shown
            if ($this->sql->select("user", "user_hideemail", "user_id='$hduposterid'"))
            {
                $hdu_urow = $this->sql->fetch();
                $this->hdu_showemail = $hdu_urow['hdu_urow'] > 0;
            }
            // If the user is the poster supervisor or technician then show the email address anyway
            if (USERID == $hduposterid || $this->hdu_super || $this->hdu_technician)
            {
                $this->hdu_showemail = false;
            }
            
            // Display top table containing back or print record // Not needed in normal view, only when editing or new, right?
            $hdu_retval = $this->hdu_new?"
<script type='text/javascript'>
<!--
function checkform(theform)
{
	if (theform.hdu_summary.value==null || theform.hdu_summary.value == \"\")
	{
		alert(\"" . HDU_213 . "\");
		return false;
	}
	if (theform.hdu_category.value==0 )
	{
		alert(\"" . HDU_212 . "\");
		return false;
	}
	if (theform.hdu_description.value==null || theform.hdu_description.value == \"\")
	{
		alert(\"" . HDU_211 . "\");
		return false;
	}
	return true;
}
function changed()
{
	document.getElementById('formok').disabled=false
	document.getElementById('formok').value='" . HDU_5 . "'
	document.getElementById('hdu_changed').value='yes'
}
-->
</script>
	<form id='upstat' method='post' action='" . e_SELF . "' onsubmit=\"return checkform(this)\" >
	<div>
		<input type='hidden' name='hdu_aaction' value='updet' />
		<input type='hidden' name='from' value='$from' />
		<input type='hidden' name='id' value='$hdu_showid' />
		<input type='hidden' name='hdu_new' value='" . ($this->hdu_new?1:0) . "' />
		<input type='hidden' name='hdu_cfix' value='" . $hdu_fix . "' />
		<input type='hidden' name='hdu_callocated' value='" . $hdu_allocated . "' />
		<input type='hidden' name='hdu_readyclosed' value='" . $hdu_closed . "' />
		<input type='hidden' name='hduposterid' value='" . $hduposterid . "' />
		<input type='hidden' name='hdu_ctech' value='" . $hdu_tech . "' />
		<input type='hidden' id='hdu_changed' name='hdu_changed' value='no' />
		<input type='hidden' id='hdu_lasttime' name='hdu_lasttime' value='" . $hdu_lastchanged . "' />":"";
            /*
            if (!$helpdesk_obj->hdu_new && (USERID == $hdu_posterid || $helpdesk_obj->hduprefs_allread) && !$helpdesk_obj->hdu_super && !$helpdesk_obj->hdu_technician)
            {
                $hdu_retval .= "<input type='hidden' name='hdu_commentonly' value='yes'>";
            }
            else
            {
                $hdu_retval .= "<input type='hidden' name='hdu_commentonly' value='no'>";
            }
            */
            $hdu_retval .= "<input type='hidden' name='hdu_commentonly' value=".((!$helpdesk_obj->hdu_new && (USERID == $hdu_posterid || $helpdesk_obj->hduprefs_allread) && !$helpdesk_obj->hdu_super && !$helpdesk_obj->hdu_technician)?'yes':'no').">";

            $hdu_retval .= $this->hdu_new?"</div>":"";
            // *
            // * Top page header
            // *
            // $hdu_retval .= "
            // <div id='titlecaption' style='text-align:center'>";
//            require(HDU_THEME);
            $HDU_SHOWTICKET = e107::getTemplate('helpdesk', 'helpdesk_show');
            $hdu_shortcodes = e107::getScBatch('show','helpdesk');
//            var_dump($hdu_shortcodes);
//            var_dump($HDU_SHOWTICKET);
            $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET[$this->hdu_new?"edit_main":"main"], false, $hdu_shortcodes);
            // $hdu_retval .= "
            // <tr>
            // <td class='forumheader3' colspan='2'>
            // <div id=\"tabcontentcontainer\">";
            // // Div for ticket details
            // $hdu_retval .= "<div id=\"sc1\" class=\"tabcontent\">";
            $hdu_retval .= $this->hdu_new?"":'<div class="tab-content"><div id="tab0" class="tab-pane fade show active" role="tabpanel" aria-labelledby="tab0" tabindex="0">';
            $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET[$this->hdu_new?"edit":"ticket"], true, $hdu_shortcodes);
            // $hdu_retval .= "</div>";
            $hdu_retval .= $this->hdu_new?"":'</div><div id="tab1" class="tab-pane fade" role="tabpanel" aria-labelledby="tab1" tabindex="0">';
            $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET["details"], true, $hdu_shortcodes);
            if ($this->hdu_new)
            {
                // if it is a new ticket then set the default rates
                if ($hdu_hrate == 0)
                {
                    $hdu_hrate = $this->hduprefs_hourlyrate;
                }
                if ($hdu_drate == 0)
                {
                    $hdu_drate = $this->hduprefs_distancerate;
                }
                if ($hdu_callout == 0)
                {
                    $hdu_callout = $this->hduprefs_callout;
                }
            }
            $hdu_retval .= $this->hdu_new?"":'</div><div id="tab2" class="tab-pane fade" role="tabpanel" aria-labelledby="tab2" tabindex="0">';
            $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET["finance"], true, $hdu_shortcodes);

            if (!$helpdesk_obj->hdu_new && (USERID == $hdu_posterid || $helpdesk_obj->hduprefs_allread || $helpdesk_obj->hdu_super || $helpdesk_obj->hdu_technician))
            {
                $hdu_retval .= $this->hdu_new?"":'</div><div id="tab3" class="tab-pane fade" role="tabpanel" aria-labelledby="tab3" tabindex="0">';
                $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET["comment_header"], true, $hdu_shortcodes);
		
				$where = " hduc_ticketid='$hdu_showid' order by hduc_date asc";
				$hducrows = e107::getDb()->retrieve("hdu_comments", "*", $where, true);
				foreach($hducrows AS $hducrow)
                {
                    extract($hducrow);
                    $hduc_poster = explode(".", $hduc_poster);
                    $hduc_posterid = $hduc_poster[0];
                    $hduc_postername = $hduc_poster[1];
                    $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET["comment_detail"], true, $hdu_shortcodes);
                }
                $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET["comment_footer"], true, $hdu_shortcodes);
            }

            $hdu_retval .= $this->hdu_new?"":'</div></div>';
            $hdu_retval .= $this->tp->parseTemplate($HDU_SHOWTICKET["footer"], true, $hdu_shortcodes);
            $hdu_retval .= $this->hdu_new?"</form>":"";
        }
/*
        echo "<pre>";
        print_r($hdu_retval);
        echo "</pre>";
*/
                return $hdu_retval;
//        return "test";
}
    
    // **********************************************************************************************
    // *
    // *	Function	:	update_ticket($id)
    // *
    // *	Parameters	:	integer - $id ID of ticket to update
    // *
    // *	Returns		:	integer - ID if successful else -ve for error
    // *
    // *	Description	:	Updates/creates a ticket
    // *
    // *
    // **********************************************************************************************
	   // Update the fields that techy people update and only the techy people are allowed to do it
    function update_ticket($id)
    {
        global $id;
        unset($hdu_up_closed);
        unset($hdu_args);
 
        $this->hdu_new = intval($_POST['hdu_new']) == 1;
        if ($_POST['hdu_changed'] == "yes")
        {
            $hdu_sendmail = false;
            $hdu_a_alloctime = 0;
            $hdu_a_fcost = 0;
            $hdu_drate = 0;
            // *****************************************************************
            // * Now get all the calculatable bits
            // *****************************************************************
            // *****************************************************************
            // * Calculate the financials
            // *****************************************************************
            if ($this->hduprefs_showfixes && ($_POST['hdu_fixcost'] == 0 || $_POST['hdu_cfix'] != $_POST['hdu_fix']))
            {
                $hdu_fixcost = $this->hdu_getfixcost($_POST['hdu_fix']);
            }
            else
            {
                $hdu_fixcost = $_POST['hdu_fixcost'];
            }
            if (is_numeric($_POST['hdu_drate']))
            {
                $hdu_drate = $_POST['hdu_drate'];
            }
            else
            {
                $hdu_drate = ($this->hduprefs_distancerate > 0?$this->hduprefs_distancerate:0);
            }

            if (is_numeric($_POST['hdu_callout']))
            {
                $hdu_callout = $_POST['hdu_callout'];
            }
            else
            {
                $hdu_callout = ($this->hduprefs_callout > 0?$this->hduprefs_callout:0);
            }
            if (is_numeric($_POST['hdu_eqptcost']))
            {
                $hdu_eqptcost = $_POST['hdu_eqptcost'] ;
            }
            else
            {
                $hdu_eqptcost = 0;
            }
            if (is_numeric($_POST['hdu_distance']))
            {
                $hdu_distance = $_POST['hdu_distance'];
            }
            else
            {
                $hdu_distance = 0;
            }
            if (is_numeric($_POST['hdu_hours']))
            {
                $hdu_hours = $_POST['hdu_hours'];
            }
            else
            {
                $hdu_hours = 0;
            }

            if (is_numeric($_POST['hdu_hrate']))
            {
                $hdu_hrate = $_POST['hdu_hrate'];
            }
            else
            {
                $hdu_hrate = ($this->hduprefs_hourlyrate > 0?$this->hduprefs_hourlyrate:0);
            }
            $hdu_a_dcost = $hdu_distance * $hdu_drate ;
            $hdu_a_hcost = $hdu_hours * $hdu_hrate;

			$hdu_fixcost    = (float) ($hdu_fixcost ?? 0);
			$hdu_callout    = (float) ($hdu_callout ?? 0);
			$hdu_a_fcost    = (float) ($hdu_a_fcost ?? 0);
			$hdu_eqptcost   = (float) ($hdu_eqptcost ?? 0);
			$hdu_a_dcost    = (float) ($hdu_a_dcost ?? 0);
			$hdu_a_hcost    = (float) ($hdu_a_hcost ?? 0);

            $hdu_a_totalcost = $hdu_fixcost + $hdu_callout + $hdu_a_fcost + $hdu_eqptcost + $hdu_a_dcost + $hdu_a_hcost;
            // if the ticket has been allocated to a helpdesk then set the time
            // print $_POST['hdu_tech']."hdu_tech";
            // print $_POST['hdu_allocated']."allocated";
            // print $_POST['hdu_callocated']."callocated";
            // *****************************************************************
            // *Work out the allocations
            // *****************************************************************
            // If the ticket is new and not supervisor or technician because they get the details tab
            if ($this->hdu_new && !$this->hdu_super && !$this->hdu_technician)
            {
                // If we auto assign a helpdesk if category given then do so
                if ($this->hduprefs_autoassign)
                {
                    // if we are auto assigning to helpdesk and no manual selection of assignee
                    // get the help desk associated with this category
                    if ($this->sql->select("hdu_categories", "hducat_helpdesk", "where hducat_id = '" . intval($_POST['hdu_category']) . "'", "nowhere", false))
                    {
                        // Get the helpdesk to which this will be assigned if one exists
                        extract($this->sql->fetch());
                        $hdu_a_tech = $hducat_helpdesk;
                        // else
                        // {
                        // if there isnt a helpdesk
                        // $hdu_a_tech = 0;
                        // }
                        if (intval($hdu_a_tech) > 0)
                        {
                            $_POST['hdu_resolution'] = $this->hduprefs_assigned;
                            $hdu_a_alloctime = time();
                        }
                    }
                }
                else
                {
                    $hdu_a_tech = intval($_POST['hdu_tech']);
                }
                // $hdu_a_resolution = 0;
                // $hdu_a_tech = 0;
            } elseif ($_POST['hdu_ctech'] != $_POST['hdu_tech'] && intval($_POST['hdu_tech']) > 0)
            {
                // Assignment has changed and not un assigned
                $hdu_a_tech = $_POST['hdu_tech'];
                $hdu_a_alloctime = time();
            } elseif (intval($_POST['hdu_tech']) == 0)
            {
                // ticket now unassigned
                $hdu_a_tech = 0;
                $hdu_a_alloctime = 0;
            }
            else
            {
                // No change made to assignment
                $hdu_a_tech = $_POST['hdu_tech'];
                $hdu_a_alloctime = intval($_POST['hdu_callocated']);
            }
            // *****************************************************************
            // *Work out the resolutions
            // *****************************************************************
            // If no resolution specified then get the default
            // print "W" . $this->hduprefs_defaultres;
            if ($this->hduprefs_defaultres > 0 && $this->hdu_new && intval($_POST['hdu_resolution']) == 0)
            {
                $hdu_a_resolution = $this->hduprefs_defaultres;
            }
            else
            {
                // otherwise use the resolution specified
                $hdu_a_resolution = intval($_POST['hdu_resolution']);
            }
            // *****************************************************************
            // * Work out the status
            // *****************************************************************
            // Work out if the ticket is open or closed
            // Is this resolution is one that closes the ticket and not already closed then automatically then close the ticket.
            if ($this->hdu_statcloses($hdu_a_resolution) && intval($_POST['hdu_readyclosed']) == 0)
            {
                // print "closed";
                $hdu_a_closed = time();
            } elseif (!$this->hdu_statcloses($hdu_a_resolution))
            {
                // print "opened";
                $hdu_a_closed = 0;
            }
            else
            {
                $hdu_a_closed = intval($_POST['hdu_readyclosed']);
            }
            // if a comment has been posted and its by a user and this repoens then set status or if technician or supervisor then reopen
            if (!empty($_POST['hduc_comment']) && intval($_POST['hdu_readyclosed']) > 0 && ($this->hdu_technician || $this->hdu_super || $this->hduprefs_reopen))
            {
                if (intval($_POST['hdu_tech']) == 0)
                {
                    // if no status assigned set tod default open status
                    $hdu_a_resolution = $this->hduprefs_defaultres;
                }
                else
                {
                    $hdu_a_resolution = $this->hduprefs_assigned;
                }
                $hdu_a_closed = 0;
            }
            // *
            // *
            // print "$hdu_a_totalcost - $hdu_a_fcost - $hdu_a_dcost - $hdu_a_hcost - " . $_POST['hdu_eqptcost'] . " - " . $_POST['hdu_callout'] . "-";
            // Set all the parameters for inserting in to the db
            if ($this->hdu_new)
            {
                if (intval($_POST['hdu_selusers']) > 0)
                {
                    // Get poster's name and id if it was from a quick entry
                    $hdu_a_posterid = intval($_POST['hdu_selusers']);
                    $hdu_a_poster = $this->hdu_getposterdetails($hdu_a_posterid);
                    $hdu_email = $this->tp->toDB($this->hdu_getuseremail($hdu_a_posterid));
                }
                else
                {
                    // If they are a logged in user then get their username and id
                    $hdu_a_posterid = USERID;
                    $hdu_a_poster = $hdu_a_poster . USERID . "." . $this->tp->toDB(USERNAME);
                    $hdu_email = $this->tp->toDB(USEREMAIL);
                }
                // Insert the record
                // check if an existing identical ticket exists
                if ($this->sql->select("hdunit", "hdu_id", "hdu_category='" . intval($_POST['hdu_category']) . "'
and hdu_summary='" . $this->tp->toDB($_POST['hdu_summary']) . "' and hdu_description='" . $this->tp->toDB($_POST['hdu_description']) . "' and
hdu_priority='" . intval($_POST['hdu_priority']) . "'"))
                {
                    // already exists
                    $hdu_result = -3;
                }
                else
                {
                    $hdu_args .= "
	'0',
	" . time() . ",
	'$hdu_a_poster',
	'$hdu_a_posterid',
	'" . intval($_POST['hdu_category']) . "',
	'" . $this->tp->toDB($_POST['hdu_summary']) . "',
	'" . $this->tp->toDB($_POST['hdu_description']) . "',
	'" . intval($_POST['hdu_priority']) . "',
	'" . $hdu_a_resolution . "',
	'" . $hdu_email . "',
	'" . $hdu_a_alloctime . "',
	'$hdu_a_tech',
	" . time() . ",
	'" . $hdu_a_closed . "',
	'" . $this->tp->toDB($_POST['hdu_tagno']) . "',
	'0',
	'0',
	'" . intval($_POST['hdu_fix']) . "',
	'" . $this->tp->toDB($hdu_fixother) . "',
	'" . $this->tp->toDB($hdu_fixcost) . "',
	'0',
	'" . $this->tp->toDB($hdu_distance) . "',
	'" . $this->tp->toDB($hdu_drate) . "',
	'" . $hdu_a_dcost . "',
	'" . $this->tp->toDB($hdu_hours) . "',
	'" . $this->tp->toDB($hdu_hrate) . "',
	'" . $hdu_a_hcost . "',
	'" . $this->tp->toDB($hdu_callout) . "',
	'" . $this->tp->toDB($hdu_eqptcost) . "',
	'" . $hdu_a_totalcost . "'";
                    $id = $this->sql->insert("hdunit", $hdu_args, false);
                    if ($id > 0)
                    {
                        // Ticket created
                        $hdu_result = $id;
                    }
                    else
                    {
                        // unable to create ticket
                        $hdu_result = -2;
                    }
                }
            }
            else
            {
                if ($_POST['hdu_commentonly'] == "no")
                {
                    // only save if not just user's comments
                    $this->hdu_new = false;
                    // ***************************************************************
                    // * Save an existing ticket
                    // ***************************************************************
                    $hdu_email = $_POST['hdu_email'];
                    $hdu_args .= "hdu_category = '" . intval($_POST['hdu_category']) . "',";
                    $hdu_args .= "hdu_summary = '" . $this->tp->toDB($_POST['hdu_summary']) . "',";
                    $hdu_args .= "hdu_description = '" . $this->tp->toDB($_POST['hdu_description']) . "',";
                    $hdu_args .= "hdu_priority = '" . intval($_POST['hdu_priority']) . "',";
                    $hdu_args .= "hdu_resolution = '" . $hdu_a_resolution . "',";
                    $hdu_args .= "hdu_email = '" . $hdu_email . "',";
                    $hdu_args .= "hdu_allocated = '" . $hdu_a_alloctime . "',";
                    $hdu_args .= "hdu_tech = '" . $hdu_a_tech . "',";
                    $hdu_args .= "hdu_lastchanged = '" . time() . "',";
                    $hdu_args .= "hdu_closed = '" . $hdu_a_closed . "',";
                    $hdu_args .= "hdu_tagno = '" . $this->tp->toDB($_POST['hdu_tagno']) . "',";
                    $hdu_args .= "hdu_fix = '" . intval($_POST['hdu_fix']) . "',";
                    $hdu_args .= "hdu_fixother = '" . $this->tp->toDB($_POST['hdu_fixother']) . "',";
                    $hdu_args .= "hdu_fixcost = '" . $this->tp->toDB($hdu_fixcost) . "',";
                    $hdu_args .= "hdu_distance = '" . $this->tp->toDB($hdu_distance) . "',";
                    $hdu_args .= "hdu_drate = '" . $this->tp->toDB($hdu_drate) . "',";
//                    $hdu_a_dcost = $hdu_a_dcost;  // ????
                    $hdu_args .= "hdu_dcost = '" . $this->tp->toDB($hdu_a_dcost) . "',";
                    $hdu_args .= "hdu_hours = '" . $this->tp->toDB($hdu_hours) . "',";
//                    $hdu_a_hcost = $hdu_a_hcost;  // ????
                    $hdu_args .= "hdu_hrate = '" . $this->tp->toDB($hdu_hrate) . "',";
                    $hdu_args .= "hdu_hcost = '" . $hdu_a_hcost . "',";
                    $hdu_args .= "hdu_eqptcost = '" . $this->tp->toDB($hdu_eqptcost) . "',";
                    $hdu_args .= "hdu_callout = '" . $this->tp->toDB($hdu_callout) . "',";
                    $hdu_args .= "hdu_totalcost = '" . $hdu_a_totalcost . "'";
                    $hdu_args .= " where hdu_id = '" . intval($id) . "' " ;
                    if ($this->sql->update("hdunit", $hdu_args, false))
                    {
                        $hdu_result = $id;
                    }
                    else
                    {
                        $hdu_result = -2;
                    }
                    $hdu_recno = intval($id);
                }
                $this->helpdesk_cache_clear();
            }
            // *****************************************************************
            // * If configured to produce a pdf then do so.
            // * We should also check if we can produce the pdf (directory writable
            // * etc)
            // * Else just send the emails
            // *****************************************************************
            // if ($this->hdu_new)
            // {
            // $hdu_msg .= "<br /><a href='?$from..." . $_POST['R1'] . "'>" . HDU_32 . "</a>";
            // }
            // else
            // {
            // $hdu_msg .= HDU_79 . "<br /><a href='?$from..." . $_POST['R1'] . "'>" . HDU_32 . "</a>";
            // }
            // make sure buffer is empty
            // ob_flush();
            if ($hdu_result)
            {
                if ($this->hduprefs_mailpdf)
                {
                    require_once("pdfit.php");
                    pdfit($hdu_recno, "f", "Helpdesk", e_PLUGIN . HELPDESK_FOLDER . "/pdfout/" . "helpdesk" . $hdu_recno . ".pdf", "A4");
                    $this->hdu_notify($id, $this->hdu_new);
                }
                else
                {
                    $this->hdu_notify($id, $this->hdu_new);
                }
            }
            if (!empty($_POST['hduc_comment']))
            {
                $this->post_comment();
                // now check if posting a comment re opens the ticket
                // add code
                $hdu_result = $id;
            }
        }
        return $hdu_result;
    }
    // **********************************************************************************************
    // *
    // *	Function	:	hdu_notify($hdu_notifyid = 0, $hdu_notifyaction)
    // *
    // *	Parameters	:	$hdu_notifyid integer the ticket number
    // *				:	$hdu_notifyaction boolean update or new ticket
    // *
    // *
    // *
    // *
    // **********************************************************************************************
    function hdu_notify($hdu_notifyid = 0, $hdu_notifyaction = NULL)
    {
        global $PLUGINS_DIRECTORY, $pref, $sysprefs, $pm_prefs ,
        $hdu_up_db, $hdu_msg, $hdu_recno,
        $hdu_newing,
        $hdu_email,
        $hdu_a_tech;
        // get the record for this particular ticket
        $this->sql->gen("
		select * from #hdunit
		left join #hdu_categories on hdu_category=hducat_id
		left join #hdu_helpdesk on hducat_helpdesk=hdudesk_id
		left join #hdu_resolve on  hdu_resolution=hdures_id
		left join #user on hdu_posterid=user_id
		where hdu_id = $hdu_notifyid", false);
        extract($this->sql->fetch());
        // get the poster name
        $hduposterdet = explode(".", $hdu_poster, 2);
        $hduposterid = $hduposterdet[0];
        $hdupostername = $hduposterdet[1];
        // *******************************
        // * Get the necessary files for emailing and PMing
        // *******************************
        require_once(e_HANDLER . "mail.php");
        $retrieve_prefs[] = 'pm_prefs';
        require_once(e_PLUGIN . "pm/pm_class.php");
        require_once(e_PLUGIN . "pm/pm_func.php");
        $lan_file = e_PLUGIN . "pm/languages/" . e_LANGUAGE . ".php";
        include_lan(e_PLUGIN . "pm/languages/English.php");

        $pm_prefs = $sysprefs->getArray("pm_prefs");
        $hdum_pm = new private_message;
        // Get the technician class
        // $sql->db_Select("hdu_helpdesk", "*", "where hdudesk_id=$hdu_a_tech", "nowhere", false);
        // $hdu_hrow = $sql->db_Fetch();
        // $hdu_technician_class = $hdu_hrow['hdudesk_class'];
        $message = $hdum_head . $hdum_info . $hdum_message . $hdum_link;
        // ********************
        // * 1. process the user
        // * 2. process supervisor
        // * 3. process technicians
        // * 4. Email the helpdesk's email
        // *********************
        // Check if we notify the user and we have a valid user id
        if ($this->hduprefs_mailuser > 0 && $user_id > 0)
        {
            // Check if we notify the user by email
            if ($this->hduprefs_mailuser == 1)
            {
                // Do we notify the user of updates to their ticket by email?
                // You can only send an email if the user's id > 0 otherwise they are not a registered user
                $hdu_message1 = $this->tp->toFORM(($hdu_notifyaction? $this->hduprefs_usertext:$this->hduprefs_updateuser)) . "<br /><br />";
                $hdu_message1 .= HDU_44 . "<br /><br />";
                $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
                $hdu_message1 .= HDU_22 . "<a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.$hdu_notifyid '>" . HDU_209 . "</a><br /><br />";
                $hdu_message1 .= HDU_43 . "<br /><br />";
                $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_usersubject:$this->hduprefs_userupsubject));
                if ($this->hduprefs_mailpdf)
                {
                    // Do we send ticket as a pdf
                    if (sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name, e_PLUGIN . HELPDESK_FOLDER . "/pdfout/helpdesk" . $hdu_notifyid . ".pdf"))
                    {
                        $hdu_msg .= HDU_46 . " $hdu_email<br />";
                    }
                    else
                    {
                        $hdu_msg .= HDU_47 . " $hdu_email<br />";
                    }
                }
                else
                {
                    // Dont send a pdf of the ticket
                    // sendemail($send_to, $subject, $message, $to_name, $send_from='', $from_name='', $attachments='', $Cc='', $Bcc='', $returnpath='', $returnreceipt='',$inline ="")
                    // print " uemail - $hdu_email - $hdu_subject - $user_name - $hdudesk_email - $hdudesk_name";
                    if (sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name))
                    {
                        $hdu_msg .= HDU_46 . "<br />";
                    }
                    else
                    {
                        $hdu_msg .= HDU_47 . "<br />";
                    }
                }
            } // end : if ($this->hduprefs_mailuser == 1 && $user_id>0)
            // Check if we notify the user by PM
            if ($this->hduprefs_mailuser == 2)
            {
                // Do we notify the user of updates to their ticket by PM?
                // You can only send a PM if the user's id > 0 otherwise they are not a registered user
                $hdu_message1 = "<hr />" . $this->tp->toFORM(($hdu_notifyaction? $this->hduprefs_usertext:$this->hduprefs_updateuser)) . "<br /><br />";
                $hdu_message1 .= HDU_44 . "<br /><br />";
                $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
                $hdu_message1 .= HDU_22 . " <a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.{$hdu_notifyid }' >" . HDU_209 . "</a><br /><br />";
                $hdu_message1 .= HDU_43 . "<br /><br /><hr />";
                $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_usersubject:$this->hduprefs_userupsubject));
                $hdu_vars['pm_subject'] = $hdu_subject;
                $hdu_vars['pm_message'] = $hdu_message1;
                $hdu_vars['to_info']['user_id'] = $user_id;
                $hdu_vars['from_id'] = $this->hduprefs_pmfrom;
                $hdu_vars['to_info']['user_email'] = $hdu_email;
                $hdu_vars['to_info']['user_name'] = $user_name;
                $hdu_vars['to_info']['user_class'] = $user_class;
                $res = $this->add($hdu_vars);
            }
        }
        // Check if we notify the supervisor class and supervisor class is active
        if ($this->hduprefs_mailhelpdesk > 0 && $this->pluginPrefs['hduprefs_supervisorclass'] < 255)
        {
            // get a list of supervisors
            $hdusclist = $this->pluginPrefs['hduprefs_supervisorclass'];
            if ($hdusclist == 254)
            {
                // admin
                $hdu_where = 'where user_admin=1';
            } elseif ($hdusclist == 250)
            {
                // main admin
                $hdu_where = 'where user_admin=1 and left(user_perms,1)="0"';
            }
            else
            {
                // normal userclass
                $hdu_where = "where find_in_set({$hdusclist},user_class)";
            }

            $hdu_arg = "select user_id,user_name,user_email from #user {$hdu_where} ";
            $hdu_gotsuper = $this->sql->gen($hdu_arg, false);
            if ($hdu_gotsuper)
            {
                while ($hdu_row = $this->sql->fetch())
                {
                    $hdu_supers[] = array('user_id' => $hdu_row['user_id'], 'user_name' => $hdu_row['user_name'], 'user_email' => $hdu_row['user_email']);
                }
            }
            foreach($hdu_supers as $value)
            {
                // check if we notify the supervisor class by email
                $user_email = $value['user_email'];
                $user_name = $value['user_name'];
                $user_id = $value['user_id'];
                if ($this->hduprefs_mailhelpdesk == 1)
                {
                    // get the email address for this helpdesk
                    $hdu_message1 = $this->tp->toFORM(($hdu_notifyaction? $this->hduprefs_helpdesktext:$this->hduprefs_updatehelpdesk)) . "<br /><br />";
                    $hdu_message1 .= HDU_44 . "<br /><br />";
                    $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
                    $hdu_message1 .= HDU_22 . "<a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.$hdu_notifyid '>" . HDU_209 . "</a><br /><br />";
                    $hdu_message1 .= HDU_43 . "<br /><br />";
                    $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_helpdesksubject:$this->hduprefs_helpupsubject));
                    // Get all the members of the class for this helpdesk and email each one
                    // hdu_tech
                    $hdu_technician_class = $hdu_a_tech;
                    // $hdu_mailarg = "select user_id,user_email,user_name,user_class from #user where find_in_set('$hdudesk_class',user_class)";
                    // $sql->db_Select_gen($hdu_mailarg, false);
                    // print $hdu_mailarg;
                    // while ($hdu_row = $sql->db_Fetch())
                    // {
                    // extract($hdu_row);
                    // print $user_email;
                    // print "<br>tech " . $user_email;
                    if ($this->hduprefs_mailpdf)
                    {
                        // print " uemail - $user_email ";
                        sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name, e_PLUGIN . HELPDESK_FOLDER . "/pdfout/helpdesk" . $hdu_notifyid . ".pdf");
                    }
                    else
                    {
                        // sendemail($send_to, $subject, $message, $to_name, $send_from='', $from_name='', $attachments='', $Cc='', $Bcc='', $returnpath='', $returnreceipt='',$inline ="")
                        // print " uemail - $user_email - $hdu_subject - $user_name - $hdudesk_email - $hdudesk_name";
                        sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name);
                    }
                    // } // while
                }
                // if there is an email address specified for the helpdesk then email that anyway.
                if ($this->hduprefs_mailhelpdesk == 2)
                {
                    // PM Team all members of the user class containing the technicians
                    unset($hdu_vars);
                    $hdu_message1 = $this->tp->toFORM(($hdu_newing?$hduprefs_helpdesktext:$hduprefs_updatehelpdesk)) . "<br /><br />";
                    $hdu_message1 .= HDU_44 . "<br /><br />";
                    $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
                    $hdu_message1 .= HDU_22 . " <a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.{$hdu_notifyid }' >" . HDU_209 . "</a><br /><br />";
                    $hdu_message1 .= HDU_43 . "<br /><br /><hr />";
                    $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_helpdesksubject:$this->hduprefs_helpupsubject));
                    // $hdu_mailarg = "select user_id,user_email,user_class from #user where find_in_set('$hdudesk_class',user_class)";
                    // $hdu_sql = new db;
                    // $hdu_sql->db_Select_gen($hdu_mailarg, false);
                    // print $hdu_mailarg;
                    // while ($hdu_row = $hdu_sql->db_Fetch())
                    // {
                    // extract($hdu_row);
                    // print $user_email;
                    $hdu_up_hemail = $user_email;
                    $hdu_vars['pm_subject'] = $hdu_subject;
                    $hdu_vars['pm_message'] = $hdu_message1;
                    $hdu_vars['to_info']['user_id'] = $user_id;
                    $hdu_vars['from_id'] = $this->hduprefs_pmfrom;;
                    $hdu_vars['to_info']['user_email'] = $user_email;
                    $hdu_vars['to_info']['user_name'] = $user_name;
                    // $hdu_vars['to_info']['user_class'] = $user_class;
                    $res = $this->add($hdu_vars);
                    // } // while
                }
            }
        }
        // Check if we notify the helpdesk class with technicians and supervisor class is active
        if ($this->hduprefs_mailhelpdesk > 0 && $hdudesk_class < 255)
        {
            // get a list of supervisors
            $hdusclist = $hdudesk_class;
            if ($hdusclist == 254)
            {
                // admin
                $hdu_where = 'where user_admin=1';
            } elseif ($hdusclist == 250)
            {
                // main admin
                $hdu_where = 'where user_admin=1 and left(user_perms,1)="0"';
            }
            else
            {
                // normal userclass
                $hdu_where = "where find_in_set({$hdusclist},user_class)";
            }
            // Get all the members of the class for this helpdesk and email each one
            $hdu_arg = "select user_id,user_name,user_email from #user {$hdu_where} ";
            $hdu_gottech = $this->sql->gen($hdu_arg, false);
            if ($hdu_gottech)
            {
                while ($hdu_row = $this->sql->fetch())
                {
                    $hdu_techs[] = array('user_id' => $hdu_row['user_id'], 'user_name' => $hdu_row['user_name'], 'user_email' => $hdu_row['user_email']);
                }
            }

            foreach($hdu_techs as $value)
            {
                // check if we notify the supervisor class by email
                $user_email = $value['user_email'];
                $user_name = $value['user_name'];
                $user_id = $value['user_id'];
                if ($this->hduprefs_mailhelpdesk == 1)
                {
                    // get the email address for this helpdesk
                    $hdu_message1 = $this->tp->toFORM(($hdu_notifyaction? $this->hduprefs_helpdesktext:$this->hduprefs_updatehelpdesk)) . "<br /><br />";
                    $hdu_message1 .= HDU_44 . "<br /><br />";
                    $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
                    $hdu_message1 .= HDU_22 . "<a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.$hdu_notifyid '>" . HDU_209 . "</a><br /><br />";
                    $hdu_message1 .= HDU_43 . "<br /><br />";
                    $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_helpdesksubject:$this->hduprefs_helpupsubject));
                    $hdu_technician_class = $hdu_a_tech;

                    if ($this->hduprefs_mailpdf)
                    {
                        // print " uemail - $user_email ";
                        sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name, e_PLUGIN . HELPDESK_FOLDER . "/pdfout/helpdesk" . $hdu_notifyid . ".pdf");
                    }
                    else
                    {
                        // sendemail($send_to, $subject, $message, $to_name, $send_from='', $from_name='', $attachments='', $Cc='', $Bcc='', $returnpath='', $returnreceipt='',$inline ="")
                        // print " uemail - $user_email - $hdu_subject - $user_name - $hdudesk_email - $hdudesk_name";
                        sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name);
                    }
                    // } // while
                }
                // if there is an email address specified for the helpdesk then email that anyway.
                if ($this->hduprefs_mailhelpdesk == 2)
                {
                    // PM Team all members of the user class containing the technicians
                    unset($hdu_vars);
                    $hdu_message1 = $this->tp->toFORM(($hdu_newing?$hduprefs_helpdesktext:$hduprefs_updatehelpdesk)) . "<br /><br />";
                    $hdu_message1 .= HDU_44 . "<br /><br />";
                    $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
                    $hdu_message1 .= HDU_22 . " <a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.{$hdu_notifyid }' >" . HDU_209 . "</a><br /><br />";
                    $hdu_message1 .= HDU_43 . "<br /><br /><hr />";
                    $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_helpdesksubject:$this->hduprefs_helpupsubject));
                    // $hdu_mailarg = "select user_id,user_email,user_class from #user where find_in_set('$hdudesk_class',user_class)";
                    // $hdu_sql = new db;
                    // $hdu_sql->db_Select_gen($hdu_mailarg, false);
                    // print $hdu_mailarg;
                    // while ($hdu_row = $hdu_sql->db_Fetch())
                    // {
                    // extract($hdu_row);
                    // print $user_email;
                    $hdu_up_hemail = $user_email;
                    $hdu_vars['pm_subject'] = $hdu_subject;
                    $hdu_vars['pm_message'] = $hdu_message1;
                    $hdu_vars['to_info']['user_id'] = $user_id;
                    $hdu_vars['from_id'] = $this->hduprefs_pmfrom;;
                    $hdu_vars['to_info']['user_email'] = $user_email;
                    $hdu_vars['to_info']['user_name'] = $user_name;
                    // $hdu_vars['to_info']['user_class'] = $user_class;
                    $res = $this->add($hdu_vars);
                    // } // while
                }
            }
        }
        if (!empty($hdudesk_email))
        {
            $hdu_message1 = $this->tp->toFORM(($hdu_notifyaction? $this->hduprefs_helpdesktext:$this->hduprefs_updatehelpdesk)) . "<br /><br />";
            $hdu_message1 .= HDU_44 . "<br /><br />";
            $hdu_plugloc = SITEURL . $PLUGINS_DIRECTORY;
            $hdu_message1 .= HDU_22 . "<a href='" . $hdu_plugloc . HELPDESK_FOLDER . "/helpdesk.php?0.show.$hdu_notifyid '>" . HDU_209 . "</a><br /><br />";
            $hdu_message1 .= HDU_43 . "<br /><br />";
            $hdu_subject = $this->tp->toFORM(($hdu_notifyaction?$this->hduprefs_helpdesksubject:$this->hduprefs_helpupsubject));
            $user_email = $hdudesk_email;
            if ($this->hduprefs_mailpdf)
            {
                // print " uemail - $user_email ";
                sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name, e_PLUGIN . HELPDESK_FOLDER . "/pdfout/helpdesk" . $hdu_notifyid . ".pdf");
            }
            else
            {
                // sendemail($send_to, $subject, $message, $to_name, $send_from='', $from_name='', $attachments='', $Cc='', $Bcc='', $returnpath='', $returnreceipt='',$inline ="")
                // print " uemail - $user_email - $hdu_subject - $user_name - $hdudesk_email - $hdudesk_name";
                sendemail($user_email, $hdu_subject . "- [$hdu_id]", $hdu_message1, $user_name, $hdudesk_email, $this->hduprefs_emailfrom . " " . $hdudesk_name);
            }
        }
    }
    function add($vars)
    {
        global $pm_prefs;
        $vars['options'] = "";
        $pmsize = 0;
        $attachlist = "";
        $pm_options = "";
        if (isset($vars['receipt']) && $vars['receipt'])
        {
            $pm_options .= "+rr+";
        }
        if (isset($vars['uploaded']))
        {
            foreach($vars['uploaded'] as $u)
            {
                if (!isset($u['error']))
                {
                    $pmsize += $u['size'];
                    $a_list[] = $u['name'];
                }
            }
            $attachlist = implode(chr(0), $a_list);
        }
        $pmsize += strlen($vars['pm_message']);
        $pm_subject = $this->tp->toDB($vars['pm_subject']);
        $pm_message = $this->tp->toDB($vars['pm_message'], false, true);
        $sendtime = time();
        if (isset($vars['to_userclass']) || isset($vars['to_array']))
        {
            if (isset($vars['to_userclass']))
            {
                require_once(e_HANDLER . "userclass_class.php");
                $toclass = r_userclass_name($vars['pm_userclass']);
                $tolist = $this->get_users_inclass($vars['pm_userclass']);
                $ret .= LAN_PM_38 . ": {$vars['to_userclass']}<br />";
                $class = true;
            }
            else
            {
                $tolist = $vars['to_array'];
                $class = false;
            }
            foreach($tolist as $u)
            {
                set_time_limit(30);
                if ($pmid = $this->sql->insert("private_msg", "0, '" . intval($vars['from_id']) . "', '" . $this->tp->toDB($u['user_id']) . "', '" . intval($sendtime) . "', '0', '{$pm_subject}', '{$pm_message}', '1', '0', '" . $this->tp->toDB($attachlist) . "', '" . $this->tp->toDB($pm_options) . "', '" . intval($pmsize) . "'"))
                {
                    if ($class == false)
                    {
                        $toclass .= $u['user_name'] . ", ";
                    }
                    if (check_class($pm_prefs['notify_class'], $u['user_class']))
                    {
                        $vars['to_info'] = $u;
                        $this->pm_send_notify($u['user_id'], $vars, $pmid, count($a_list));
                    }
                }
                else
                {
                    $ret .= LAN_PM_39 . ": {$u['user_name']} <br />";
                }
            }
            if (!$pmid = $this->sql->insert("private_msg", "0, '" . intval($vars['from_id']) . "', '" . $this->tp->toDB($toclass) . "', '" . intval($sendtime) . "', '1', '{$pm_subject}', '{$pm_message}', '0', '0', '" . $this->tp->toDB($attachlist) . "', '" . $this->tp->toDB($pm_options) . "', '" . intval($pmsize) . "'"))
            {
                $ret .= LAN_PM_41 . "<br />";
            }
        }
        else
        {
            if ($pmid = $this->sql->insert("private_msg", "0, '" . intval($vars['from_id']) . "', '" . $this->tp->toDB($vars['to_info']['user_id']) . "', '" . intval($sendtime) . "', '0', '{$pm_subject}', '{$pm_message}', '0', '0', '" . $this->tp->toDB($attachlist) . "', '" . $this->tp->toDB($pm_options) . "', '" . intval($pmsize) . "'"))
            {
                if (check_class($pm_prefs['notify_class'], $vars['to_info']['user_class']))
                {
                    set_time_limit(30);
                    $this->pm_send_notify($vars['to_info']['user_id'], $vars, $pmid, count($a_list));
                }
                $ret .= LAN_PM_40 . ": {$vars['to_info']['user_name']}<br />";
            }
        }
        return $ret;
    }
    function pm_send_notify($uid, $pminfo, $pmid, $attach_count = 0)
    {
        require_once(e_HANDLER . "mail.php");
        global $PLUGINS_DIRECTORY;
        $subject = LAN_PM_100 . SITENAME;
        $pmlink = SITEURL . $PLUGINS_DIRECTORY . "pm/pm.php?show.{$pmid}";
        $txt = LAN_PM_101 . SITENAME . "\n\n";
        $txt .= LAN_PM_102 . USERNAME . "\n";
        $txt .= LAN_PM_103 . $pminfo['pm_subject'] . "\n";
        if ($attch_count > 0)
        {
            $txt .= LAN_PM_104 . $attach_count . "\n";
        }
        $txt .= LAN_PM_105 . "\n" . $pmlink . "\n";
        sendemail($pminfo['to_info']['user_email'], $subject, $txt, $pminfo['to_info']['user_name']);
    }

    function tablerender($caption, $text, $mode = "default", $return = false)
    {
        global $ns ;
        // do the mod rewrite steps if installed
        // $modules = apache_get_modules();
        if ($this->pluginPrefs['hduprefs_seo'] == 1)
        {
            $patterns[0] = '/' . $PLUGINS_DIRECTORY . '\/helpdesk\/helpdesk\.php\?([0-9]+).([a-z]+).([0-9]+).([0-9]+)/';
            $patterns[1] = '/' . $PLUGINS_DIRECTORY . '\/helpdesk\/helpdesk\.php\?([0-9]+).([a-z]+).([0-9]+)/';
            $replacements[0] = '/helpdesk/helpdesk-$1-$2-$3-$4.html';
            $replacements[1] = '/helpdesk/helpdesk-$1-$2-$3.html';

            $text = preg_replace($patterns, $replacements, $text);
        }
        $ns->tablerender($caption, $text, $mode , $return);
    }
    function regen_htaccess($onoff)
    {
        $hta = '.htaccess';
        $pattern = array("\n", "\r");
        $replace = array("", "");
        if (is_writable($hta) || !file_exists($hta))
        {
            // open the file for reading and get the contents
            $file = file($hta);
            $skip_line = false;
            unset($new_line);
			$new_line = array();
            foreach($file as $line)
            {
                if (strpos($line, '*** HELPDESK REWRITE BEGIN ***') > 0)
                {
                    // we start skipping
                    $skip_line = true;
                }

                if (!$skip_line)
                {
                    // print strlen($line) . '<br>';
                    $new_line[] = str_replace($pattern, $replace, $line);
                }
                if (strpos($line, '*** HELPDESK REWRITE END ***') > 0)
                {
                    $skip_line = false;
                }
            }
            if ($onoff == 'on')
            {
                $base_loc = str_replace(basename($_SERVER['PHP_SELF']), '', $_SERVER['PHP_SELF']);
                $new_line[] = "#*** HELPDESK REWRITE BEGIN ***";
                $new_line[] = 'RewriteEngine On';
                $new_line[] = "RewriteBase $base_loc";
                $new_line[] = 'RewriteRule helpdesk.html helpdesk.php';
                $new_line[] = 'RewriteRule helpdesk-([0-9]*)-([a-z]*)-([0-9]*)\.html(.*)$ helpdesk.php?$1.$2.$3';
                $new_line[] = 'RewriteRule helpdesk-([0-9]*)-([a-z]*)-([0-9]*)-([0-9]*)\.html(.*)$ helpdesk.php?$1.$2.$3.$4';
                $new_line[] = '#*** HELPDESK REWRITE END ***';
                $outwrite = implode("\n", $new_line);
            }
            else
            {
                $outwrite = implode("\n", $new_line);
            }
            $retval = 0;
            if ($fp = fopen('tmp.txt', 'wt'))
            {
                // we can open the file for reading
                if (fwrite($fp, $outwrite) !== false)
                {
                    fclose($fp);
                    // we have written the new data to temp file OK
                    if (is_readable('old.htaccess'))
                    {
                        // there is an old htaccess file so delete it
                        if (!unlink('old.htaccess'))
                        {
                            $retval = 2;
                        }
                    }
                    if ($retval == 0)
                    {
                        // old one deleted OK so rename the existing to the old one
                        if (is_readable('.htaccess'))
                        {
                            // if there is an old .htaccess then rename it
                            if (!rename('.htaccess', 'old.htaccess'))
                            {
                                $retval = 3;
                            }
                        }
                    }
                    if ($retval == 0)
                    {
                        // successfully renamed existing htaccess to old.htaccess
                        // so rename the temp file to .htaccess
                        if (!rename('tmp.txt', '.htaccess'))
                        {
                            $retval = 4;
                        }
                    }
                }
                else
                {
                    // unable to open temporary file
                    $retval = 5;
                }
            }
            else
            {
                fclose($fp);
                $retval = 1;
            }
            return $retval;
            // unlink('old.htaccess');
            // print_a($new_line);
        }
    }
}

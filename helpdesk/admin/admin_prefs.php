<?php
require_once("../../../class2.php");
if (!defined('e107_INIT'))
{
    exit;
}
if (!getperms("P"))
{
    header("location:" . e_BASE . "index.php");
    exit;
}
include_lan(e_PLUGIN . HELPDESK_FOLDER . "/languages/admin/" . e_LANGUAGE . "_helpdesk_admin.php");
if (!isset($helpdesk_obj) || !is_object($helpdesk_obj))
{
	require_once(e_PLUGIN . HELPDESK_FOLDER . "/includes/helpdesk_class.php");
	$helpdesk_obj = new helpdesk;
}

include_once(e_PLUGIN .  HELPDESK_FOLDER .  "/admin/left_menu.php");

class helpdesk_prefs_ui extends e_admin_ui
{

	protected $pluginTitle		= LAN_PREFS;
	protected $pluginName		= HELPDESK_FOLDER;


	protected $prefs = array(
		'hduprefs_seo' => array(
			'title'      => HDU_A504,
			'tab'        => 0,
			'type'       => 'radio',
			'data'       => 'int',
			'help'       => HDU_A505,
			'writeParms' => array(
				'optArray' => array(
					1 => HDU_A28, // Label for value 1
					2 => HDU_A29  // Label for value 2
				),
				'inline' => true,
				'fieldLabels' => array(
					1 => "<img src='" . HELPDESK_IMAGES_PATH . "admin_images/docs_16.png' alt='" . HDU_A503 . "' title='" . HDU_A503 . "' onclick='expandit(\"hdu_esca\")' />"
				)
			)
		),

		// Supervisor class
		'hduprefs_supervisorclass' => array(
			'title'      => HDU_A9,
			'type'       => 'userclass',
			'data'       => 'userclass',
			'help'       => HDU_A301,
			'writeParms' => array('classlist' => 'nobody,main,admin,classes')
		),

		// Post class
		'hduprefs_postclass' => array(
			'title'      => HDU_A203,
			'type'       => 'userclass',
			'data'       => 'userclass',
			'help'       => HDU_A204,
			'writeParms' => array('classlist' => 'nobody,member,main,admin,classes')
		),

		// User class
		'hduprefs_userclass' => array(
			'title'      => HDU_A11,
			'type'       => 'userclass',
			'data'       => 'userclass',
			'help'       => HDU_A302,
			'writeParms' => array('classlist' => 'public,nobody,member,main,admin,classes')
		),

		// Message top
		'hduprefs_messagetop' => array(
			'title'      => HDU_A107,
			'type'       => 'textarea',
			'data'       => 'str',
			'help'       => HDU_A303,
			'writeParms' => array('size' => 'block-level')
		),

		// Message bottom
		'hduprefs_messagebottom' => array(
			'title'      => HDU_A108,
			'type'       => 'textarea',
			'data'       => 'str',
			'help'       => HDU_A304,
			'writeParms' => array('size' => 'block-level')
		),

		// Phone
		'hduprefs_phone' => array(
			'title'      => HDU_A20,
			'type'       => 'text',
			'data'       => 'str',
			'help'       => HDU_A305,
			'writeParms' => array('size' => '30', 'maxlength' => 20)
		),

		// FAQ link
		'hduprefs_faq' => array(
			'title'      => HDU_A501,
			'type'       => 'text',
			'data'       => 'str',
			'help'       => HDU_A502,
			'writeParms' => array('size' => '80', 'maxlength' => 200)
		),

		// Rows per page
		'hduprefs_rows' => array(
			'title'      => HDU_A1,
			'type'       => 'number',
			'data'       => 'int',
			'help'       => HDU_A306,
			'writeParms' => array('size' => '5', 'maxlength' => 2)
		),

		// Menu title
		'hduprefs_menutitle' => array(
			'title'      => HDU_A15,
			'type'       => 'text',
			'data'       => 'str',
			'help'       => HDU_A307,
			'writeParms' => array('size' => '30', 'maxlength' => 30)
		),

		// Helpdesk title
		'hduprefs_title' => array(
			'title'      => HDU_A25,
			'type'       => 'text',
			'data'       => 'str',
			'help'       => HDU_A308,
			'writeParms' => array('size' => '30', 'maxlength' => 30)
		),

		// Hourly rate
		'hduprefs_hourlyrate' => array(
			'title'      => HDU_A115,
			'type'       => 'text',
			'data'       => 'str',
			'help'       => HDU_A309,
			'writeParms' => array('size' => '10', 'maxlength' => 10)
		),

		// Distance rate
		'hduprefs_distancerate' => array(
			'title'      => HDU_A116,
			'type'       => 'text',
			'data'       => 'str',
			'help'       => HDU_A310,
			'writeParms' => array('size' => '10', 'maxlength' => 10)
		),

		// Escalate every n days
		'hduprefs_escalatedays' => [
			'title' => HDU_A16,
			'type' => 'number',
			'size' => 'small',
			'writeParms' => ['maxlength' => 3],
			'help' => HDU_A311,
		],

		// Escalate on posted date or last action date
		'hduprefs_escalateon' => [
			'title' => HDU_A23,
			'type' => 'dropdown',
			'writeParms' => [
				'optArray' => [
					'1' => HDU_A21,
					'2' => HDU_A22,
				],
			],
			'help' => HDU_A312,
		],

		// Auto close after n days
		'hduprefs_autoclosedays' => [
			'title' => HDU_A17,
			'type' => 'number',
			'size' => 'small',
			'writeParms' => ['maxlength' => 3],
			'help' => HDU_A313,
		],

		// Resolution for auto close
		'hduprefs_autocloseres' => [
			'title' => HDU_A112,
			'type' => 'dropdown',
			'data' => 'int',
			'writeParms' => [
				'table' => 'hdu_resolve',
				'id' => 'hdures_id',
				'label' => 'hdures_resolution',
				'order' => 'hdures_resolution',
				'default' => [0 => HDU_A128],
			],
			'help' => HDU_A314,
		],

		// Default resolution for new tickets
		'hduprefs_defaultres' => [
			'title' => HDU_A114,
			'type' => 'dropdown',
			'data' => 'int',
			'writeParms' => [
				'table' => 'hdu_resolve',
				'id' => 'hdures_id',
				'label' => 'hdures_resolution',
				'order' => 'hdures_resolution',
				'default' => [0 => HDU_A128],
			],
			'help' => HDU_A315,
		],

		// Default assignment
		'hduprefs_assigned' => [
			'title' => HDU_A198,
			'type' => 'dropdown',
			'data' => 'int',
			'writeParms' => [
				'table' => 'hdu_resolve',
				'id' => 'hdures_id',
				'label' => 'hdures_resolution',
				'order' => 'hdures_resolution',
				'default' => [0 => HDU_A128],
			],
			'help' => HDU_A316,
		],

		// Default status for closed tickets
		'hduprefs_closestat' => [
			'title' => HDU_A199,
			'type' => 'dropdown',
			'data' => 'int',
			'writeParms' => [
				'table' => 'hdu_resolve',
				'id' => 'hdures_id',
				'label' => 'hdures_resolution',
				'order' => 'hdures_resolution',
				'default' => [0 => HDU_A128],
			],
			'help' => HDU_A317,
		],

		// Poster only view restrict list
		'hduprefs_posteronly' => [
			'title' => HDU_A14,
			'type' => 'boolean',
			'help' => HDU_A318,
		],

		// Allow user to reopen tickets
		'hduprefs_reopen' => [
			'title' => HDU_A19,
			'type' => 'boolean',
			'help' => HDU_A319,
		],

		// Comments visible to all
		'hduprefs_allread' => [
			'title' => HDU_A18,
			'type' => 'boolean',
			'help' => HDU_A320,
		],

		// Show asset tag
		'hduprefs_showassettag' => [
			'title' => HDU_A27,
			'type' => 'boolean',
			'help' => HDU_A321,
		],

		// Show fixes
		'hduprefs_showfixes' => [
			'title' => HDU_A110,
			'type' => 'boolean',
			'help' => HDU_A322,
		],

		// Show financials
		'hduprefs_showfinance' => [
			'title' => HDU_A111,
			'type' => 'boolean',
			'help' => HDU_A323,
		],


		
	);

	public function init()
	{
 
	}
	
 
	 

	// left-panel help menu area. (replaces e_help.php used in old plugins)
	public function renderHelp()
	{
		$caption = LAN_HELP;
		$text = 'Some help text';

		return array('caption' => $caption, 'text' => $text);
	}

 
}



class hhelpdesk_prefs_form_ui extends e_admin_form_ui
{
}


new helpdesk_adminArea();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN . "footer.php");
exit;


// require_once(e_ADMIN . "auth.php");
// if (!defined("ADMIN_WIDTH"))
// {
//     define(ADMIN_WIDTH, "width:100%;");
// }

// $hdu_msg = "";
// $hdu_ac_text = "";
// $hdu_ac_catopt = "";
// $hdu_ed = false;

// // Check that valid user class to do this if not tell them
// $hdu_ac_action = $_POST['hdu_ac_action'] ?? null;
// // * If we are updating then update or insert the record
// if ($hdu_ac_action == 'update')
// {
//     $hdu_ac_id = $_POST['hducat_id'];
//     if (empty($_POST['hducat_category']))
//     {
//         // category needs completing
//         $hdu_ac_text .= "
// 		<table style='" . ADMIN_WIDTH . "' class='fborder'>
// 		<tr>
// 			<td class='forumheader'>" . HDU_A185 . "</td>
// 		</tr>
// 		</table>";
//         $hdu_ac_action = 'dothings';
//         $_POST['hdu_ac_selcat'] = $hdu_ac_id;
//         $_POST['hdu_ac_recdel'] = 1;
//     }
//     else
//     {
//         $hdu_ac_id = $_POST['hducat_id'];
//         if ($hdu_ac_id == 0)
//         {
//             // New record so add it
//             $hdu_ac_args = "
// 		'0',
// 		'" . $tp->toDB($_POST['hducat_category']) . "',
// 		0,
// 		'" . intval($_POST['hducat_helpdesk']) . "'," . time();
//             if ($sql->db_Insert("hdu_categories", $hdu_ac_args, false))
//             {
//                 // saved OK
//                 $hdu_msg .= HDU_A45 ;
//             }
//             else
//             {
//                 // failed to save
//                 $hdu_msg .= HDU_A47;
//             }
//         }
//         else
//         {
//             // Update existing
//             $hdu_ac_args = "
// 		hducat_category='" . $tp->toDB($_POST['hducat_category']) . "',
// 		hducat_helpdesk='" . intval($_POST['hducat_helpdesk']) . "',
// 		hducat_lastupdate='" . time() . "'
// 		where hducat_id='" . intval($hdu_ac_id) . "'";

//             if ($sql->db_Update("hdu_categories", $hdu_ac_args, false))
//             {
//                 // Changes saved
//                 $hdu_msg .= HDU_A46;
//             }
//             else
//             {
//                 // unable to save changes
//                 $hdu_msg .= HDU_A48 ;
//             }
//         }
//         $helpdesk_obj->helpdesk_cache_clear();
//     }
// }
// // We are creating, editing or deleting a record
// if ($hdu_ac_action == 'dothings')
// {
//     $hdu_ac_id = intval($_POST['hdu_ac_selcat']);
//     $hdu_ac_do = intval($_POST['hdu_ac_recdel']);
//     $hdu_ac_dodel = false;
//     $hdu_ed = false;
//     switch ($hdu_ac_do)
//     {
//         case '1': // Edit existing record
//             {
//                 // We edit the record
//                 $sql->db_Select("hdu_categories", "*", "hducat_id='$hdu_ac_id'");
//                 $hdu_ac_row = $sql->db_Fetch() ;
//                 extract($hdu_ac_row);
//                 $hdu_ac_cap1 = HDU_A49;
//                 $hdu_ed = true;
//                 break;
//             }
//         case '2': // New department
//             {
//                 // Create new record
//                 $hducat_id = 0;
//                 // set all fields to zero/blank
//                 $hducat_category = "";
//                 $hdu_ac_cap1 = HDU_A50;
//                 $hdu_ed = true;
//                 break;
//             }
//         case '3':
//             {
//                 // delete the record
//                 if ($_POST['hdu_ac_okdel'] == '1')
//                 {
//                     // We are going to delete this record
//                     if ($sql->db_Count("hdunit", "(*)", "where hdu_category='$hdu_ac_id'"))
//                     {
//                         // Record in use
//                         $hdu_msg .= HDU_A64 ;
//                     }
//                     else
//                     {
//                         // Record not in use
//                         if ($sql->db_Delete("hdu_categories", " hducat_id='$hdu_ac_id'"))
//                         {
//                             // Deleted record OK
//                             $hdu_msg .= HDU_A62;
//                         }
//                         else
//                         {
//                             // Error deleting record
//                             $hdu_msg .= HDU_A63;
//                         }
//                     }
//                 }
//                 else
//                 {
//                     // Not confirmed deletion
//                     $hdu_msg .= HDU_A51 ;
//                 }
//                 $hdu_ac_dodel = true;
//             } # End case
//     } # end switch
//     if (!$hdu_ac_dodel)
//     {
//         $hdu_ac_text .= "
// <form id='deptformupdate' method='post' action='" . e_SELF . "'>
// 	<div>
// 		<input type='hidden' value='$hducat_id' name='hducat_id' />
// 		<input type='hidden' value='update' name='hdu_ac_action' />
// 		<input type='hidden' name='hdu_ac_selcat' value='" . $_POST['hdu_ac_selcat'] . "' />
// 	</div>
// 	<table style='" . ADMIN_WIDTH . "' class='fborder'>
// 		<tr>
// 			<td colspan='2' class='fcaption'>$hdu_ac_cap1</td>
// 		</tr>
// 		<tr>
// 			<td style='width:30%' class='forumheader3'>" . HDU_A52 . "</td>
// 			<td class='forumheader3'><input type='text' size='30' maxlength='30' class='tbox' name='hducat_category' value='" . $tp->toFORM($hducat_category) . "' /></td>
// 		</tr>";
//         $hdu_ac_selhelp = "<select name='hducat_helpdesk' class='tbox'>
// 		<option value='0'>" . HDU_A171 . "</option>";
//         if ($sql->db_Select("hdu_helpdesk", "*", " order by hdudesk_name", "nowhere"))
//         {
//             while ($hdu_ac_rowsel = $sql->db_Fetch())
//             {
//                 extract($hdu_ac_rowsel);

//                 $hdu_ac_selhelp .= "<option value='$hdudesk_id'" .
//                 ($hdudesk_id == $hducat_helpdesk?" selected='selected'":"") . ">" . $tp->toFORM($hdudesk_name) . "</option>";
//             }
//             $hdu_ac_selhelp .= "</select>";
//         }
//         else
//         {
//             $hdu_ac_selhelp .= "<select name='hducat_helpdesk' class='tbox'>
// 		<option value='0'>" . HDU_A141 . "</option></select>";
//         }
//         $hdu_ac_text .= "
// 		<tr>
// 			<td class='forumheader3'>" . HDU_A172 . "</td>
// 			<td class='forumheader3'>$hdu_ac_selhelp&nbsp;<img src='". HELPDESK_IMAGES_PATH."admin_images/docs_16.png'  alt='".HDU_A503."' title='".HDU_A503."' onclick='expandit(\"hdu_scat\")' />
// 			<div id='hdu_scat' style='display:none' ><em>" . HDU_A330 . "</em></div></td>
// 		</tr>
// 				<tr>
// 			<td colspan='2' class='forumheader2'><input type='submit' name='submitit' value='" . HDU_A61 . "' class='button' /></td>
// 		</tr>
// 		<tr>
// 			<td colspan='2' class='fcaption'>&nbsp;</td>
// 		</tr>
// 	</table>
// </form>";
//     }
// }


// if (!$hdu_ed)
// {
//     // Get the category names to display in combo box
//     // then display actions available
//     $hdu_ac_yes = false;
//     if ($sql2->db_Select("hdu_categories", "hducat_id,hducat_category", " order by hducat_category", "nowhere", false))
//     {
//         $hdu_ac_yes = true;
//         while ($hdu_ac_row = $sql2->db_Fetch())
//         {
//             extract($hdu_ac_row);
//             $hdu_ac_catopt .= "<option value='$hducat_id' " . ($hducat_id == $_POST['hdu_ac_selcat']?"selected='selected'":"") . ">" . $tp->toFORM($hducat_category) . "</option>";
//         }
//     }
//     else
//     {
//         $hdu_ac_catopt .= "<option value='0'>" . HDU_A134 . "</option>";
//     }

//     $hdu_ac_text .= "
// <form id='hducatform' method='post' action='" . e_SELF . "'>
// 	<div>
// 		<input type='hidden' value='dothings' name='hdu_ac_action' />
// 	</div>
// 	<table style='" . ADMIN_WIDTH . "' class='fborder'>
// 		<tr>
// 			<td colspan='2' class='fcaption'>" . HDU_A54 . "</td>
// 		</tr>
// 			<tr>
// 				<td colspan='2' class='forumheader2'><b>$hdu_msg</b>&nbsp;</td>
// 			</tr>

// 		<tr>
// 			<td style='width:20%;' class='forumheader3'>" . HDU_A31 . "</td>
// 			<td class='forumheader3'><select name='hdu_ac_selcat' class='tbox'>$hdu_ac_catopt</select></td>
// 		</tr>
// 		<tr>
// 			<td style='width:20%;' class='forumheader3'>" . HDU_A59 . "</td><td class='forumheader3'><input type='radio' style='border:0px;' name='hdu_ac_recdel'  class='radio' id='hdu_ac_recdelE'  value='1' " . ($hdu_ac_yes?"checked='checked'":"disabled='disabled'") . " /><label for='hdu_ac_recdelE' > " . HDU_A75 . "</label><br />
// 				<input type='radio' name='hdu_ac_recdel' style='border:0px;' class='radio' id='hdu_ac_recdelN' value='2' " . (!$hdu_ac_yes?"checked='checked'":"") . "/><label for='hdu_ac_recdelN' > " . HDU_A76 . "</label><br />
// 				<input type='radio' name='hdu_ac_recdel' style='border:0px;' class='radio' id='hdu_ac_recdelD' value='3' /><label for='hdu_ac_recdelD' > " . HDU_A77 . "</label>
// 				<input type='checkbox'  style='border:0px;'  name='hdu_ac_okdel' id='hdu_ac_okdel' class='tbox' value='1' /><label for='hdu_ac_okdel' > " . HDU_A78 . "</label>
// 			</td>
// 		</tr>
// 				<tr>
// 			<td colspan='2' class='forumheader2'><input type='submit' name='submits' value='" . HDU_A60 . "' class='tbox' /></td>
// 		</tr>
// 		<tr>
// 			<td colspan='2' class='fcaption'>&nbsp;</td>
// 		</tr>
// 	</table>
// </form>";
// }
// $ns->tablerender(HDU_A2, $hdu_ac_text);
// require_once(e_ADMIN . "footer.php");

// ?>

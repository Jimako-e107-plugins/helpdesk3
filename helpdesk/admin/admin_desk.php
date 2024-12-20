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
$hdu_msg = "";
$hdu_text = "";
$hdu_ac_catopt = "";
$hdu_ac_text = "";

include_once(e_PLUGIN . HELPDESK_FOLDER .  "/admin/left_menu.php");

class hdu_helpdesk_ui extends e_admin_ui
{

	protected $pluginTitle		= HDU_A154;
	protected $pluginName		= 'helpdesk';
	//	protected $eventName		= 'helpdesk-'; // remove comment to enable event triggers in admin. 		
	protected $table			= 'hdu_helpdesk';
	protected $pid				= 'hdudesk_id';
	protected $perPage			= 10;
	protected $batchDelete		= true;
	protected $batchExport     = true;
	protected $batchCopy		= true;

	//	protected $sortField		= 'somefield_order';
	//	protected $sortParent      = 'somefield_parent';
	//	protected $treePrefix      = 'somefield_title';

	//	protected $tabs				= array('tab1'=>'Tab 1', 'tab2'=>'Tab 2'); // Use 'tab'=>'tab1'  OR 'tab'=>'tab2' in the $fields below to enable. 

	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.

	protected $listOrder		= 'hdudesk_id DESC';

	protected $fields 		= array(
		'checkboxes'              => array('title' => '', 'type' => null, 'data' => null, 'width' => '5%', 'thclass' => 'center', 'forced' => 'value', 'class' => 'center', 'toggle' => 'e-multiselect', 'readParms' => [], 'writeParms' => [],),
		'hdudesk_id'              => array('title' => LAN_ID, 'type' => 'number', 'data' => 'int', 'width' => '5%', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'hdudesk_name'            => array('title' => HDU_A152, 'type' => 'text', 'data' => 'safestr', 'width' => 'auto', 'inline' => true, 'validate' => true, 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'hdudesk_email'           => array('title' => HDU_A155, 'type' => 'email', 'data' => 'safestr', 'width' => 'auto', 'inline' => true, 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'hdudesk_class'           => array('title' => HDU_A192, 'type' => 'userclass', 'data' => 'int', 'width' => 'auto', 'batch' => true, 'filter' => true, 'inline' => true, 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		//	'hdudesk_order'           => array('title' => LAN_ORDER, 'type' => 'number', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		//'hdudesk_lastupdate'      => array('title' => LAN_UPDATE, 'type' => 'datestamp', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'options'                 => array('title' => LAN_OPTIONS, 'type' => null, 'data' => null, 'width' => '10%', 'thclass' => 'center last', 'class' => 'center last', 'forced' => 'value', 'readParms' => [], 'writeParms' => [],),
	);

	protected $fieldpref = array('hdudesk_name', 'hdudesk_class', 'hdudesk_email', 'hdudesk_order');


	//	protected $preftabs        = array('General', 'Other' );
	protected $prefs = array();

	//large, xlarge, xxlarge, block-level
	public function init()
	{

		$this->getRequest()->setMode('desk');

		$this->fields['hdudesk_name']['writeParms']['size'] = 'xlarge';

		$this->postFilterMarkup = $this->AddButton();
	}

	function AddButton()
	{
		$mode = $this->getRequest()->getMode();

		$text = "</fieldset>
			</form>
			<div class='e-container'>
      			<table  style='" . ADMIN_WIDTH . "' class='table adminlist table-striped'>
					<tr>
						<td>";
		$text .=			'<a href="' . e_SELF . '?mode=' . $mode . '&action=create" class="btn batch e-hide-if-js btn-success"><span>' . LAN_CREATE . '</span></a>';
		$text .= "		</td>
					</tr>
				</table>
			</div>
			<form>
				<fieldset>";
		return $text;
	}


	// left-panel help menu area. (replaces e_help.php used in old plugins)
	public function renderHelp()
	{
		$caption = LAN_HELP;
		$text = 'Some help text';

		return array('caption' => $caption, 'text' => $text);
	}

	public function beforeCreate($new_data, $old_data)
	{
		$new_data['hdudesk_lastupdate'] = time();
		return $new_data;
	}

	public function beforeUpdate($new_data, $old_data, $id)
	{
		$new_data['hdudesk_lastupdate'] = time();
		return $new_data;
	}
}



class hdu_helpdesk_form_ui extends e_admin_form_ui
{
}

// require_once(e_ADMIN . "auth.php");
// if (!defined("ADMIN_WIDTH"))
// {
//     define(ADMIN_WIDTH, "width:100%;");
// }

// $hdu_ac_action = $_POST['action'] ?? null;
// $hdu_ac_edit = false;
// // * If we are updating then update or insert the record
// if ($hdu_ac_action == 'update')
// {
//     $hdu_ac_id = $_POST['hdudesk_id'];
//     if (empty($_POST['hdudesk_name']))
//     {
//         $hdu_ac_text .= HDU_A186;
//         $hdu_ac_action = 'dothings';
//         $_POST['hdu_ac_seldesk'] = $hdu_ac_id;
//         $_POST['hdu_ac_recdel'] = 1;
//     }
//     else
//     {
//         if ($hdu_ac_id == 0)
//         {
//             // New record so add it
//             $hdu_ac_args = "
// 		'0',
// 		'" . $tp->toDB($_POST['hdudesk_name']) . "',
// 		'" . intval($_POST['hdudesk_class']) . "',
// 		'" . $tp->toDB($_POST['hdudesk_email']) . "',
// 		0," . time();
//             if ($sql->db_Insert("hdu_helpdesk", $hdu_ac_args, false))
//             {
//                 // created OK
//                 $hdu_ac_text .= HDU_A165 ;
//             }
//             else
//             {
//                 // failed to create
//                 $hdu_ac_text .= HDU_A167 ;
//             }
//             $hdu_ac_edit = false;
//         }
//         else
//         {
//             // Update existing
//             $hdu_ac_args = "
// 		hdudesk_name='" . $tp->toDB($_POST['hdudesk_name']) . "',
// 		hdudesk_class='" . intval($_POST['hdudesk_class']) . "',
// 		hdudesk_email='" . $tp->toDB($_POST['hdudesk_email']) . "',
// 		hdudesk_lastupdate='" . time() . "'
// 		where hdudesk_id='" . intval($hdu_ac_id) . "'";
//             if ($sql->db_Update("hdu_helpdesk", $hdu_ac_args, false))
//             {
//                 // Changes saved
//                 $hdu_ac_text .= HDU_A153;
//             }
//             else
//             {
//                 // failed to save changes
//                 $hdu_ac_text .= HDU_A168 ;
//             }
//             $hdu_ac_edit = false;
//         }
//         $helpdesk_obj->helpdesk_cache_clear();
//     }
// }
// // We are creating, editing or deleting a record
// if ($hdu_ac_action == 'dothings')
// {
//     $hdu_ac_id = intval($_POST['hdu_ac_seldesk']);
//     $hdu_ac_do = intval($_POST['hdu_ac_recdel']);
//     $hdu_ac_dodel = false;
//     switch ($hdu_ac_do)
//     {
//         case '1': // Edit existing record
//             {
//                 // We edit the record
//                 $sql->db_Select("hdu_helpdesk", "*", "hdudesk_id='$hdu_ac_id'");
//                 $hdu_ac_row = $sql->db_Fetch() ;
//                 extract($hdu_ac_row);
//                 $hdu_ac_cap1 = HDU_A149;
//                 $hdu_ac_edit = true;
//                 break;
//             }
//         case '2': // New department
//             {
//                 // Create new record
//                 $hdudesk_id = 0;
//                 // set all fields to zero/blank
//                 $hdudesk_fix = "";
//                 $hdudesk_cost = 0;
//                 $hdu_ac_cap1 = HDU_A150;
//                 $hdu_ac_edit = true;
//                 break;
//             }
//         case '3':
//             {
//                 // delete the record
//                 if ($_POST['hdu_ac_okdel'] == '1')
//                 {
//                     // We are going to delete this record
//                     if ($sql->db_Count("hdu_categories", "(*)", "where hducat_helpdesk='$hdu_ac_id'"))
//                     {
//                         // Record in use
//                         $hdu_ac_text .= HDU_A164 ;
//                     }
//                     else
//                     {
//                         // Record not in use
//                         if ($sql->db_Delete("hdu_helpdesk", " hdudesk_id='$hdu_ac_id'"))
//                         {
//                             // Deleted record OK
//                             $hdu_ac_text .= HDU_A162 ;
//                             $helpdesk_obj->helpdesk_cache_clear();
//                         }
//                         else
//                         {
//                             // Error deleting record
//                             $hdu_ac_text .= HDU_A163;
//                         }
//                     }
//                 }
//                 else
//                 {
//                     // Not confirmed deletion
//                     $hdu_ac_text .= HDU_A71;
//                 }
//                 $hdu_ac_dodel = true;
//                 $hdu_ac_edit = false;
//             } # End case
//     } # end switch
//     if (!$hdu_ac_dodel)
//     {
//         $hdu_text .= "
// 	<form id='deptformupdate' method='post' action='" . e_SELF . "'>
// 		<div>
// 			<input type='hidden' value='$hdudesk_id' name='hdudesk_id' />
// 			<input type='hidden' value='update' name='action' />
// 			<input type='hidden' name='hdu_ac_seldesk' value='" . $_POST['hdu_ac_seldesk'] . "' />
// 		</div>
// 		<table style='" . ADMIN_WIDTH . "' class='fborder'>
// 			<tr>
// 				<td colspan='2' class='fcaption'>$hdu_ac_cap1</td>
// 			</tr>
// 			<tr>
// 				<td style='width:20%;' class='forumheader3'>" . HDU_A152 . "</td>
// 				<td  class='forumheader3'>
// 					<input type='text' size='30' maxlength='30' class='tbox' name='hdudesk_name' value='" . $tp->toFORM($hdudesk_name) . "' />
// 				</td>
// 			</tr>
// 			<tr>
// 				<td style='width:20%;' class='forumheader3'>" . HDU_A155 . "</td>
// 				<td  class='forumheader3'>
// 					<input type='text' size='30' maxlength='30' class='tbox' name='hdudesk_email' value='" . $tp->toFORM($hdudesk_email) . "' />
// 				</td>
// 			</tr>
// 			<tr>
// 				<td style='width:20%' class='forumheader3'>" . HDU_A192 . "</td>
// 				<td class='forumheader3'>" . r_userclass("hdudesk_class", $hdudesk_class, "off", "classes") . "</td>
// 			</tr>
// 			<tr>
// 				<td colspan='2' class='forumheader2'>
// 					<input type='submit' name='submitit' value='" . HDU_A81 . "' class='button' />
// 				</td>
// 			</tr>
// 			<tr>
// 				<td colspan='2' class='fcaption'>
// 					&nbsp;
// 				</td>
// 			</tr>
// 		</table>
// 	</form>";
//     }
// }
// // This was the heldesk email - now we email all the technicians in the class instead
// // <tr>
// // <td style='width:20%;' class='forumheader3'>" . HDU_A155 . "</td>
// // <td  class='forumheader3'><input type='text' size='42' maxlength='100' class='tbox' name='hdudesk_email' value='" . $tp->toFORM($hdudesk_email) . "' /></td>
// // </tr>
// // end
// if (!$hdu_ac_edit)
// {
//     // Get the department names to display in combo box
//     // then display actions available
//     $hdu_ac_yes = false;
//     if ($sql2->db_Select("hdu_helpdesk", "hdudesk_id,hdudesk_name", " order by hdudesk_name", "nowhere"))
//     {
//         $hdu_ac_yes = true;
//         while ($hdu_ac_row = $sql2->db_Fetch())
//         {
//             extract($hdu_ac_row);
//             $hdu_ac_catopt .= "<option value='$hdudesk_id' " . ($hdudesk_id == $_POST['hdu_ac_seldesk']?"selected='selected'":"") . ">" . $tp->toFORM($hdudesk_name) . "</option>";
//         }
//     }
//     else
//     {
//         $hdu_ac_catopt .= "<option value='0'>" . HDU_A141 . "</option>";
//     }
//     $hdu_text .= "
// <form id='hducatform' method='post' action='" . e_SELF . "'>
// 	<div>
// 		<input type='hidden' value='dothings' name='action' />
// 	</div>
// 	<table style='" . ADMIN_WIDTH . "' class='fborder'>
// 		<tr>
// 			<td colspan='2' class='fcaption'>" . HDU_A154 . "</td>
// 		</tr>
// 		<tr>
// 			<td colspan='2' class='forumheader2'><b>$hdu_ac_text</b>&nbsp;</td>
// 		</tr>
// 		<tr>
// 			<td style='width:20%;' class='forumheader3'>" . HDU_A140 . "</td><td class='forumheader3'><select name='hdu_ac_seldesk' class='tbox'>$hdu_ac_catopt</select></td>
// 		</tr>
// 		<tr>
// 			<td style='width:20%;' class='forumheader3'>" . HDU_A59 . "</td><td class='forumheader3'><input type='radio' style='border:0px;' name='hdu_ac_recdel'  class='radio' id='hdu_ac_recdelE'  value='1' " . ($hdu_ac_yes?"checked='checked'":"disabled='disabled'") . " /><label for='hdu_ac_recdelE' > " . HDU_A75 . "</label><br />
// 				<input type='radio' name='hdu_ac_recdel' style='border:0px;' class='radio' id='hdu_ac_recdelN' value='2' " . (!$hdu_ac_yes?"checked='checked'":"") . "/><label for='hdu_ac_recdelN' > " . HDU_A76 . "</label><br />
// 				<input type='radio' name='hdu_ac_recdel' style='border:0px;' class='radio' id='hdu_ac_recdelD' value='3' /><label for='hdu_ac_recdelD' > " . HDU_A77 . "</label>
// 				<input type='checkbox'  style='border:0px;'  name='hdu_ac_okdel' id='hdu_ac_okdel' class='tbox' value='1' /><label for='hdu_ac_okdel' > " . HDU_A78 . "</label>
// 			</td>
// 		</tr>
// 		<tr>
// 			<td colspan='2' class='forumheader2'><input type='submit' name='submits' value='" . HDU_A80 . "' class='button' /></td>
// 		</tr>
// 		<tr>
// 			<td colspan='2' class='fcaption'>&nbsp;</td>
// 		</tr>
// 	</table>
// </form>";
// }

// $ns->tablerender(HDU_A2, $hdu_text);
// require_once(e_ADMIN . "footer.php");


new helpdesk_adminArea();

require_once(e_ADMIN . "auth.php");
e107::getAdminUI()->runPage();

require_once(e_ADMIN . "footer.php");
exit;

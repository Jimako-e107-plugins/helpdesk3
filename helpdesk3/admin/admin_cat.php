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

include_once(e_PLUGIN . "helpdesk3/admin/left_menu.php");

class hdu_categories_ui extends e_admin_ui
{

	protected $pluginTitle		= 'Help Desk';
	protected $pluginName		= 'helpdesk3';
	//	protected $eventName		= 'helpdesk3-hdu_categories'; // remove comment to enable event triggers in admin. 		
	protected $table			= 'hdu_categories';
	protected $pid				= 'hducat_id';
	protected $perPage			= 10;
	protected $batchDelete		= true;
	protected $batchExport     = true;
	protected $batchCopy		= true;

	//	protected $sortField		= 'somefield_order';
	//	protected $sortParent      = 'somefield_parent';
	//	protected $treePrefix      = 'somefield_title';

	//	protected $tabs				= array('tab1'=>'Tab 1', 'tab2'=>'Tab 2'); // Use 'tab'=>'tab1'  OR 'tab'=>'tab2' in the $fields below to enable. 

	//	protected $listQry      	= "SELECT * FROM `#tableName` WHERE field != '' "; // Example Custom Query. LEFT JOINS allowed. Should be without any Order or Limit.

	protected $listOrder		= 'hducat_id DESC';

	protected $fields 		= array(
		'checkboxes'              => array('title' => '', 'type' => null, 'data' => null, 'width' => '5%', 'thclass' => 'center', 'forced' => 'value', 'class' => 'center', 'toggle' => 'e-multiselect', 'readParms' => [], 'writeParms' => [],),
		'hducat_id'               => array('title' => LAN_ID, 'type' => 'number', 'data' => 'int', 'width' => '5%', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'hducat_category'         => array('title' => LAN_CATEGORY, 'type' => 'text', 'data' => 'safestr', 'width' => 'auto', 'batch' => false, 'filter' => true, 'inline' => true, 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
	//	'hducat_order'            => array('title' => LAN_ORDER, 'type' => 'number', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'hducat_helpdesk'         => array('title' => HDU_A172, 'type' => 'dropdown', 'data' => 'int', 'width' => 'auto', 'filter' => true, 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left', 'batch' => false,),
	//	'hducat_lastupdate'       => array('title' => 'Lastupdate', 'type' => 'datestamp', 'data' => 'int', 'width' => 'auto', 'help' => '', 'readParms' => [], 'writeParms' => [], 'class' => 'left', 'thclass' => 'left',),
		'options'                 => array('title' => LAN_OPTIONS, 'type' => null, 'data' => null, 'width' => '10%', 'thclass' => 'center last', 'class' => 'center last', 'forced' => 'value', 'readParms' => [], 'writeParms' => [],),
	);

	protected $fieldpref = array('hducat_category', 'hducat_order', 'hducat_helpdesk');

	//	protected $filterSort = ['field_key_5', 'field_key_7']; // Display these fields first in the filter drop-down. 

	//	protected $batchSort = ['field_key_5', 'field_key_7'];; // Display these fields first in the batch drop-down.


	//	protected $preftabs        = array('General', 'Other' );
	protected $prefs = array();


	public function init()
	{
		$this->getRequest()->setMode('cat');

		$this->fields['hducat_category']['writeParms']['size'] = 'xlarge';
		$this->fields['hducat_helpdesk']['help'] = HDU_A330;
		$this->postFilterMarkup = $this->AddButton();

		// Set drop-down values (if any). 
 		$cats = e107::getDb()->retrieve('hdu_helpdesk', "hdudesk_id, hdudesk_name", true,  true,  'hdudesk_id'); 
		 
	 	
 
		// Check if the PHP version is 7.4 or newer
		if (version_compare(PHP_VERSION, '7.4.0', '>='))
		{
			// Use arrow function for PHP 7.4+
			$result = array_map(fn($cats) => $cats['hdudesk_name'], $cats);
		}
		else
		{
			// Use anonymous function for older PHP versions
			$result = array_map(function ($cat)
			{
				return $cat['hdudesk_name'];
			}, $cats);
		}
		$result[0] = HDU_A171;
		$this->fields['hducat_helpdesk']['writeParms']['optArray'] = $result; // Example Drop-down array. 

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

	// ------- Customize Create --------

	public function beforeCreate($new_data, $old_data)
	{
		$new_data['hducat_lastupdate'] = time();
		return $new_data;
	}

	public function afterCreate($new_data, $old_data, $id)
	{
		// do something
	}

	public function onCreateError($new_data, $old_data)
	{
		// do something		
	}


	// ------- Customize Update --------

	public function beforeUpdate($new_data, $old_data, $id)
	{
		$new_data['hducat_lastupdate'] = time();
		return $new_data;
	}

	public function afterUpdate($new_data, $old_data, $id)
	{
		// do something	
	}

	public function onUpdateError($new_data, $old_data, $id)
	{
		// do something		
	}

	// left-panel help menu area. (replaces e_help.php used in old plugins)
	public function renderHelp()
	{
		$caption = LAN_HELP;
		$text = 'Some help text';

		return array('caption' => $caption, 'text' => $text);
	}

 
}



class hdu_categories_form_ui extends e_admin_form_ui
{
}


new helpdesk3_adminArea();

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

<?php
if (!defined('e107_INIT')){exit;}
function print_item($id)
{
        // moved to e_print.php
}

function email_item($id)
{
//        return e107::getAddon('forum','e_print')->render($thread_id); // Quick Fix
//global $tp, $sql;
$sql = e107::getdb();
/* Why is this needed here? Leftovers?
    require_once(e_PLUGIN . HELPDESK_FOLDER . "/includes/helpdesk_class.php");
    if (!is_object($helpdesk_obj))
    {
        $helpdesk_obj = new helpdesk;
    }
*/
    $hdu_arg = "select * from #hdunit
		left join #hdu_categories on hdu_category=hducat_id
		left join #hdu_helpdesk on hducat_helpdesk=hdudesk_id
		left join #hdu_resolve on  hdu_resolution=hdures_id
		where hdu_id = $id";
    $sql->gen($hdu_arg, false);
    $row = $sql->fetch();
    $hdu_message = HDU_235 . "<br /><br />" . HDU_238 . " <a href='" . SITEURL . e_PLUGIN . HELPDESK_FOLDER . "/helpdesk.php?0.show.$id'>" . HDU_239 . "</a><br /><br />";
    $hdu_message .= "<br /><br />" . HDU_236 . " <b>" . e107::getParser()->toHTML($row['hdu_summary']) . "</b> " . HDU_237 . " <b>$id</b><br />" ;
    return $hdu_message;
}

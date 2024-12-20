<?php
if (e_LANGUAGE != "English" && file_exists(e_PLUGIN . HELPDESK_FOLDER . "/languages/" . e_LANGUAGE . ".php"))
{
    include_once(e_PLUGIN . HELPDESK_FOLDER . "/languages/" . e_LANGUAGE . ".php");
} 
else
{
    include_once(e_PLUGIN . HELPDESK_FOLDER . "/languages/English.php");
}

$helpdesk_title = HDU_199;
$search_info[]=array("sfile" => e_PLUGIN. HELPDESK_FOLDER . "search.php", "qtype" => $helpdesk_title, "refpage" => "helpdesk.php");

?>

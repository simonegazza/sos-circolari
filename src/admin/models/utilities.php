<?php
function getAcademicYear() {
    $month = idate("m");
    $academic_year = idate("d");
    if ($academic_year < 31 && $month < 8) { return "" . (idate("Y") -1) . "/" . idate("Y"); }
    else { return "" . idate("Y") . "/" . (idate("Y") + 1 ); }
}

function flat(array $elems, $key) {
    for ($i = 0; $i < count($elems); $i++) { $flat[$i] = $elems[$i][$key]; }
    return $flat;
}

function getAcademicYearsInDB() {
	$db = JFactory::getDbo();
	$query = $db->getQuery(true);
	$query->select("anno_scolastico")
		->from("sos_circolari")
		->group("anno_scolastico");
	$db->setQuery($query);
	$result = flat(\Joomla\Utilities\ArrayHelper::fromObject($db->loadObjectList()), "anno_scolastico");
	return $result;
}

function getRecipientGroupsInDB() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select("title")
        ->from("sos_circolari")
        ->join("inner", "sos_gruppi_destinatari on sos_circolari.id = id_circolare")
        ->join("inner", "j_usergroups on id_gruppo = j_usergroups.id")
        ->group("title");
    $db->setQuery($query);
    $result = flat(\Joomla\Utilities\ArrayHelper::fromObject($db->loadObjectList()), "title");
    return $result;
}
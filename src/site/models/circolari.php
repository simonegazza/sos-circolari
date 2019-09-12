<?php
defined("_JEXEC") or die("Restricted access");
require_once JPATH_COMPONENT_ADMINISTRATOR . "/models/utilities.php";

class SosCircolariModelCircolari extends JModelList {
    protected function populateState($ordering = "IdCircolare", $direction = "desc") {
        $app = JFactory::getApplication();

        $value = $app->getUserStateFromRequest($this->context . ".filter.search", "filter_anno");
        $this->setState("filter.search", $value);

        if (isset($_REQUEST["filter_anno"])) {
            $value = $app->getUserStateFromRequest($this->context . ".filter.anno", "filter_anno");
            $this->setState("filter.anno", $value);
        } else {
            $this->setState("filter.anno", getAcademicYear());
        }

        if (isset($_REQUEST["filter_group"])) {
            $value = $app->getUserStateFromRequest($this->context . ".filter.group", "filter_group");
            $this->setState("filter.group", $value);
        } else {
            $this->setState("filter.group", "");
        }

        $value = $app->getUserStateFromRequest($this->context . ".filter.search", "filter_search");
        $this->setState("filter.search", $value);

        parent::populateState($ordering, $direction);
    }
    protected function getListQuery() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select("sos_circolari.id, sos_circolari.numero, sos_circolari.oggetto, sos_circolari.anno_scolastico, sos_circolari.bozza, j_users.name, sos_circolari.data_pubblicazione")
            ->from("sos_circolari")
            ->join("inner", "j_users ON sos_circolari.autore = j_users.id")
            ->order("numero desc");

        $filterSearchBar = $this->getState("filter.search");
        $filterAcademicYear = $this->getState("filter.anno");
        $filterRecipientGroup = $this->getState("filter.group");

        if(!empty($filterSearchBar) || $filterSearchBar != "") {
            $search = $db->quote("%" . $filterSearchBar . "%");
            $query->where(["(oggetto LIKE " . $search . " OR testo LIKE ". $search .")"]);
        }

        if(!empty($filterAcademicYear) || $filterAcademicYear != "") { $query->where(["anno_scolastico = " . $db->quote($filterAcademicYear)]); }

        if(!empty($filterRecipientGroup) || $filterRecipientGroup != "") {
            $query->join("left", "sos_gruppi_destinatari on sos_circolari.id = id_circolare")
                ->join("left", "j_usergroups on id_gruppo = j_usergroups.id")
                ->where(["title = " . $db->quote($filterRecipientGroup)]);
        }

        return $query;
    }
}

<?php
defined('_JEXEC') or die('Restricted access');

class SosCircolariModelCircolari extends JModelList
{
    protected function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("sos_circolari.id, sos_circolari.numero, sos_circolari.oggetto, sos_circolari.anno_scolastico, sos_circolari.bozza, j_users.name, sos_circolari.data_pubblicazione")
            ->from("sos_circolari")
            ->join("inner", "j_users ON sos_circolari.autore = j_users.id")
            ->order("numero desc");

        return $query;
    }
}

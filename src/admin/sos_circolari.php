<?php
function getAnnoScolastico() {
        $month = idate("m");
        $day = idate("d");
        if ($day < 31 && $month < 8) {
            return "" . (idate("Y") -1) . "/" . idate("Y");
        } else {
            return "" . idate("Y") . "/" . (idate("Y") + 1 );
    }
}


function getNumber() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("numero")
        ->from("sos_circolari")
        ->where("numero is not null")
        ->order("numero desc")
        ->setLimit("1");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    return empty(\Joomla\Utilities\ArrayHelper::fromObject($result[0])["numero"]) ? 1 : ((int)\Joomla\Utilities\ArrayHelper::fromObject($result[0])["numero"]) + 1;
}

class Circolare {
    public $id;
    public $number;
    public $title;
    public $body;
    public $draft;
    public $is_private;
    public $ay;
    public $record_number;
    public $location;
    public $user_actions;
    public $userId;
    public $publication_date;

/*public function getId() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("id")
        ->from("sos_circolari")
        ->order("id desc")
        ->setLimit("1");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    return (int)$result["id"];
}*/

    public function __construct(array $config) {
        //$this->id = $this->getId() + 1;
        $this->title = $config["oggetto"];
        $this->body = $config["testo"];
        $this->draft = $config["bozza"];
        $this->ay = $config["anno_scolastico"];
        $this->is_private = $config["privata"];
        $this->record_number = $config["protocollo"];
        $this->location = $config["luogo"];
        $this->user_actions = $config["sos_azioni_utente"];

        $user = &JFactory::getUser();
        $this->userId = (int)$user->id;

        $this->number = $config["numero"] ?
            $config["numero"] :
            $config["bozza"] ?
                "NULL" :
                getNumber();

        $this->publication_date = $config[data_pubblicazione] ?
            $config[data_pubblicazione] :
            $config["bozza"] ?
                "NULL" :
                str_replace("-","",date("Y-m-d"));
    }

    public function createCircolare() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
            "numero",
            "oggetto",
            "testo",
            "autore",
            "bozza",
            "data_pubblicazione",
            "anno_scolastico",
            "sos_azioni_utente",
            "protocollo",
            "privata",
            "luogo"
        ];

        $values = [
            $this->number,
            $db->quote($this->title),
            $db->quote($this->body),
            $this->userId,
            $this->draft,
            $this->publication_date,
            $db->quote($this->ay),
            $this->user_actions,
            $db->quote($this->record_number),
            $this->is_private,
            $db->quote($this->location)
        ];

        $query->insert($db->quoteName("sos_circolari"))
            ->columns($db->quoteName($columns))
            ->values(implode(",", $values));

        $db->setQuery($query)
            ->execute();
    }

    public function deleteCircolare() {
      $db = JFactory::getDbo();
      $query = $db->getQuery(true);

      $conditions = [$db->quoteName("id") . " = " . $this->id];

      $query->delete($db->quoteName("sos_circolari"))
          ->where($conditions);

      $db->setQuery($query)
         ->execute();
    }
}

function readForWidget() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("numero, oggetto")
        ->from("sos_circolari")
        ->where("bozza = 0 and privata = 0")
        ->order("numero desc");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    var_dump($result);
    return $result;
}

function readMultiple() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("sos_circolari.oggetto, sos_circolari.numero, sos_circolari.anno_scolastico, sos_circolari.bozza, j_users.name, sos_circolari.data_pubblicazione, sos_risposte.id_utente, sos_risposte.id_azione_utente")
        ->from("sos_circolari")
        ->join("inner", $db->quoteName("j_users") . " on (" . $db->quoteName("sos_circolari.autore") . " = " . $db->quoteName("j_users.id") . ")")
        ->join("inner", $db->quoteName("sos_risposte") . " on (" . $db->quoteName("sos_circolari.id") . " = " . $db->quoteName("sos_risposte.id_circolare") . ")")
        ->order("numero desc");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    var_dump($result);
    return $result;
}

function readSingle() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("*")
        ->from("sos_circolari")
        ->join("inner", $db->quoteName("j_users") . " on (" . $db->quoteName("sos_circolari.autore") . " = " . $db->quoteName("j_users.id") . ")")
        ->join("inner", $db->quoteName("sos_circolari_allegati") . " on (" . $db->quoteName("sos_circolari.id") . " = " . $db->quoteName("sos_circolari_allegati.id_circolare") . ")")
        ->join("inner", $db->quoteName("sos_allegati") . " on (" . $db->quoteName("sos_circolari_allegati.id_allegato") . " = " . $db->quoteName("sos_allegati.id") . ")")
        ->join("inner", $db->quoteName("sos_azioni_utente") . " on (" . $db->quoteName("sos_circolari.sos_azioni_utente") . " = " . $db->quoteName("sos_azioni_utente.id") . ")")
        ->join("inner", $db->quoteName("sos_gruppi_destinatari") . " on (" . $db->quoteName("sos_circolari.id") . " = " . $db->quoteName("sos_gruppi_destinatari.id_circolare") . ")")
        ->join("inner", $db->quoteName("j_usergroups") . " on (" . $db->quoteName("sos_gruppi_destinatari.id_gruppo") . " = " . $db->quoteName("j_usergroups.id") . ")")
        ->join("inner", $db->quoteName("sos_risposte") . " on (" . $db->quoteName("sos_circolari.id") . " = " . $db->quoteName("sos_risposte.id_circolare") . ")")
        ->join("inner", $db->quoteName("sos_utenti_destinatari") . " on (" . $db->quoteName("sos_circolari.id") . " = " . $db->quoteName("sos_utenti_destinatari.id_circolare") . ")")
        ->order("numero desc");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    var_dump($result);
    return $result;
}

//readForWidget();
//readMultiple();
//readSingle();
//$prova = new Circolare($circolare);
//$prova->createCircolare();
//$prova->deleteCircolare();

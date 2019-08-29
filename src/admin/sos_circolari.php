<?php
class Circolare {
    public $title;
    public $body;
    public $is_published;
    public $is_private;
    public $ay;
    public $record_number;
    public $location; 
    public $user_actions;
    public $userId;
    public $publication_date;

    public $number;

    public function getAnnoScolastico() {
        $month = idate("m");
        $day = idate("d");
        if ($day < 31 && $month < 8) {
            return "" . (idate("Y") -1) . "/" . idate("Y");
        } else {
            return "" . idate("Y") . "/" . (idate("Y") + 1 );
        }
    }

    public function __construct(array $config) {
        $this->title = $config["oggetto"];
        $this->body = $config["testo"];
        $this->is_published = $config["bozza"];
        $this->ay = $config["anno_scolastico"];
        $this->is_private = $config["privata"];
        $this->record_number = $config["protocollo"];
        $this->location = $config["luogo"];
        $this->user_actions = $config["sos_azioni_utente"];
        $user = &JFactory::getUser();
        $this->userId = (int)$user->id;
        $this->publication_date = (int)$config["data_pubblicazione"];
        $this->number = $config["numero"];
    }

    public function createCircolare() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
            'oggetto',
            'testo',
            'autore',
            'bozza',
            'data_pubblicazione',
            'anno_scolastico',
            'sos_azioni_utente',
            'protocollo',
            'privata',
            'luogo'
        ];

        $values = [
            $db->quote($this->title),
            $db->quote($this->body),
            $this->userId,
            $this->is_published,
            $this->publication_date,
            $db->quote($this->getAnnoScolastico()),
            $this->user_actions,
            $db->quote($this->record_number),
            $this->is_private,
            $db->quote($this->location)
        ];

        $query
            ->insert($db->quoteName('sos_circolari'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

      $db->setQuery($query);
      $db->execute();
    }

    public function deleteCircolare() {
      $db = JFactory::getDbo();

      $query = $db->getQuery(true);

      $conditions = array(
        $db->quoteName('numero') . ' = ' . $this->number//$db->quote($this->number), 
      );

      $query->delete($db->quoteName('sos_circolari'));
      $query->where($conditions);

      $db->setQuery($query);

      $result = $db->execute();
    }
}


$circolare = [
    "oggetto" => "test",
    "testo" => "testtestprova",
    "bozza" => 0,
    "data_pubblicazione" => $bozza ? str_replace("-","",date("Y-m-d")) : NULL,
    "anno_scolastico" => "2018/2019",
    "sos_azioni_utente" => 1,
    "privata" => 1,
    "protocollo" => "4499/C",
    "luogo" => "Reggio Emilia",
    "numero" => 3
];

$prova = new Circolare($circolare);
$prova->createCircolare();
$prova->deleteCircolare();

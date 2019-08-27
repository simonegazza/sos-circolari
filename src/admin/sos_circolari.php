<?php
class Circolare {
    public $title;
    public $body;
    public $is_published;
    public $is_private;
    public $ay;
    public $record_number;
    public $location; // = "Reggio nell'Emilia"; //controllare che si possa fare in php
    public $user_actions;

//    public function getAnnoScolastico() {
//        $now = date("Y-m-d");
//        if data.giorno <31 && data.mese < 08 {
//            return "data.anno-1/data.anno"
//        } else {
//            return "data.anno/data.anno+1"
//        }
//        //ternario ^ (?)
//    }

    public function __construct(array $config) {
        $this->title = $config["oggetto"];
        $this->body = $config["testo"];
        $this->is_published = $config["bozza"];
        $this->ay = $config["anno_scolastico"];
        $this->is_private = $config["privata"];
        $this->record_number = $config["protocollo"];
        $this->location = $config["luogo"];
       $this->user_actions = $config["sos_azioni_utente"];
    }

    public function createCircolare() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $user = &JFactory::getUser();
        $userId = $user->get('id');

        $columns = array(
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
          );

        $values = array(
            $db->quote($this->title),
            $db->quote($this->body),
            1,//$userId,
            $this->is_published,                                            //TODO delete not null in SQL
            $this->is_published ? date("Y-m-d") : NULL,
            $db->quote($this->ay), //$this->getAnnoScolastico(),                     //TODO get it from eslewhere
            $this->user_actions,
            $db->quote($this->record_number),
            $this->is_private,
            $db->quote($this->location)
          );


        //TODO: insert into MYSQL
        $query
            ->insert($db->quoteName('sos_circolari'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        $db->setQuery($query);
        $db->execute();
    }
}
$circolare = [
    "oggetto" => "test",
    "testo" => "testtestprova",
    "bozza" => 1,
    "anno_scolastico" => "2018/2019",
    "sos_azioni_utente" => 1,
    "privata" => 0,
    "protocollo" => "11111",
    "luogo" => "Reggio Emilia"
];

$prova = new Circolare($circolare);
$prova->createCircolare();

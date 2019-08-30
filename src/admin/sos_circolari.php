<?php
function getAnnoScolastico() {
        $month = idate("m");
        $dacademic_year = idate("d");
        if ($dacademic_year < 31 && $month < 8) {
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

function getLatestArticleId() {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("id")
        ->from("j_content")
        ->where("id is not null")
        ->order("id desc")
        ->setLimit("1");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    return empty(\Joomla\Utilities\ArrayHelper::fromObject($result[0])["id"]) ? 1 : ((int)\Joomla\Utilities\ArrayHelper::fromObject($result[0])["id"]) + 1;
}

function getAssetId(string $assetTitle) {
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query
        ->select("id")
        ->from("j_assets")
        ->where("id is not null")   //->where($db->quoteName("title") .  " = " . $db->quote($assetTitle))
        ->order("id desc")
        ->setLimit("1");
    $db->setQuery($query)
        ->execute();

    $result = $db->loadObjectList();
    return empty(\Joomla\Utilities\ArrayHelper::fromObject($result[0])["id"]) ? 1 : ((int)\Joomla\Utilities\ArrayHelper::fromObject($result[0])["id"]) + 1;
}

class Circolare {
    public $id;
    public $number;
    public $title;
    public $body;
    public $draft;
    public $is_private;
    public $academic_year;
    public $record_number;
    public $location;
    public $user_actions;
    public $userId;
    public $publication_date;

    public $attachment_name;
    public $attachment_url;

    public $lca_id_attachment;
    public $lca_id_circolare;

    public $lcug_id_usergroup;
    public $lcug_id_circolare;

    public $lcu_id_user;
    public $lcu_id_circolare;

    public $parent_id;
    public $level;
    public $asset_name;
    public $rules;

    public $asset_id;
    public $article_alias;
    public $html_body;
    public $state;
    public $catid;
    public $article_date;
    public $access;
    public $featured;


    public function __construct(array $config) {
        $this->title = $config["oggetto"];
        $this->body = $config["testo"];
        $this->draft = $config["bozza"];
        $this->academic_year= $config["anno_scolastico"];
        $this->is_private = $config["privata"];
        $this->record_number = $config["protocollo"];
        $this->location = $config["luogo"];
        $this->user_actions = $config["sos_azioni_utente"];
        
        $this->attachment_name = $config["nome_allegato"];
        $this->attachment_url = $config["url_allegato"];
        $this->lca_id_attachment = $config["id_allegato"];
        $this->lca_id_circolare = $config["id_circolare"];

        $this->lcug_id_usergroup = $config["id_gruppo"];
        $this->lcug_id_circolare = $config["id_circolare"];

        $this->lcu_id_user = $config["id_utente"];
        $this->lcu_id_circolare = $config["id_circolare"];

        $user = &JFactory::getUser();
        $this->userId = (int)$user->id;
        
        $this->number = $config["numero"] ?
            $config["bozza"] ?
                "NULL" : getNumber() : "NULL";
        
        $this->publication_date = $config["data_pubblicazione"] ?
            $config["bozza"] ?
                "NULL" : str_replace("-","",date("Y-m-d")) : "NULL";
        
        $this->parent_id = 27;
        $this->level = 3;
        $this->asset_name = $config["asset_name"]; 
        $this->rules = "{}";

        $this->asset_id = ((int)getAssetId($config["oggetto"]));
        $this->article_alias = "circolare-" . ((string)getNumber());
        $this->html_body = "<p>" . $this->body . "</p>";
        $this->state = 1;
        $this->catid = $config["catid"];
        $this->article_date = ((string)date("Y-m-d H:i:s"));
        $this->access = 1;
        $this->featured = 0;
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
            $db->quote($this->academic_year),
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

    public function createAllegato() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
            "nome",
            "url"
        ];

        $values = [
            $db->quote($this->attachment_name),
            $db->quote($this->attachment_url)
        ];

        $query->insert($db->quoteName("sos_allegati"))
            ->columns($db->quoteName($columns))
            ->values(implode(",", $values));

        $db->setQuery($query)
            ->execute();
    }

    public function linkCircolareAllegato() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
            "id_allegato",
            "id_circolare"
        ];

        $values = [
            $this->lca_id_attachment,
            $this->lca_id_circolare
        ];

        $query->insert($db->quoteName("sos_circolari_allegati"))
            ->columns($db->quoteName($columns))
            ->values(implode(",", $values));

        $db->setQuery($query)
            ->execute();
    }

    public function linkCircolareUsergroup() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
            "id_gruppo",
            "id_circolare"
        ];

        $values = [
            $this->lcug_id_usergroup,
            $this->lcug_id_circolare
        ];

        $query->insert($db->quoteName("sos_gruppi_destinatari"))
            ->columns($db->quoteName($columns))
            ->values(implode(",", $values));

        $db->setQuery($query)
            ->execute();
    }

    public function linkCircolareUser() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
            "id_utente",
            "id_circolare"
        ];

        $values = [
            $this->lcu_id_user,
            $this->lcu_id_circolare
        ];

        $query->insert($db->quoteName("sos_utenti_destinatari"))
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

    public function createArticleAsset() {     
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
          "parent_id",  //27
          "level",      //3
          "name",       //com_content.article.X
          "title",
          "rules"       //{}
        ];

        $values = [
          $this->parent_id,
          $this->level,
          $db->quote($this->asset_name),
          $db->quote($this->title),
          $db->quote($this->rules)
        ];

        $query->insert($db->quoteName("j_assets"))
            ->columns($db->quoteName($columns))
            ->values(implode(",", $values));

        $db->setQuery($query)
            ->execute();
    }

    public function createArticleContent() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $columns = [
          "asset_id",   // get asset id
          "title",      // oggetto
          "alias",      // circolare-numero
          "introtext",  // create article body
          "state",      // 1
          "catid",      // cat id, default 2 (uncategorised)
          "created",    // current date
          "created_by", // autore circolare
          "publish_up", // current date
          "access",     // 1
          "featured"
        ];

        $values = [
          $this->asset_id,
          $db->quote($this->title),
          $db->quote($this->article_alias),
          $db->quote($this->html_body),
          $this->state,
          $this->catid,
          $db->quote($this->article_date),
          $this->userId,
          $db->quote($this->article_date),
          $this->access,
          $this->featured
        ];
        
        $query->insert($db->quoteName("j_content"))
            ->columns($db->quoteName($columns))
            ->values(implode(",", $values));

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

$circolare = [
      "numero" => $bozza ? getNumber() : "NULL",
	    "oggetto" => "test",
	    "testo" => "testtestprova",
	    "bozza" => 0,
	    "data_pubblicazione" => (int)$bozza ? str_replace("-","",date("Y-m-d")) : "NULL",
	    "anno_scolastico" => "2018/2019",
	    "sos_azioni_utente" => 1,
	    "privata" => 1,
	    "protocollo" => "4499/C",
      "luogo" => "Reggio Emilia",
        "nome_allegato" => "",
        "url_allegato" => "",
          "id_allegato" => 1,
          "id_circolare" => 1,
          "id_gruppo" => 1,
            "id_utente" => 951,
      "asset_name" => "com_content.article." . ((string)getLatestArticleId()),
      "catid" => 2
];

//readForWidget();
//readMultiple();
//readSingle();
$prova = new Circolare($circolare);
$prova->createCircolare();
//$prova->createAllegato();
//$prova->linkCircolareAllegato();
//$prova->linkCircolareUsergroup();
//$prova->linkCircolareUser();
//$prova->deleteCircolare();

$prova->createArticleAsset();
$prova->createArticleContent();

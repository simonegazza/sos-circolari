<?php

defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal');

use \Joomla\Utilities\ArrayHelper;

class com_sos_circolariInstallerScript {

    private $oldAzioniUtente = [];
    private $oldCircolari = [];
    private $oldCircolariRisposte = [];
    private $oldGruppiEsclusi = [];
    private $oldConfigurazioni = [];

    private function getConf($db) {
        $query = $db->getQuery(true);
        $query->select("*")->from($db->quoteName("#__com_sos_configurazioni"));
        $db->setQuery($query)->execute();
        $confs = [];
        foreach (ArrayHelper::fromObject($db->loadObjectList()) as $conf) { $confs[$conf["chiave"]] = $conf["valore"]; }
        return $confs;
    }

    private function getContentTableInDB($db, $table) {
        $query = $db->getQuery(true);
        $query->select("*")->from($db->quoteName($table));
        $db->setQuery($query)->execute();
        return ArrayHelper::fromObject($db->loadObjectList());
    }

    private function getOldAutori($db) {
        $queryAutori = $db->getQuery(true);
        $queryAutori->select("id, autore")
            ->from($db->quoteName("#__sos_circolari"))
            ->join("left", $db->quoteName("#__users") . "on username = autore")
            ->group("autore");
        $db->setQuery($queryAutori)->execute();
        foreach (ArrayHelper::fromObject($db->loadObjectList()) as $elem) { $autori[$elem["autore"]] = $elem["id"]; }
        return $autori;
    }

    private function getGruppiEsclusi($db) {
        $queryGruppi = $db->getQuery(true);
        $queryGruppi->select("id")
            ->from($db->quoteName("#__sos_circolari_gruppi_esclusi"))
            ->join("inner", $db->quoteName("#__usergroups") . "on title = Nome")
            ->group("title");
        $db->setQuery($queryGruppi)->execute();
        return ArrayHelper::fromObject($db->loadObjectList());
    }

    private function insertConfigurazioni($db) {
        $queryInsertConfigurazioni= $db->getQuery(true);
        $columns = ["id", "chiave","valore"];
        $queryInsertConfigurazioni->insert($db->quoteName("#__com_sos_configurazioni"))->columns($columns);
        foreach ($this->oldConfigurazioni as $conf) {
            $values = [
                $conf["idConfig"],
                $db->quote($conf["keyConfig"]),
                $db->quote($conf["valueConfig"])
            ];
            $queryInsertConfigurazioni->values(implode(",", $values));
        }
        $db->setQuery($queryInsertConfigurazioni)->execute();
    }

    private function insertAzioniUtente($db) {
        $queryInsertAzioniUtente = $db->getQuery(true);
        $columns = [ "id", "azione" ];
        $queryInsertAzioniUtente->insert($db->quoteName("#__com_sos_azioni_utente"))->columns($columns);
        foreach ($this->oldAzioniUtente as $azione) {
            $queryInsertAzioniUtente->values("" . $azione["Id"] . "," . $db->quote($azione["Opzioni"]));
        }
        $db->setQuery($queryInsertAzioniUtente)->execute();
    }

    private function insertCircolari($db) {
        $queryInsertCircolari = $db->getQuery(true);
        $queryAttachments = $db->getQuery(true);
        $queryGroups = $db->getQuery(true);
        $queryAttachmentsCircolari = $db->getQuery(true);

        $confs = $this->getConf($db);
        $oldAutori = $this->getOldAutori($db);

        $columns = [
            "id",
            "numero",
            "oggetto",
            "testo",
            "autore",
            "bozza",
            "data_pubblicazione",
            "data_fine_interazione",
            "anno_scolastico",
            "azioni_utente",
            "protocollo",
            "privata",
            "luogo"
        ];

        $queryInsertCircolari->insert($db->quoteName("#__com_sos_circolari"))->columns($columns);
        $queryAttachments->insert($db->quoteName("#__com_sos_allegati"))->columns(["id_circolare", "nome"]);
        $queryGroups->insert($db->quoteName("#__com_sos_gruppi_destinatari"))->columns(["id_gruppo", "id_circolare"]);

        foreach ($this->oldCircolari as $circolare) {
            $values = [
                $circolare["IdCircolare"],
                $circolare["NumeroCircolare"],
                $db->quote($circolare["Oggetto"]),
                $db->quote($circolare["Testo"]),
                $oldAutori[$circolare["Autore"]],
                $circolare["Stato"] ? 0 : 1,
                $db->quote($circolare["DataPubblicazione"]),
                $db->quote($circolare["DataFineInterazioni"]),
                $db->quote($circolare["AnnoScolastico"]),
                $circolare["TemplateOpzioni"],
                $db->quote($circolare["NumeroProtocollo"]),
                $circolare["isPrivate"] === "false" ? 0 : 1,
                $db->quote($circolare["Luogo"])
            ];
            $queryInsertCircolari->values(implode(",", $values));
            foreach (json_decode($circolare["Allegati"], true) as $attachment) {
                $queryAttachments->values("" .  $circolare["IdCircolare"] . "," . $db->quote($attachment) . "" );
            }
            foreach (json_decode($circolare["GruppiDestinatari"]) as $group) {
                $queryGroups->values("". $group . "," . $circolare["IdCircolare"]);
            }
        }
        $db->setQuery($queryInsertCircolari)->execute();
        $db->setQuery($queryAttachments)->execute();
        $db->setQuery($queryGroups)->execute();
    }

    private function insertRisposte($db) {
        $queryInsertRisposte = $db->getQuery(true);
        $columns = ["id", "id_circolare", "id_utente", "id_azione_utente", "azione", "data_risposta"];
        $queryInsertRisposte->insert($db->quoteName("#__com_sos_circolari_risposte"))->columns($columns);
        foreach ($this->oldCircolariRisposte as $risposta) {
            $azione = json_decode($risposta["Risposta"], true);
            $idAzione = array_keys($azione)[0];
            $values = [
                $risposta["idRisposta"],
                $risposta["IdCircolare"],
                $risposta["idUser"],
                $idAzione,
                $db->quote($azione[$idAzione]),
                $db->quote($risposta["DataRisposta"])
            ];
            $queryInsertRisposte->values(implode(",", $values));
        }
        $db->setQuery($queryInsertRisposte)->execute();
    }

    private function insertGrupiEsclusi($db) {
        $queryInsertGruppoEsclusi= $db->getQuery(true);
        $queryInsertGruppoEsclusi->insert($db->quoteName("#__com_sos_circolari_gruppi_esclusi"))->columns(["id"]);
        foreach ($this->oldGruppiEsclusi as $gruppo) {
            $queryInsertGruppoEsclusi->values($gruppo["id"]);
        }
        $db->setQuery($queryInsertGruppoEsclusi)->execute();
    }

    /**
     * method to install the component
     *
     * @return void
     */

    function install($parent) {
    }

    /**
     * method to uninstall the component
     *
     * @return void
     */
    function uninstall($parent) {
    }

    /**
     * method to update the component
     *
     * @return void
     */
    function update($parent) {
    }

    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    function preflight($type, $parent) {
        $db = JFactory::getDbo();
        $this->oldConfigurazioni = $this->getContentTableInDB($db, "#__sos_circolari_configuration");
        $this->oldAzioniUtente = $this->getContentTableInDB($db, "#__sos_circolari_opzioni");
        $this->oldCircolari = $this->getContentTableInDB($db, "#__sos_circolari");
        $this->oldCircolariRisposte = $this->getContentTableInDB($db, "#__sos_circolari_risposte");
        $this->oldGruppiEsclusi = $this->getGruppiEsclusi($db);
    }

    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    function postflight($type, $parent) {
        $db = JFactory::getDbo();
        $this->insertAzioniUtente($db);
        $this->insertConfigurazioni($db);
        $this->insertCircolari($db);
        $this->insertRisposte($db);
        $this->insertGrupiEsclusi($db);
    }
}

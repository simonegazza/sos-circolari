CREATE TABLE IF NOT EXISTS sos_gruppi (
	id INT NOT NULL,
	email VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_utenti (
	id INT NOT NULL,
	email VARCHAR(255) NOT NULL,
	gruppo INT NOT NULL,

	FOREIGN KEY (gruppo) REFERENCES sos_gruppi (id),
	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_azioni_utente (
	id INT NOT NULL,
	azione VARCHAR(255) NOT NULL,

	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_circolari (
	numero INT UNIQUE NOT NULL,
	oggetto TEXT NOT NULL,
	testo TEXT NOT NULL,
	autore INT NOT NULL,
	bozza TINYINT NOT NULL,
	data_pubblicazione DATE NOT NULL,
--	data_fine_interazione DATE NOT NULL,
	anno_scolastico VARCHAR(10) NOT NULL,
	sos_azioni_utente INT NOT NULL,
	protocollo VARCHAR(255) NOT NULL,
	privata TINYINT NOT NULL,
	luogo VARCHAR(255) NOT NULL,

	FOREIGN KEY (autore) REFERENCES sos_utenti (id),
	FOREIGN KEY (sos_azioni_utente) REFERENCES sos_azioni_utente (id),
	PRIMARY KEY (numero)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_utenti_destinatari (
	id_utente INT NOT NULL,
	id_circolare INT NOT NULL,

	FOREIGN KEY (id_circolare) REFERENCES sos_circolari(numero),
	FOREIGN KEY (id_utente) REFERENCES sos_utenti(id),
	PRIMARY KEY (id_utente, id_circolare)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_gruppi_destinatari (
	id_gruppo INT NOT NULL,
	id_circolare INT NOT NULL,

	FOREIGN KEY (id_circolare) REFERENCES sos_circolari(numero),
	FOREIGN KEY (id_gruppo) REFERENCES sos_gruppi(id),
	PRIMARY KEY (id_gruppo, id_circolare)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_allegati (
	id INT NOT NULL,
	nome TEXT NOT NULL,
	url TEXT NOT NULL,

	PRIMARY KEY (id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_circolari_allegati (
	id_allegato INT NOT NULL,
	id_circolare INT NOT NULL,

	FOREIGN KEY (id_circolare) REFERENCES sos_circolari(numero),
	FOREIGN KEY (id_allegato) REFERENCES sos_allegati(id),
	PRIMARY KEY (id_allegato, id_circolare)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sos_risposte (
	id INT NOT NULL,
	id_circolare INT NOT NULL,
	id_utente INT NOT NULL,
	id_azione_utente INT NOT NULL,
	data_risposta DATE NOT NULL,

	FOREIGN KEY (id_circolare) REFERENCES sos_circolari(numero),
	FOREIGN KEY (id_azione_utente) REFERENCES sos_azioni_utente(id),
	FOREIGN KEY (id_utente) REFERENCES sos_utenti(id),

	PRIMARY KEY (id, id_circolare)
) ENGINE=InnoDB;

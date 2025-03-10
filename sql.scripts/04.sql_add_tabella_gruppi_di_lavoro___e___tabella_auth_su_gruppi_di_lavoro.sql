CREATE TABLE `rebuilding_gruppi_di_lavoro` (
  `idrebuilding_gld` int(11) NOT NULL AUTO_INCREMENT,
  `gdl_titolo` varchar(512) DEFAULT NULL,
  `gdl_testo` varchar(512) DEFAULT NULL,
  `gdl_icona` varchar(255) DEFAULT NULL,
  `gdl_tipo` varchar(16) DEFAULT "tematici",
  PRIMARY KEY (`idrebuilding_gld`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


CREATE TABLE `rebuilding_gruppi_di_lavoro_auth` (
  `idrebuilding_gld_auth` int(11) NOT NULL AUTO_INCREMENT,
  `fk_idrebuilding_gld` int(11) null ,		-- foreign key verso la tabella dei gruppi di lavoro
  `codice_fiscale` varchar(16) DEFAULT NULL,
  `canView` boolean DEFAULT FALSE,
  `canEdit` boolean DEFAULT FALSE,
  PRIMARY KEY (`idrebuilding_gld_auth`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
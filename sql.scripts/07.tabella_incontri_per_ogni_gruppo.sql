

/*
 * Tabella degli incontri tenuti dal gruppo di lavoro.
 * 
 * 
 * */
CREATE TABLE `rebuilding_gruppi_di_lavoro_incontri` (
  `idincontro` int(11) NOT NULL AUTO_INCREMENT,
  `fk_idrebuilding_gld` int(11) NOT NULL ,
  `incontro_titolo` varchar(512) DEFAULT 'titolo',
  `incontro_abstract` varchar(512) DEFAULT 'abstract',
  `incontro_giorno` DATE default NULL,
  `incontro_file_agenda` varchar(256) default NULL ,
  `incontro_file_verbale` varchar(256) default NULL,
  `incontro_file_video` varchar(256) default null,
  `incontro_dt_created` DATETIME default null,
  `incontro_op_created` varchar(256) default null,
  `incontro_dt_last_modified` DATETIME default null,
  `incontro_op_last_modified` varchar(256) default null
  PRIMARY KEY (`idincontro`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
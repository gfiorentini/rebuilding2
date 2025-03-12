

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
  `incontro_giorno` DATETIME default null,
  `incontro_file_agenda` varchar(256) default NULL ,
  `incontro_file_verbale` varchar(256) default NULL,
  `incontro_file_video` varchar(256) default null,
  `incontro_dt_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `incontro_op_created` varchar(256) default null,
  `incontro_dt_last_modified` TIMESTAMP default CURRENT_TIMESTAMP,
  `incontro_op_last_modified` varchar(256) default null ,
  PRIMARY KEY (`idincontro`)
) ENGINE=MyISAM ;
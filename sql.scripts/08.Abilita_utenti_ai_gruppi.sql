

INSERT INTO rebuilding_gruppi_di_lavoro_auth  (fk_idrebuilding_gld, codice_fiscale, canView, canEdit)  
select idrebuilding_gld, 'FRNGCM70R19E388C', 1, 1  from rebuilding_gruppi_di_lavoro;



INSERT INTO rebuilding_gruppi_di_lavoro_auth (
fk_idrebuilding_gld, codice_fiscale, canView, canEdit) VALUES(0, NULL, NULL, 0, 0);


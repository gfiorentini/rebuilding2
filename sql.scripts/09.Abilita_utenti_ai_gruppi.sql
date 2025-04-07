INSERT INTO rebuilding_gruppi_di_lavoro_auth  (fk_idrebuilding_gld, codice_fiscale, canView, canEdit)  
select idrebuilding_gld, 'MSSPLA85T69C615H', 1, 1  from rebuilding_gruppi_di_lavoro;
insert into rebuilding_gruppi_di_lavoro_auth 
(fk_idrebuilding_gld, codice_fiscale, canView, canEdit )
select 32, operatore_codicefiscale, 1, 1 from dara_operatore where operatore_codicefiscale is not null




select * from rebuilding_gruppi_di_lavoro where idrebuilding_gld=23

select * from rebuilding_gruppi_di_lavoro_auth where fk_idrebuilding_gld = 23 and codice_fiscale = 'FRNGCM70R19E388C'

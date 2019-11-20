-- Query 1
with local_anomalia_count(latitude, longitude, nome, anomalia_count)
as (select L.latitude, L.longitude, L.nome, COUNT(*)
    from (local_publico natural join item) as L, incidencia as I
    where L.id = I.item_id
    group by (latitude, longitude))
select latitude, longitude, nome
from local_anomalia_count
where anomalia_count = (select MAX(anomalia_count) from local_anomalia_count);


-- Query 2
with utilizador_regular_tmp(email, anomalia_count)
     as (select I.email, COUNT(A.id)
         from (utilizador_regular natural join incidencia) as I, anomalia A
         where I.anomalia_id = A.id
               and A.tem_anomalia_redacao = false
               and TIMESTAMP '2019-01-01' <= A.ts
               and A.ts < TIMESTAMP '2019-07-01'
         group by I.email)
select email
from utilizador_regular_tmp
where anomalia_count = (select MAX(anomalia_count) from utilizador_regular_tmp);


-- Query 3
with norte_rio_maior as (select * from local_publico
                         where latitude > 39.336775),
     utilizador_local(email, latitude, longitude)
       as (select I.email, LP.latitude, LP.longitude
           from incidencia as I, anomalia A,
                (item natural join local_publico) as LP
           where I.item_id = LP.id
                 and I.anomalia_id = A.id
                 and extract(year from A.ts) = 2019)
select distinct email
from utilizador_local U1
where not exists (select latitude, longitude from norte_rio_maior
                  except
                  select latitude, longitude from utilizador_local U2
                  where U1.email = U2.email);


-- Query 4
with incidencia_qual_sul(email, anomalia_id)
       as (select I.email, I.anomalia_id
           from (utilizador_qualificado natural join incidencia) as I,
                (item natural join local_publico) as LP, anomalia A
           where I.item_id = LP.id
                 and I.anomalia_id = A.id
                 and LP.latitude < 39.336775
                 and extract(year from A.ts) = extract(year from current_date))
select distinct U1.email
from incidencia_qual_sul U1
where exists (select U2.anomalia_id
              from incidencia_qual_sul U2
              where U2.email = U1.email
              except
              select C.anomalia_id
              from (proposta_de_correcao natural join correcao) as C
              where C.email = U1.email);

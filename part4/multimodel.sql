drop table d_utilizador cascade;
drop table d_tempo cascade;
drop table d_local cascade;
drop table d_lingua cascade;
drop table f_anomalia cascade;

create table d_utilizador (
    id_utilizador serial,
    email         varchar(255) not null,
    tipo          varchar(255) not null,
    constraint pk_d_utilizador primary key(id_utilizador) 
);

insert into d_utilizador (email, tipo)
select email, 'qualificado' 
as tipo from utilizador_qualificado 
union 
select email, 'regular' 
as tipo from utilizador_regular;

create table d_tempo (
    id_tempo        serial,
    dia             integer not null,
    dia_da_semana   integer not null,
    semana          integer not null,
    mes             integer not null,
    trimestre       integer not null,
    ano             integer not null,
    constraint pk_d_tempo primary key(id_tempo)
);

insert into d_tempo(dia, dia_da_semana, semana, mes, trimestre, ano)
select  extract(day from ts) as dia, 
        extract(isodow from ts) ,
        extract(week  from ts) as semana, 
        extract(month from ts) as mes, 
        extract(quarter from ts) as trimestre, 
        extract(year from ts) as ano 
from anomalia
union 
select extract(day from data_hora) as dia, 
        extract(isodow from data_hora) ,
        extract(week  from data_hora) as semana, 
        extract(month from data_hora) as mes, 
        extract(quarter from data_hora) as trimestre, 
        extract(year from data_hora) as ano
from proposta_de_correcao;




create table d_local (
    id_local    serial,
    latitude    numeric(8, 6) not null,
    longitude   numeric(9, 6) not null,
    nome        varchar(255)  not null,
    constraint pk_d_local primary key(id_local)
);

insert into d_local (latitude, longitude, nome)
select latitude, longitude, nome from local_publico;


create table d_lingua (
    id_lingua   serial,
    lingua      varchar(255) not null,
    constraint pk_d_lingua primary key(id_lingua)
);

insert into d_lingua (lingua)
select distinct lingua from anomalia 
union 
select distinct lingua2 from anomalia_traducao;


create table f_anomalia (
    id_utilizador   integer,
    id_tempo        integer,
    id_local        integer,
    id_lingua       integer,
    tipo_anomalia   varchar(255) not null,
    com_proposta    boolean not null,
    constraint fk_utilizador foreign key(id_utilizador) 
    references d_utilizador(id_utilizador) on delete cascade,
    constraint fk_tempo foreign key(id_tempo) 
    references d_tempo(id_tempo) on delete cascade,
    constraint fk_local foreign key(id_local) 
    references d_local(id_local) on delete cascade,
    constraint fk_lingua foreign key(id_lingua) 
    references d_lingua(id_lingua) on delete cascade,
    constraint pk_f_anomalia primary key(id_utilizador, id_tempo, id_local, id_lingua)
);


insert into f_anomalia
select  U.id_utilizador, T.id_tempo, L.id_local, G.id_lingua, 'redação', true
        from anomalia as A, correcao as C, incidencia as I, 
        item as M ,d_utilizador as U, d_tempo as T, d_local as L, d_lingua as G  
        where A.tem_anomalia_redacao = true 
        and A.id not in (select id from anomalia_traducao) 
        and I.anomalia_id = A.id 
        and M.id = I.item_id 
        and C.anomalia_id = A.id 
        and extract(day from A.ts) = T.dia 
        and extract(month from A.ts) = T.mes 
        and extract(year from A.ts) = T.ano 
        and L.latitude = M.latitude 
        and L.longitude = M.longitude 
        and U.email = I.email 
        and A.lingua = G.lingua
union
select  U.id_utilizador, T.id_tempo, L.id_local, G.id_lingua, 'redação', false 
        from anomalia as A, incidencia as I, 
        item as M ,d_utilizador as U, d_tempo as T, d_local as L, d_lingua as G  
        where A.tem_anomalia_redacao = true 
        and A.id not in (select id from anomalia_traducao)
        and A.id not in (select anomalia_id from correcao)
        and I.anomalia_id = A.id 
        and M.id = I.item_id 
        and extract(day from A.ts) = T.dia 
        and extract(month from A.ts) = T.mes 
        and extract(year from A.ts) = T.ano 
        and L.latitude = M.latitude 
        and L.longitude = M.longitude 
        and U.email = I.email 
        and A.lingua = G.lingua
union
select  U.id_utilizador, T.id_tempo, L.id_local, G.id_lingua, 'traducao', false 
        from anomalia as A, incidencia as I, 
        item as M ,d_utilizador as U, d_tempo as T, d_local as L, d_lingua as G  
        where A.tem_anomalia_redacao = false 
        and A.id not in (select anomalia_id from correcao)
        and I.anomalia_id = A.id 
        and M.id = I.item_id 
        and extract(day from A.ts) = T.dia 
        and extract(month from A.ts) = T.mes 
        and extract(year from A.ts) = T.ano 
        and L.latitude = M.latitude 
        and L.longitude = M.longitude 
        and U.email = I.email 
        and A.lingua = G.lingua
union
select  U.id_utilizador, T.id_tempo, L.id_local, G.id_lingua, 'traducao', true 
        from anomalia as A, incidencia as I, correcao as C, 
        item as M ,d_utilizador as U, d_tempo as T, d_local as L, d_lingua as G  
        where A.tem_anomalia_redacao = false 
        and C.anomalia_id = A.id
        and I.anomalia_id = A.id 
        and M.id = I.item_id 
        and extract(day from A.ts) = T.dia 
        and extract(month from A.ts) = T.mes 
        and extract(year from A.ts) = T.ano 
        and L.latitude = M.latitude 
        and L.longitude = M.longitude 
        and U.email = I.email 
        and A.lingua = G.lingua;

-- insert into f_anomalia values(1, 7, 4, 3, 'tradução', TRUE);
-- insert into f_anomalia values(2, 10, 2, 7, 'tradução', FALSE);
-- insert into f_anomalia values(2, 5, 3, 6, 'tradução', TRUE);
-- insert into f_anomalia values(3, 6, 4, 8, 'redação', TRUE);  --3
-- insert into f_anomalia values(3, 8, 5, 5, 'redação', TRUE);  --9
-- insert into f_anomalia values(3, 1, 1, 4, 'redação', TRUE);  --11
-- insert into f_anomalia values(4, 10, 2, 7, 'tradução', FALSE); --5
-- insert into f_anomalia values(4, 11, 5, 2, 'tradução', FALSE); --8
-- insert into f_anomalia values(5, 10, 4, 5, 'tradução', TRUE); --1
-- insert into f_anomalia values(5, 10, 2, 8, 'redação', TRUE); --6
-- insert into f_anomalia values(5, 2, 5, 1, 'tradução', TRUE); --7


-- select  U.id_utilizador, 'redação', 't' from anomalia as A, correcao as C, incidencia as I natural join d_utilizador as U  where A.tem_anomalia_redacao = true and A.id not in (select id from anomalia_traducao) and I.anomalia_id = A.id and C.anomalia_id = A.id
-- union select A.id, 'redacao', 'f' from anomalia as A where A.id not in (select anomalia_id from correcao) and A.id not in (select id from anomalia_traducao) 
-- union select A.id, 'traducao', 'f' from anomalia as A where A.tem_anomalia_redacao = false and A.id not in (select anomalia_id from correcao)
-- union select A.id, 'traducao', 't' from anomalia as A where A.tem_anomalia_redacao = false and A.id in (select anomalia_id from correcao);



--select  U.id_utilizador, T.id_tempo, 'redação', 't' from anomalia as A, correcao as C, incidencia as I natural join d_utilizador as U, d_tempo as T  where A.tem_anomalia_redacao = true and A.id not in (select id from anomalia_traducao) and I.anomalia_id = A.id and C.anomalia_id = A.id and extract(day from A.ts) = T.dia and extract(month from A.ts) = T.mes and extract(year from A.ts) = T.ano
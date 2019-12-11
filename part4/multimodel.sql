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
select email, 'qualificado' as tipo
from utilizador_qualificado
union
select email, 'regular' as tipo
from utilizador_regular;

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
select  extract(day from ts),
        extract(isodow from ts),
        extract(week  from ts),
        extract(month from ts),
        extract(quarter from ts),
        extract(year from ts)
from anomalia
union
select extract(day from data_hora),
       extract(isodow from data_hora),
       extract(week  from data_hora),
       extract(month from data_hora),
       extract(quarter from data_hora),
       extract(year from data_hora)
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

CREATE OR REPLACE FUNCTION get_tipo_anomalia(id integer) RETURNS VARCHAR(255)
as $$
BEGIN
    IF id in (select A.id from anomalia_traducao A) THEN
        RETURN 'traducao';
    ELSE
        RETURN 'redacao';
    END IF;
END
$$ language plpgsql;

CREATE OR REPLACE FUNCTION anomalia_tem_proposta(id integer) RETURNS BOOLEAN
as $$
BEGIN
    IF id in (select anomalia_id from correcao) THEN
        RETURN true;
    ELSE
        RETURN false;
    END IF;
END
$$ language plpgsql;

insert into f_anomalia (id_utilizador, id_tempo, id_local, id_lingua,
                        tipo_anomalia, com_proposta)
select DU.id_utilizador, DT.id_tempo, DLO.id_local, DLI.id_lingua,
       get_tipo_anomalia(A.id), anomalia_tem_proposta(A.id)
from anomalia as A,
     incidencia as I,
     (item natural join local_publico) as L,
     d_utilizador as DU,
     d_tempo as DT,
     d_local as DLO,
     d_lingua as DLI
where A.id = I.anomalia_id
      and I.item_id = L.id
      and I.email = DU.email
      and extract (day from A.ts) = DT.dia
      and extract (isodow from A.ts) = DT.dia_da_semana
      and extract (week from A.ts) = DT.semana
      and extract (month from A.ts) = DT.mes
      and extract (quarter from A.ts) = DT.trimestre
      and extract (year from A.ts) = DT.ano
      and L.latitude = DLO.latitude
      and L.longitude = DLO.longitude
      and A.lingua = DLI.lingua;

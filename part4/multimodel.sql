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
    references d_lingua(id_lingua) on delete cascade
);


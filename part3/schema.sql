drop table local_publico cascade;
drop table item cascade;
drop table anomalia cascade;
drop table anomalia_traducao cascade;
drop table duplicado cascade;
drop table utilizador cascade;
drop table utilizador_qualificado cascade;
drop table utilizador_regular cascade;

create table local_publico (
    latitude      numeric(8, 6) not null,
    longitude     numeric(9, 6) not null,
    nome          varchar(255) not null,
    constraint pk_local_publico primary key(latitude, longitude),
    constraint latitude_check check (-90 <= latitude and latitude <= 90),
    constraint longitude_check check (-180 <= longitude and longitude <= 180)
);

create table item (
    id             integer not null,
    descricao      varchar(255) not null,
    localizacao    varchar(255) not null,    -- not sure about the type
    latitude       numeric(8, 6) not null,
    longitude      numeric(9, 6) not null,
    constraint fk_item_local_publico foreign key(latitude, longitude)
               references local_publico(latitude, longitude),
    constraint pk_item primary key(id)
);

create table anomalia (
    id             integer not null,
    zona           integer[4] not null,     --what do you think
    imagem         bytea not null,
    lingua         varchar(255) not null,
    ts             timestamp not null,
    descricao      varchar(255) not null,
    tem_anomalia_redacao boolean not null,
    constraint pk_anomalia primary key(id)
);

create table anomalia_traducao (
    id             integer not null,
    zona2          integer[4] not null,
    lingua2        varchar(255) not null,
    constraint pk_anomalia_traducao primary key(id),
    constraint fk_anomalia_traducao_anomalia foreign key(id)
                references anomalia(id)
);

-- TODO: how to apply RI-1 and RI-2?? Maybe this way?
-- create assertion overlap_constraint check 
-- (
--     anomalia.lingua != anomalia_traducao.lingua2
--     anomalia.zona[0] != anomalia_traducao[0]
--     anomalia.zona[1] != anomalia_traducao[1]
--     anomalia.zona[2] != anomalia_traducao[2]
--     anomalia.zona[3] != anomalia_traducao[3]
-- );

create table duplicado (
    item1          integer not null,
    item2          integer not null,
    constraint pk_duplicado primary key(item1, item2),
    constraint fk_duplicado_item foreign key(item1) 
                references item(id),
    constraint fk_duplicado_item2 foreign key(item2) 
                references item(id),                    --neds to be like this, because it's different instances of an item.id
    check(item1 < item2)
);

create table utilizador (
    email          varchar(255) not null,
    pass           varchar(255) not null,               -- PASSWORD is a SQL command
    constraint pk_utilizador primary key(email)         
);

--TODO: how to implement RI-4?

create table utilizador_qualificado (
    email           varchar(255) not null,
    constraint pk_utilizador_qualificado primary key(email),
    constraint fk_utilizador_qualificado_utilizador foreign key(email)
                references utilizador(email)
);


--TODO: how to implement RI-5?

create table utilizador_regular (
    email           varchar(255) not null,
    constraint pk_utilizador_regular primary key(email),
    constraint fk_utilizador_regular_utilizador foreign key(email)
                references utilizador(email)
);

--TODO: how to implement RI-6?





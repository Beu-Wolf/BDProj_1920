drop table local_publico cascade;
drop table item cascade;

create table local_publico (
    latitude      numeric(8, 6) not null,
    longitude     numeric(9, 6) not null,
    nome          varchar(255) not null,
    constraint pk_local_publico primary key(latitude, longitude),
    constraint latitude_check check (-90 <= latitude and latitude <= 90),
    constraint longitude_check check (-180 <= longitude and longitude <= 180)
);

create table item (
    id integer not null,
    descricao varchar(255) not null,
    localizacao varchar(255) not null,    -- not sure about the type
    latitude numeric(8, 6) not null,
    longitude numeric(9, 6) not null,
    constraint fk_item_local_publico foreign key(latitude, longitude)
               references local_publico(latitude, longitude),
    constraint pk_item primary key(id)
);

drop table test.test;

drop schema test;

create schema test;

create table test.test (
    id serial not null primary key unique,
    intv int not null,
    floatv float,
    stringv varchar,
    textv varchar,
    boolv bool,
    datev timestamp without time zone default now()
);

create schema provider;
--go

-- 
-- Holds information about cloud providers, eg slicehost, amazon, etc.
--

--drop table provider.provider;
create table provider.provider (
    id int not null primary key unique,
    driver varchar not null unique,
    name varchar not null,
    url varchar,
    manage_url varchar,
    description text
);

-- we only support slicehost at the moment
insert into provider.provider values (1,'slicehost','Slicehost','http://slicehost.com/','https://manage.slicehost.com/',null);
--go

--
-- Account details for a specific provider.  We have a use case for multiple accounts per
-- provider in the case of Amazon
--

-- drop table provider.account;
create table provider.account (
    id serial not null primary key unique,
    provider_id int not null,
    name varchar not null,
    notes text,
    key varchar,
    secret varchar,
    created timestamp without time zone default now()
);
--go


--
-- Lookup table for server types
--

--drop table provider.server_type;
create table provider.server_type (
		id int not null primary key unique,
		server_type varchar not null
);

insert into provider.server_type values (1,'Load Balancer');
insert into provider.server_type values (2,'Database');
insert into provider.server_type values (3,'Web');
insert into provider.server_type values (4,'Cache');

--go


-- 
-- Lookup table for server status
--

--drop table provider.server_status;
create table provider.server_status (
    id int not null primary key unique,
    status varchar not null
);

insert into provider.server_status values (0,'Running');
insert into provider.server_status values (1,'Building');
insert into provider.server_status values (2,'Rebooting');
insert into provider.server_status values (3,'Stopped');

--go


--
-- Table for holding the ssh keys
--

--drop table provider.keys;
create table provider.keys (
    id serial not null primary key unique,
    account_id int not null,
    text_id varchar not null unique,
    name varchar not null,
    notes text,
    public_filename varchar,
    private_filename varchar,
    created timestamp without time zone default now()
);

--go


--
-- Inventory represents slices in an account for a provider
-- Driver specific information is stored in the driver_data field
--
-- Also, root_password is only stored until all post-slice creation
-- code has run.  Only used with providers that don't build with a pre-shared
-- key.  
--

--drop table provider.inventory;
create table provider.inventory (
	id serial not null primary key unique,
	account_id int not null,
    server_type_id int not null,
	name varchar not null,
	notes text,
	root_password varchar,
	status_id int not null,
    progress int default 100,
    server_size varchar,
    base_image varchar,
    backup_name varchar,
    driver_data text,
    created timestamp without time zone default now()
);

--go

--
-- Some providers offer multiple addresses.  Note that some of these will not be ip address but domains.
--

--drop table provider.ipaddress;
create table provider.ipaddress
(
    id serial not null primary key unique,
    inventory_id int not null,
    is_ip boolean default true,
    address varchar not null,
    created timestamp without time zone default now()
);

--go


--
-- Contains information about clouds
--

create schema sky;

--go


--
-- Logical groupings of inventory
--

--drop table sky.cloud;
create table sky.cloud
(
    id serial not null primary key unique,
    name varchar not null,
    uri varchar not null,
    notes text,
    created timestamp without time zone default now()
);

--go

--
-- Inventory belonging to the cloud
--

--drop table sky.cloud_inventory;
create table sky.cloud_inventory
(
    id serial not null primary key unique,
    cloud_id int not null,
    inventory_id int not null
);

--go

--
-- Foreign key constraints
--

alter table provider.account add constraint account_provider_id_fkey foreign key (provider_id) references provider.provider(id) on delete cascade;
alter table provider.inventory add constraint inventory_account_id_fkey foreign key (account_id) references provider.account(id) on delete cascade;
alter table provider.ipaddress add constraint ipaddress_inventory_id_fkey foreign key (inventory_id) references provider.inventory(id) on delete cascade;
alter table provider.keys add constraint keys_account_id_fkey foreign key (account_id) references provider.account(id) on delete cascade;

--go



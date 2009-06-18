alter table provider.account drop constraint account_provider_id_fkey;
alter table provider.inventory drop constraint inventory_account_id_fkey;
alter table provider.ipaddress drop constraint ipaddress_inventory_id_fkey;
alter table provider.keys drop constraint keys_account_id_fkey;

drop table provider.provider;
drop table provider.account;
drop table provider.server_type;
drop table provider.server_status;
drop table provider.keys;
drop table provider.inventory;
drop table provider.ipaddress;
drop schema provider;

drop table sky.cloud;
drop table sky.cloud_inventory;
drop schema sky;

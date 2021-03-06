create table item (item_id serial primary key not null, item_internal_name text not null unique, item_display_name text not null unique, item_cost numeric not null);
insert into item values (default, 'firescroll', 'Fire Scroll', 175), (default, 'icescroll', 'Ice Scroll', 125), (default, 'speedscroll', 'Speed Scroll', 225), (default, 'stealthscroll', 'Stealth Scroll', 150), (default, 'shuriken', 'Shuriken', 50), (default, 'dimmak', 'Dim Mak', 1000), (default, 'ginsengroot', 'Ginseng Root', 1000), (default, 'tigersalve', 'Tiger Salve', 3000);
delete from inventory where item not in (select item_display_name from item);
alter table inventory add foreign key (item) references item(item_display_name) on update cascade on delete cascade;

grant all on item to developers;
alter table item owner to developers;

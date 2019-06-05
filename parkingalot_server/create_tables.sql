drop table if exists parking_space;
drop table if exists device;


create table device(
	name 						varchar(255),
	update_time					timestamp,
	free_condition 				ENUM('and', 'or', 'sensor', 'image'),
	downlink_update_interval	smallint,
	n_spaces					smallint,
	primary key(name)
);

create table parking_space(
	id 							smallint,
	name						varchar(255),
	full						boolean,
	utilization					integer,
	longitude 					varchar(11),
	latitude					varchar(11),
	primary key(id,name),
	foreign key (name) references device(name)
);
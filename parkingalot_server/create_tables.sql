drop table if exists parking_space;
drop table if exists device;


create table device(
	name 						varchar(255),
	total_update_time			timestamp,
	update_time					timestamp,
	free_condition 				ENUM('and', 'or', 'sensor', 'image'),
	total_update_interval 		smallint,
	downlink_update_interval	smallint,
	primary key(name)
);

create table parking_space(
	id 							smallint,
	name						varchar(255),
	full						boolean,
	utilization					integer,
	longitude 					varchar(10),
	latitude					varchar(10),
	primary key(id,name),
	foreign key (name) references device(name)
);
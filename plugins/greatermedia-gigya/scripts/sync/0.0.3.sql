Create Table member_query_users(
	site_id           int            not null,
	member_query_id   int            not null,
	store_type        varchar(16)    not null,
	user_id           varchar(252)   not null
) Engine=InnoDB;

create database outcons encoding = 'UTF8';

create table users
(
    id serial primary key,
    first_name text not null,
    last_name text not null,
	email text not null
);

create table projects
(
    id serial primary key,
    name text not null
);

create table time_logs
(
    id serial primary key,
    users_id int not null references users,
    projects_id int not null references projects,
	hours real not null,
    date timestamp DEFAULT now() not null
);

alter table time_logs add constraint time_logs_max_hours CHECK (hours <= 8.0);

create or replace function init() returns void as $$
	declare
		users_first_name_arr text[] := array['John', 'Gringo', 'Mark', 'Lisa', 'Maria', 'Sonya', 'Philip', 'Jose', 'Lorenzo', 'George', 'Justin'];
		users_last_name_arr text[] := array['Johnson', 'Lamas', 'Jackson', 'Brown', 'Mason', 'Rodriguez', 'Roberts', 'Thomas', 'Rose', 'McDonalds'];	
		domain_arr text[] := array['hotmail.com', 'gmail.com', 'live.com'];
		projects_name_arr text[] := array['My own', 'Outcons',  'Free Time'];	

		users_first_name text;
		users_last_name text;
		domain text;
		users_email text;

		projects_count int;
		insert_users_id int;
		projects_id int;
		exists_hours real;
		new_hours real;
		max_hours_per_day real := 8;
    begin
		delete from time_logs;
    	delete from users;
		delete from projects;

        for i in array_lower(projects_name_arr, 1)..array_upper(projects_name_arr, 1) loop
            insert into projects (name) values (projects_name_arr[i]);
        end loop;
		select count(*) from projects into projects_count;

		for i in 1..100 loop 
			users_first_name = users_first_name_arr[trunc(random() * array_length(users_first_name_arr, 1)) + 1];
        	users_last_name = users_last_name_arr[trunc(random() * array_length(users_last_name_arr, 1)) + 1];
    	    domain = domain_arr[trunc(random() * array_length(domain_arr, 1)) + 1];
	        users_email = lower(users_first_name || '.' || users_last_name || '@' || domain);

			insert into users (first_name, last_name, email) values (users_first_name, users_last_name, users_email) returning id into insert_users_id;
			for j in 1..(trunc(random() * 20) + 1) loop 
				select sum(hours) from time_logs where users_id = insert_users_id and date::date = current_date group by users_id into exists_hours; 
				if exists_hours is null then
					exists_hours := 0;
				end if;
				select round((random()*8 )::numeric, 2)::real into new_hours;
				if (exists_hours + new_hours <= max_hours_per_day) then
					select id from projects offset trunc(random() * projects_count) limit 1 into projects_id;
					insert into time_logs (users_id, projects_id, hours) values (insert_users_id, projects_id, new_hours); 
				end if;
			end loop;
		end loop;

    end;
$$ language plpgsql;

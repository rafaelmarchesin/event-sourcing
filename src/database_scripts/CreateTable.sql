CREATE TABLE todo_list_table (
id INT(8) not null AUTO_INCREMENT PRIMARY KEY,
task_id INT (8) NOT NULL,
version INT (1) default 1,
task VARCHAR(140) NOT NULL,
done INT(1) default 0,
event_type VARCHAR(140),
date_time DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE todo_list_projections (
id INT(8) not null AUTO_INCREMENT PRIMARY KEY,
task_id INT (8) NOT NULL,
version INT (1) default 1,
task VARCHAR(140),
done INT(1) default 0,
event_type VARCHAR(140),
date_time DATETIME DEFAULT CURRENT_TIMESTAMP
);
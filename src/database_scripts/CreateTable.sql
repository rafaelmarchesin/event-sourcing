CREATE TABLE todo_list_table (
id INT(8) not null AUTO_INCREMENT PRIMARY KEY,
task VARCHAR(140) NOT NULL,
done INT(1) default 0
);
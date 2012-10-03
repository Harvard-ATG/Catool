CREATE DATABASE  `catool` ;
CREATE USER 'catool'@'localhost' IDENTIFIED BY  'catool';
GRANT USAGE ON * . * TO  'catool'@'localhost' IDENTIFIED BY  'catool';
GRANT ALL PRIVILEGES ON  `catool` . * TO  'catool'@'localhost';

CREATE DATABASE  `catool_test` ;
CREATE USER 'catool_test'@'localhost' IDENTIFIED BY  'catool_test';
GRANT USAGE ON * . * TO  'catool_test'@'localhost' IDENTIFIED BY  'catool_test';
GRANT ALL PRIVILEGES ON  `catool_test` . * TO  'catool_test'@'localhost';

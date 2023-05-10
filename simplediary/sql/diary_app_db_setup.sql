/*
 * Name:    Brandon Mitchell
 * Description: Creates the tables for the diaryappdb if they don't exist and 
 *              sets up the user to connect to the database.
 */



-- Database already exists, select it so the table is made in the right place
USE diaryappdb;

CREATE TABLE IF NOT EXISTS tbl_users
(
    username varchar(30) NOT NULL, 
    password varchar(255) NOT NULL,
    first_name varchar(40) NOT NULL,  
    middle_initial char NULL,
    last_name varchar(40) NOT NULL,
    
    -- If a value is nullable, it will default to null when no value is given
    last_successful_logon datetime NULL,
    last_unsuccessful_logon datetime NULL,
    num_logons int DEFAULT 0,
    PRIMARY KEY(username)
) ENGINE = InnoDB;

-- Use to track number of logons, attempts
CREATE TABLE IF NOT EXISTS tbl_logon_attempts
(
    username varchar(30) NOT NULL, 
    attempt_datetime datetime NOT NULL,
    status varchar(10) NOT NULL,
    ipaddress varchar(50) NOT NULL,
    
    -- Unsure of max size, seems like it can be very long
    user_agent varchar(8000) NOT NULL,
    FOREIGN KEY(username) REFERENCES tbl_users(username)
) ENGINE = InnoDB;

-- Store user's entries and link them to a specific username
CREATE TABLE IF NOT EXISTS tbl_diary_entries
(
    username varchar(30) NOT NULL,
    entry_datetime datetime NOT NULL,
    entry varchar(255) NOT NULL,
    FOREIGN KEY(username) REFERENCES tbl_users(username)
) ENGINE = InnoDB;

REVOKE ALL PRIVILEGES, GRANT OPTION FROM 'diaryappdbuser'@'localhost';

-- Limit user's privileges to what is strictly necessary
GRANT SELECT, INSERT ON diaryappdb.* TO 'diaryappdbuser'@'localhost';
GRANT UPDATE ON diaryappdb.tbl_users TO 'diaryappdbuser'@'localhost';
GRANT DELETE ON diaryappdb.tbl_diary_entries TO 'diaryappdbuser'@'localhost';
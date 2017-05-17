ALTER TABLE taxis
    ADD plate_country VARCHAR(3) NOT NULL DEFAULT 'CH' AFTER password_hash,
    ADD plate_state VARCHAR(2) NOT NULL DEFAULT '' AFTER plate_country,
    MODIFY max_passengers INT NOT NULL DEFAULT 4,
    MODIFY max_luggages INT NOT NULL DEFAULT 2,
    MODIFY animal BOOLEAN NOT NULL DEFAULT 0,
    MODIFY baby BOOLEAN NOT NULL DEFAULT 0,
    MODIFY skis BOOLEAN NOT NULL DEFAULT 0,
    MODIFY disabled BOOLEAN NOT NULL DEFAULT 0,
    ADD active BOOLEAN NOT NULL DEFAULT 1,
    ADD comment TEXT,
    ADD CONSTRAINT UNIQUE(plate_country,plate_state,plate_number);
UPDATE taxis SET plate_state = LEFT(plate_number,2);
UPDATE taxis SET plate_number = TRIM(SUBSTRING(plate_number,3));

ALTER TABLE requests
    ADD country VARCHAR(3) NOT NULL DEFAULT 'CH' AFTER address,
    ADD state VARCHAR(2) NOT NULL DEFAULT 'GE' AFTER country;

CREATE TABLE callcenters (
    country VARCHAR(3) NOT NULL,
    state VARCHAR(2) NOT NULL,
    title VARCHAR(255) NOT NULL,
    phone VARCHAR(32) NOT NULL,
    PRIMARY KEY (country, state)
) ENGINE=InnoDB;

INSERT INTO callcenters(country,state,title,phone)
--    VALUES('CH','GE','Taxi-Phone SA','+41223314133');
    VALUES('CH','GE','Coopérative Taxis 202','+41223202202');

ALTER TABLE requests_taxis
        DROP acceptance_time,
        DROP latitude,
        DROP longitude;

ALTER TABLE taxis
        ADD update_time DATETIME,
        ADD latitude DOUBLE,
        ADD longitude DOUBLE;

ALTER TABLE requests
        ADD locality VARCHAR(127);

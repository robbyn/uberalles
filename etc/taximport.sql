INSERT INTO taxis(username, password_hash, plate_number, first_name, last_name,
        address, email, phone, zip, city, country)
SELECT REPLACE(plate_number,' ',''), MD5(REPLACE(plate_number,' ','')),
        plate_number, first_name, last_name, address, email, phone, zip, city,
        country
FROM `TABLE 4`
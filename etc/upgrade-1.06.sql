CREATE VIEW monthly_total AS
SELECT taxi_id,YEAR(logtime) AS year,MONTH(logtime) AS month,event_type,
    count(0) AS total
FROM eventlog GROUP BY 1,2,3,4;

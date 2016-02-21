SELECT 
    sites.id,
    CONCAT(sites.address, ' ', sites.address_ext) AS address,
    sites.city,
    sites.state,
    sites.zipcode,
    IF(sites.address_type = 0, 'Residential', 'Commercial'),
    sites.deleted,
    users.username,
    sites.updated
FROM sites 
    INNER JOIN users ON users.id = sites.created_by
INTO OUTFILE '/srv/jitterbit/job_site_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
;

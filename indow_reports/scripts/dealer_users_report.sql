SELECT 
    users.username, 
    users.first_name, 
    users.last_name, 
    groups.name AS group_name, 
    CONCAT(user_addresses.address, user_addresses.address_ext) as address, 
    user_addresses.city, 
    user_addresses.state, 
    user_addresses.zipcode, 
    users.phone_1, 
    users.email_1, 
    users.deleted,
    groups.id AS dealer_account_id
FROM users 
LEFT JOIN users_groups ON users_groups.user_id = users.id
LEFT JOIN groups ON users_groups.group_id = groups.id 
LEFT JOIN user_addresses ON users.id = user_addresses.id 
WHERE 
is_customer = 0
AND
users.company_id IS NOT NULL
AND
users.username IS NOT NULL
AND 
users.username != ''
INTO OUTFILE '/srv/jitterbit/dealer_users_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
;

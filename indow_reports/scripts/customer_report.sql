SELECT
    customer_user.id, 
    customer_user.first_name, 
    customer_user.last_name,
    groups.name AS group_name,
    CONCAT(user_addresses.address, ' ', user_addresses.address_ext) AS address,
    user_addresses.city,
    user_addresses.state,
    user_addresses.zipcode,
    user_addresses.country,
    customer_user.phone_1,
    customer_user.email_1,
    customers.deleted
FROM customers
LEFT JOIN users AS customer_user ON customers.user_id = customer_user.id
INNER JOIN user_addresses ON user_addresses.user_id = customer_user.id
LEFT JOIN users AS contact_user ON customers.customer_preferred_contact = contact_user.id
LEFT JOIN users_groups ON users_groups.user_id = contact_user.id 
LEFT JOIN groups ON users_groups.group_id = groups.id 
GROUP BY customer_user.id
INTO OUTFILE '/srv/jitterbit/customer_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
;


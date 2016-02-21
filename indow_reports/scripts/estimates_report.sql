SELECT 
    users.username,
    estimates.id,
    customer.id AS customer_id,
    estimates.created,
    IF(estimates.closed = 1, 'closed', 'open') AS estimate_status,
    (
        SELECT       count(item_id)
        FROM         estimates_has_item
        INNER JOIN   items ON estimates_has_item.item_id = items.id
        INNER JOIN   product_types ON product_types.id = items.product_types_id
        INNER JOIN   products ON products.id = product_types.product_id
        WHERE        estimates_has_item.deleted=0
        AND          estimates_has_item.estimate_id = estimates.id
        AND          product_types.product_id != 3

    ) as num_win,
    groups.name AS group_name,
    estimates.total_square_feet,
    estimates.estimate_total,
    estimates.deleted,
    estimates.site_id,
    orders.id AS converted,
    quoter.username  AS quote_created_by,
    orderer.username AS order_created_by,
    techer.username  AS tech_id
FROM estimates 
    INNER JOIN users ON estimates.created_by_id = users.id 
    INNER JOIN groups ON groups.id = estimates.dealer_id
    LEFT JOIN  estimates_has_customers ON estimates_has_customers.estimate_id=estimates.id AND estimates_has_customers.primary=1
    LEFT JOIN  users customer ON customer.id = estimates_has_customers.customer_id
    LEFT JOIN  orders ON orders.estimate_id = estimates.id
    LEFT JOIN  quotes ON quotes.estimate_id = estimates.id
    LEFT JOIN  users quoter  ON quoter.id = quotes.created_by
    LEFT JOIN  users orderer ON orderer.id = orders.created_by
    LEFT JOIN  users techer  ON techer.id = estimates.tech_id
GROUP BY estimates.id
INTO OUTFILE '/srv/jitterbit/estimates_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
;


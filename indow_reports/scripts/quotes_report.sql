SELECT 
    quotes.id,
    quotes.site_id,
    orders.id AS order_id,
    customer.id AS customer_id,
    users.username,
    -- quotes.quote_total, --
    ( 
        SELECT SUM(price)
        FROM
        (
            SELECT items.id, items.site_id, items.price, quotes_has_item.quote_id
            FROM items
            INNER JOIN quotes_has_item ON quotes_has_item.item_id = items.id
            AND items.deleted = 0

            UNION ALL
            (
                SELECT items.id, items.site_id, IFNULL(subitems.price_override,IF(min_price>unit_price, min_price, unit_price)) AS price, quotes_has_item.quote_id
                FROM items
                INNER JOIN subitems ON items.id = subitems.item_id AND items.id IN (SELECT item_id FROM subitems)
                INNER JOIN product_types ON subitems.product_type_id = product_types.id
                INNER JOIN quotes_has_item ON quotes_has_item.item_id = items.id
                JOIN generator_256 i ON i.n BETWEEN 1 AND subitems.quantity
                WHERE items.deleted = 0
                AND subitems.deleted = 0
                ORDER BY items.id, i.n
            )
        ) AS all_items
        where quote_id = quotes.id
    ) AS quote_total,
    quotes.created,
    quotes.deleted,
    techer.username  AS tech_id,
    quoter.username  AS quote_created_by,
    orderer.username AS order_created_by
FROM quotes
    LEFT JOIN estimates ON estimates.quote_id = quotes.id
    INNER JOIN sites ON quotes.site_id = sites.id
    LEFT JOIN sites_techs ON sites.id = sites_techs.site_id
    INNER JOIN users ON users.id = sites.created_by
    LEFT JOIN  orders ON orders.quote_id = quotes.id
    LEFT JOIN  quotes_has_customers ON quotes_has_customers.quote_id=quotes.id AND quotes_has_customers.primary=1
    LEFT JOIN  users customer ON customer.id = quotes_has_customers.customer_id
    LEFT JOIN  users quoter   ON quoter.id = quotes.created_by
    LEFT JOIN  users orderer  ON orderer.id = orders.created_by
    LEFT JOIN  users techer   ON techer.id = sites_techs.tech_id
INTO OUTFILE '/srv/jitterbit/quotes_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
;


SELECT 
    customer.id AS customer_id,
    orders.id,
    status_codes.description,
    orders.quote_id,
    orders.site_id,
    orders.estimate_id,
    users.username,
    groups.name,
    (
        SELECT       count(item_id)
        FROM         orders_has_item 
        INNER JOIN   items ON orders_has_item.item_id = items.id
        INNER JOIN   product_types ON product_types.id = items.product_types_id
        INNER JOIN   products ON products.id = product_types.product_id
        WHERE        orders_has_item.deleted=0
        AND          orders_has_item.order_id = orders.id
        AND          product_types.product_id != 3

    ) as num_win,
    estimates.total_square_feet,
    orders.subtotal,
    orders.po_num,
    DATE_FORMAT(order_date_change.status_changed, '%m/%d/%y') as order_date,
    orders.order_confirmation_sent,
    DATE_FORMAT(oc_recieved_change.status_changed, '%m/%d/%y') as oc_recieved_date,
    DATE_FORMAT(approved_change.status_changed, '%m/%d/%y') as oc_approved_date,
    DATE_FORMAT(ship_change.status_changed, '%m/%d/%y') as ship_date,
    orders.commit_date,
    shipments.carrier,
    orders.tracking_num,
    IFNULL(orders.expedite, 0),
    TRIM(BOTH '\n' FROM o_notes.text) AS order_note_text,
    TRIM(BOTH '\n' FROM i_notes.text) AS internal_note_text,
    IF(orders.remake  = 0, 'false', 'true'),
    IF(orders.deleted = 0, 'false', 'true'),
    DATE_FORMAT(orders.updated, '%m/%d/%y') as last_modified,
    quoter.username AS quote_created_by,
    estimator.username AS estimate_created_by,
    users.id
FROM orders
    LEFT JOIN status_codes ON orders.status_code = status_codes.id
    LEFT JOIN groups ON groups.id = orders.dealer_id
    LEFT JOIN estimates ON orders.estimate_id = estimates.id
    LEFT JOIN users ON users.id = orders.created_by
    LEFT JOIN  orders_has_customers ON orders_has_customers.order_id=orders.id AND orders_has_customers.primary=1
    LEFT JOIN  users customer ON customer.id = orders_has_customers.customer_id
    LEFT JOIN shipments ON orders.shipment_id = shipments.id
    LEFT JOIN orders_notes ON orders_notes.order_id = orders.id
    LEFT JOIN notes AS o_notes ON orders_notes.note_id = o_notes.id
    LEFT JOIN order_internal_notes ON order_internal_notes.order_id = orders.id
    LEFT JOIN notes AS i_notes ON order_internal_notes.note_id = i_notes.id
    LEFT JOIN orders_status_codes AS order_date_change ON order_date_change.order_id = orders.id AND order_date_change.order_status_code_id=(SELECT id FROM status_codes WHERE code=300)
    LEFT JOIN orders_status_codes AS oc_recieved_change ON oc_recieved_change.order_id = orders.id AND oc_recieved_change.order_status_code_id=(SELECT id FROM status_codes WHERE code=300)
    LEFT JOIN orders_status_codes AS approved_change ON approved_change.order_id = orders.id AND approved_change.order_status_code_id=(SELECT id FROM status_codes WHERE code=350)
    LEFT JOIN orders_status_codes AS ship_change ON ship_change.order_id = orders.id AND ship_change.order_status_code_id=(SELECT id FROM status_codes WHERE code=700)
    LEFT JOIN users quoter    ON quoter.id    = orders.quote_created_by
    LEFT JOIN users estimator ON estimator.id = orders.estimate_created_by
GROUP BY orders.id
INTO OUTFILE '/srv/jitterbit/orders_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
;

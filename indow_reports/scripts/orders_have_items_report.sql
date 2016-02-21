SELECT 
    order_id, 
    item_id
FROM orders_has_item 
INTO OUTFILE '/srv/jitterbit/orders_have_items_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
;

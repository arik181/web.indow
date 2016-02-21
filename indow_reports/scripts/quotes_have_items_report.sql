SELECT 
    id, 
    quote_id, 
    item_id
FROM quotes_has_item 
INTO OUTFILE '/srv/jitterbit/quotes_have_items_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
;


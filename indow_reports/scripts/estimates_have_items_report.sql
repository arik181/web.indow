SELECT 
    id, 
    estimate_id, 
    item_id
FROM estimates_has_item 
INTO OUTFILE '/srv/jitterbit/estimates_have_items_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n';
;

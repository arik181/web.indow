SELECT
    items.id,
    items.site_id,
    items.floor,
    items.room,
    items.location,
    items.width,
    items.height,
    edging.name,
    REPLACE(items.acrylic_panel_thickness, '"', ''),
    IF(items.special_geom = 0, 'false', 'true'),
    product_types.product_type,
    products.product,
    users.username,
    orders.updated
FROM items 
    INNER JOIN edging ON items.edging_id = edging.id 
    INNER JOIN product_types ON items.product_types_id = product_types.id 
    LEFT JOIN products ON items.window_shape_id = products.id
    INNER JOIN sites ON items.site_id = sites.id
    INNER JOIN users ON users.id = sites.created_by
    INNER JOIN orders ON items.order_id = orders.id
INTO OUTFILE '/srv/jitterbit/items_report_tmp1.csv'
FIELDS TERMINATED BY '|'
ESCAPED  BY ''
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
;

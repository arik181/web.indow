INSERT INTO `edging` (`id`, `name`) VALUES (1, 'White'), (2, 'Brown'), (3, 'Black');
INSERT INTO `estimate_status_codes` (`id`, `code`, `text`) VALUES (1, 1, 'text'), (2, 1, 'text');
INSERT INTO `group_types` (`id`, `name`) VALUES (1, 'Admin'), (2, 'Supplier'), (3, 'Dealer'), (4, 'Contractor'), (5, 'Customer'), (6, 'Company'), (7, 'Division');
INSERT INTO `manufacturing_status` (`id`, `name`) VALUES (1, 'Include'), (2, 'Hold'), (3, 'Back Order'), (4, 'Re-Order');
INSERT INTO `measurements` (`id`, `valid`, `measurement_key`, `measurement_value`, `data_set`) VALUES (1, 1, 'A', 1, NULL), (2, 1, 'B', 2, NULL), (3, 1, 'C', 3, NULL), (4, 1, 'D', 4, NULL), (5, 1, 'E', 5, NULL), (6, 1, 'F', 6, NULL), (7, 1, 'A', 1, NULL), (8, 1, 'B', 2, NULL), (9, 1, 'C', 3, NULL), (10, 1, 'D', 4, NULL), (11, 1, 'E', 5, NULL), (12, 1, 'F', 6, NULL), (13, 1, 'A', 1, NULL), (14, 1, 'B', 2, NULL), (15, 1, 'C', 3, NULL), (16, 1, 'D', 4, NULL), (17, 1, 'E', 5, NULL), (18, 1, 'F', 6, NULL);
INSERT INTO `payment_types` (`id`, `name`) VALUES (1, 'Check (down)'),(2, 'Check (final)'),(3, 'Cash (down)'),(4, 'Cash (final)'),(5, 'Credit (down)'),(6, 'Credit (final)'),(7, 'ACH (down)'),(8, 'ACH (final)'),(9, 'Wire (down)'),(10, 'Wire (final)');
INSERT INTO `products` (`id`, `product`) VALUES (1, 'Insert'), (2, 'Skylight Insert'), (3, 'Accessories');
INSERT INTO `product_types` (`id`, `product_type`, `abbrev`, `size`, `description`, `custom_add_on`, `opening_specific`, `not_opening_specific`, `unit_price`, `unit_price_type`, `min_price`, `max_width`, `max_height`, `product_id`) VALUES (1, '', 'STD', 0, 'Standard', 0, 0, 0, 20, 'sq', 80, 61.5, 121.5, 1), (2, '', 'MG', 0, 'Museum', 0, 0, 0, 30, 'sq', 80, 73.5, 97.5, 1), (3, '', 'CG', 0, 'Commercial', 0, 0, 0, 34, 'sq', 80, 73.5, 97.5, 1), (4, '', 'PG', 0, 'Privacy', 0, 0, 0, 30, 'sq', 80, 49.5, 97.5, 1), (5, '', 'BG', 0, 'Blackout', 0, 0, 0, 20, 'sq', 80, 49.5, 73.5, 1), (6, '', 'A-STD', 0, 'Acoustic', 0, 0, 0, 30, 'sq', 80, 73.5, 97.5, 1), (7, '', 'A-BG', 0, 'Acoustic Blackout', 0, 0, 0, 30, 'sq', 80, 49.5, 97.5, 1), (8, '', 'A-CG', 0, 'Acoustic Commercial', 0, 0, 0, 38, 'sq', 80, 49.5, 97.5, 1), (9, '', 'SG', 0, 'Shade', 0, 0, 0, 30, 'sq', 80, 49.5, 97.5, 1), (19, '', '', 0, 'Storage Sleeves', 0, 0, 0, 1, 'sq', 10, 0, 0, 3), (20, '', '', 0, 'Grip Strips 8" (Qty 6)', 0, 0, 0, 38, 'unit', 0, 0, 0, 3), (21, '', '', 0, 'Frame Swivel Clips (Qty 100)', 0, 0, 0, 22, 'unit', 0, 0, 0, 3), (22, '', '', 0, 'Retaining Pins (Qty 250)', 0, 0, 0, 19, 'unit', 0, 0, 0, 3), (23, '', '', 0, 'L Brackets (Qty 12)', 0, 0, 0, 21, 'unit', 0, 0, 0, 3), (24, '', '', 48, 'Mullion Kit', 0, 0, 0, 90, 'unit', 0, 0, 0, 3), (25, '', '', 72, 'Mullion Kit', 0, 0, 0, 120, 'unit', 0, 0, 0, 3), (26, '', '', 96, 'Mullion Kit', 0, 0, 0, 150, 'unit', 0, 0, 0, 3), (27, '', '', 0, 'Mullion Kit', 0, 0, 0, 0, 'unit', 0, 0, 0, 3), (28, '', '', 48, 'H Bar', 0, 0, 0, 90, 'unit', 0, 0, 0, 3), (29, '', '', 72, 'H Bar', 0, 0, 0, 120, 'unit', 0, 0, 0, 3), (30, '', '', 96, 'H Bar', 0, 0, 0, 150, 'unit', 0, 0, 0, 3), (31, '', '', 0, 'H Bar', 0, 0, 0, 0, 'unit', 0, 0, 0, 3), (32, '', '', 48, 'T Bar', 0, 0, 0, 90, 'unit', 0, 0, 0, 3), (33, '', '', 72, 'T Bar', 0, 0, 0, 120, 'unit', 0, 0, 0, 3), (34, '', '', 96, 'T Bar', 0, 0, 0, 150, 'unit', 0, 0, 0, 3), (35, '', '', 0, 'T Bar', 0, 0, 0, 0, 'unit', 0, 0, 0, 3), (36, '', '', 48, 'F Bar', 0, 0, 0, 90, 'unit', 0, 0, 0, 3), (37, '', '', 72, 'F Bar', 0, 0, 0, 120, 'unit', 0, 0, 0, 3), (38, '', '', 96, 'F Bar', 0, 0, 0, 150, 'unit', 0, 0, 0, 3), (39, '', '', 0, 'F Bar', 0, 0, 0, 0, 'unit', 0, 0, 0, 3);
INSERT INTO `product_types` (`product_type`, `abbrev`, `size`, `description`, `custom_add_on`, `opening_specific`, `not_opening_specific`, `unit_price`, `unit_price_type`, `min_price`, `max_width`, `max_height`, `product_id`) VALUES ('', 'STD', 0, 'Standard', 0, 0, 0, 20, 'sq', 80, 31.5, 49.5, 2),('', 'MG', 0, 'Museum', 0, 0, 0, 30, 'sq', 80, 31.5, 49.5, 2),('', 'CG', 0, 'Commercial', 0, 0, 0, 34, 'sq', 80, 31.5, 49.5, 2),('', 'PG', 0, 'Privacy', 0, 0, 0, 30, 'sq', 80, 31.5, 49.5, 2),('', 'BG', 0, 'Blackout', 0, 0, 0, 20, 'sq', 80, 31.5, 49.5, 2),('', 'A-STD', 0, 'Acoustic', 0, 0, 0, 30, 'sq', 80, 31.5, 49.5, 2),('', 'A-BG', 0, 'Acoustic Black?out', 0, 0, 0, 30, 'sq', 80, 31.5, 49.5, 2),('', 'A-CG', 0, 'Acoustic Commercial', 0, 0, 0, 38, 'sq', 80, 31.5, 49.5, 2),('', 'SG', 0, 'Shade', 0, 0, 0, 30, 'sq', 80, 31.5, 49.5, 2);
UPDATE product_types SET product_type=description;
UPDATE product_types SET description='';
INSERT INTO `promotion_info` (`id`, `promotion_start_date`, `promotion_end_date`, `event_stamp`, `rebate_type`, `percent_for_estimate`, `percent_for_invoice`) VALUES (1, '2014-08-13 00:03:23', '2014-08-13 00:03:23', '2014-08-13 00:03:23', 'type1', 1, 1), (2, '2014-08-13 00:03:23', '2014-08-13 00:03:23', '2014-08-13 00:03:23', 'type2', 1, 1), (3, '2014-08-13 00:03:23', '2014-08-13 00:03:23', '2014-08-13 00:03:23', 'type2', 1, 1), (4, '2014-08-13 00:03:23', '2014-08-13 00:03:23', '2014-08-13 00:03:23', 'type3', 1, 1), (5, '2014-08-13 00:03:23', '2014-08-13 00:03:23', '2014-08-13 00:03:23', 'type3', 1, 1), (6, '2014-08-13 00:03:23', '2014-08-13 00:03:23', '2014-08-13 00:03:23', 'type4', 1, 1);
INSERT INTO `sales_modifiers` (`modifier_type`, `code`, `description`, `amount`, `modifier`, `start_date`, `end_date`, `deleted`) VALUES ('fee', 'None', '$10 Packaging Fee', 10, 'dollar', NULL, NULL, 0), ('discount', 'TH34Z2', '20% off', 20, 'percent', '2014-08-01 00:00:00', '2014-08-27 00:00:00', 0), ('discount', 'MN98ES', '10% Holiday Sale', 10, 'percent', '2014-08-11 00:00:00', '2014-08-27 00:00:00', 0), ('fee', '10s43mn', '10% Shipping', 10, 'percent', NULL, NULL, 0);
INSERT INTO `status_codes` (`id`, `code`, `description`) VALUES(1, 100, 'Pre-Order'),(2, 200, 'Measurement'),(3, 280, 'Hold - Measurement'),(4, 300, 'Order Submitted'),(5, 320, 'Pending Confirmation'),(6, 330, 'Final Review'),(7, 350, 'Order Approved'),(8, 380, 'Hold - Order Approved'),(9, 400, 'Order Processing'),(10, 480, 'Hold - Order Processing'),(11, 500, 'Manufacturing'),(12, 580, 'Hold - Manufacturing'),(13, 600, 'Packaging'),(14, 650, 'Complete'),(15, 680, 'Hold - Shipping'),(16, 700, 'Shipped');
INSERT INTO `windows` (`id`, `treatment`, `gasket_color`, `shade_grade`, `pane_thickness`, `linear_feet`, `gasket_size`, `top_spine`, `side_spines`, `safety_hardware`, `frame_depth_top`, `frame_depth_left`, `frame_depth_right`, `frame_depth_bottom`, `air_filter`, `frame_step`, `plus_four`, `cut_dimensions`, `coordinates`, `MAPP_width`, `MAPP_width_bottom`, `MAPP_width_top`, `MAPP_height`, `MAPP_height_left`, `MAPP_height_right`, `MAPP_diagonal_left`, `MAPP_diagonal_right`, `cut_script_url`) VALUES (1, 'treatment', 'color', 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'dimensions', 'coordinates', 1, 1, 1, 1, 1, 1, 1, 1, 'url'), (2, 'treatment', 'color', 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'dimensions', 'coordinates', 1, 1, 1, 1, 1, 1, 1, 1, 'url'), (3, 'treatment', 'color', 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'dimensions', 'coordinates', 1, 1, 1, 1, 1, 1, 1, 1, 'url'), (4, 'treatment', 'color', 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'dimensions', 'coordinates', 1, 1, 1, 1, 1, 1, 1, 1, 'url'), (5, 'treatment', 'color', 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'dimensions', 'coordinates', 1, 1, 1, 1, 1, 1, 1, 1, 'url'), (6, 'treatment', 'color', 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 'dimensions', 'coordinates', 1, 1, 1, 1, 1, 1, 1, 1, 'url');
INSERT INTO `window_shapes` (`id`, `name`) VALUES (1, 'Rectangle'), (2, 'Trapezoid'), (3, 'Custom');
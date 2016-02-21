#!/bin/sh

rm -rf /srv/jitterbit/*tmp*.csv
rm -rf /srv/jitterbit/*report*.csv

echo "Customer Track,First Name,Last Name,Group,Street,City,State/Province,Zip/Postal Code,Country,Phone,Email,Deleted" > /srv/jitterbit/customer_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/customer_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/customer_report_tmp1.csv
cat /srv/jitterbit/customer_report_tmp0.csv /srv/jitterbit/customer_report_tmp1.csv > /srv/jitterbit/customer_report.csv

echo "username,First Name,Last Name,Group,Street,City,State/Province,Zip/Postal Code,Phone,Email,Deleted,Dealer Account ID" > /srv/jitterbit/dealer_users_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/dealer_users_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/dealer_users_report_tmp1.csv
cat /srv/jitterbit/dealer_users_report_tmp0.csv /srv/jitterbit/dealer_users_report_tmp1.csv > /srv/jitterbit/dealer_users_report.csv

echo "Estimate has Item #,Estimate #,Item #" > /srv/jitterbit/estimates_have_items_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/estimates_have_items_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/estimates_have_items_report_tmp1.csv
cat /srv/jitterbit/estimates_have_items_report_tmp0.csv /srv/jitterbit/estimates_have_items_report_tmp1.csv > /srv/jitterbit/estimates_have_items_report.csv

echo "Username,Estimate #,Customer Track,Created,Estimate Status,No. Windows,Group,Total Sq Ft,Subtotal,Deleted,Job Site #,Converted To Order,quote created by ID,order created by ID,tech ID" > /srv/jitterbit/estimates_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/estimates_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/estimates_report_tmp1.csv
cat /srv/jitterbit/estimates_report_tmp0.csv /srv/jitterbit/estimates_report_tmp1.csv > /srv/jitterbit/estimates_report.csv

echo "Item #,Job Site #,floor,room,location,Width,Height,Color,Thickness,special geometry,Product Type,Product,username,Last Modified" > /srv/jitterbit/items_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/items_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/items_report_tmp1.csv
cat /srv/jitterbit/items_report_tmp0.csv /srv/jitterbit/items_report_tmp1.csv > /srv/jitterbit/items_report.csv

echo "Job Site #,Street,City,State/Province,Zip/Postal Code,Type,Deleted,username,Last Modified Date" > /srv/jitterbit/job_site_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/job_site_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/job_site_report_tmp1.csv
cat /srv/jitterbit/job_site_report_tmp0.csv /srv/jitterbit/job_site_report_tmp1.csv > /srv/jitterbit/job_site_report.csv

echo "Order #,Item #" > /srv/jitterbit/orders_have_items_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/orders_have_items_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/orders_have_items_report_tmp1.csv
cat /srv/jitterbit/orders_have_items_report_tmp0.csv /srv/jitterbit/orders_have_items_report_tmp1.csv > /srv/jitterbit/orders_have_items_report.csv

echo "Customer Track,Order #,Order Status,Quote #,Job Site #,Estimate #,username,Group,No. Windows,Total Sq. Ft.,Order Subtotal,PO#,Order Date,OC Sent Date,OC Received Date,Order Approved Date,Est. Ship Date,Carrier,Tracking Num.,Expedite,Order Notes,Internal Notes,Remake,Deleted,Last Modified,quote created by,estimate created by,dealer indow rep id" > /srv/jitterbit/orders_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/orders_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/orders_report_tmp1.csv
cat /srv/jitterbit/orders_report_tmp0.csv /srv/jitterbit/orders_report_tmp1.csv > /srv/jitterbit/orders_report.csv

echo "Quote has Item #,Quote #,Item #" > /srv/jitterbit/quotes_have_items_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/quotes_have_items_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/quotes_have_items_report_tmp1.csv
cat /srv/jitterbit/quotes_have_items_report_tmp0.csv /srv/jitterbit/quotes_have_items_report_tmp1.csv > /srv/jitterbit/quotes_have_items_report.csv

echo "Quote #,Job Site #,Order #,Customer Track,username,Total,Created Date,Deleted,tech ID,created by ID,order created by" > /srv/jitterbit/quotes_report_tmp0.csv
mysql -uindowwin_user -p'VM9{fpmugait' indowwin_db < /srv/modi.indowwindows.com/indow_reports/scripts/quotes_report.sql
sed -e 's/,//g' -e 's/|/,/g' -e 's/NULL//g' -i /srv/jitterbit/quotes_report_tmp1.csv
cat /srv/jitterbit/quotes_report_tmp0.csv /srv/jitterbit/quotes_report_tmp1.csv > /srv/jitterbit/quotes_report.csv

rm -rf /srv/jitterbit/*tmp*.csv

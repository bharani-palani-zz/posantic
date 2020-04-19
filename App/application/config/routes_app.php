<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$route['default_controller'] = "default/user";
$route['404_override'] = "site_404";

$route['signin'] = "login/affirm";
$route['logout'] = "login/affirm/logout";

$route['rescue'] = "rescue/rescue/forgot_password";
$route['rescue/forgot_password'] = "rescue/rescue/forgot_password";
$route['rescue/reset_password'] = "rescue/rescue/reset_password";
$route['rescue/change_password'] = "rescue/rescue/change_password";
$route['rescue/replace_password'] = "rescue/rescue/replace_password";
$route['rescue/mail_sent'] = "rescue/rescue/mail_sent";

$route['dashboard'] = "dashboard/dashboard/show_dashboard";

$route['customers'] = "customers/customers/handle";
$route['customers/group'] = "customers/customers/group";
$route['customers/lookup'] = "customers/customers/search";
$route['customers/csv_sample'] = "customers/customers/csv_sample";
$route['customers/bulk_import'] = "customers/customers/import_action";
$route['customers/download'] = "customers/customers/download_customers";
$route['customer_group/delete/id/(:any)'] = "customers/customers/delete_group/$1";
$route['customers/page'] = "customers/customers/handle";
$route['customers/page/(:any)'] = "customers/customers/handle/$1";
$route['customers/edit/id/(:any)'] = "customers/customers/edit_customer/$1";
$route['customers/edit_customer_group/(:any)'] = "customers/customers/edit_customer_group/$1";
$route['customers/delete/id/(:any)'] = "customers/customers/delete_customer/$1";
$route['customers/update/id/(:any)'] = "customers/customers/update/$1";
$route['customers/update_coordinates/id/(:any)'] = "customers/customers/update_coordinates/$1";
$route['customers/update_group/(:any)'] = "customers/customers/update_group/$1";
$route['customers/(:any)'] = "customers/customers/show_customer/$1";


$route['customers/import'] = "customers/customers/import";
$route['customers/add_group'] = "customers/customers/add_group";
$route['customers/create_group'] = "customers/customers/create_group";
$route['customers/add'] = "customers/customers/add";
$route['customers/insert'] = "customers/customers/insert";


$route['products'] = "products/products/index";
$route['products/page'] = "products/products/index";
$route['products/lookup'] = "products/products/search";
$route['products/export'] = "products/products/export";
$route['products/export_kilo_products'] = "products/products/export_kilo_products";

//pagination
$route['products/page/(:any)'] = "products/products/index/$1";
$route['products/(:any)/edit'] = "products/products/edit/$1";
$route['products/update/id/(:any)'] = "products/products/update/$1";
$route['products/create_variant/(:any)'] = "products/products/create_variant/$1";
$route['products/add_variant/id/(:any)'] = "products/products/add_variant/$1";
$route['products/temp_barcode/(:any)'] = "products/products/temp_barcode/$1";
$route['products/draw_jumbo_barcode/(:any)'] = "products/products/draw_jumbo_barcode/$1";
$route['products/make_barcode/(:any)'] = "products/products/make_barcode/$1";
$route['products/delete/id/(:any)'] = "products/products/delete_product/$1";
$route['products/delete/variant/id/(:any)'] = "products/products/delete_all_variant/$1";

$route['products/show/id/(:any)'] = "products/products/activate/$1";
$route['products/hide/id/(:any)'] = "products/products/deactivate/$1";
$route['products/delete_hidden'] = "products/products/delete_hidden";

$route['products/(:any)'] = "products/products/show/$1";
$route['products/handle'] = "products/products/handle";
$route['products/add_product'] = "products/products/add_product";
$route['products/insert_product'] = "products/products/insert_product";
$route['products/bulk_import'] = "products/products/poscloud_bulk_import";
$route['products/suppliers'] = "products/products/poscloud_suppliers";
$route['products/update_barcode_prefix'] = "products/products/update_barcode_prefix";
$route['products/create_category'] = "products/products/create_category";
$route['products/create_supplier'] = "products/products/create_supplier";
$route['products/create_tax'] = "products/products/create_tax";
$route['products/create_brand'] = "products/products/create_brand";
$route['products/category_activity'] = "products/products/category_activity";
$route['products/supplier_activity'] = "products/products/supplier_activity";
$route['products/brand_activity'] = "products/products/brand_activity";
$route['products/tax_activity'] = "products/products/tax_activity";
$route['products/change_barcode_prefix'] = "products/products/change_barcode_prefix";
$route['products/blend_autocomplete'] = "products/products/blend_autocomplete";
$route['products/tag_autocomplete'] = "products/products/tag_autocomplete";
$route['products/insert_tag'] = "products/products/insert_tag";
$route['products/insert_prd_tag'] = "products/products/insert_prd_tag";
$route['products/delete_tag'] = "products/products/delete_tag";
$route['products/get_sub_variants'] = "products/products/get_sub_variants";
$route['products/download'] = "download/download_controller/download_products";
$route['products/import'] = "download/download_controller/import";
$route['products/import_action'] = "download/download_controller/import_action";
$route['products/csv_sample'] = "download/download_controller/bulk_sample";
$route['products/brand'] = "brand/brand/show_brands";
$route['products/categories'] = "brand/brand/show_categories";
$route['products/tags'] = "brand/brand/show_tags";
$route['products/ajax_update_variant_pos'] = 'products/products/ajax_update_variant_pos';
$route['products/add_custom_variant'] = 'products/products/add_custom_variant';

$route['brand/edit_form/(:any)'] = "brand/brand/open_edit_form/$1";
$route['brand/update_brand/(:any)'] = "brand/brand/update_brand/$1";
$route['brand/delete/id/(:any)'] = "brand/brand/delete_brand/$1";
$route['brand/add'] = "brand/brand/add_brand";
$route['brand/insert_brand'] = "brand/brand/insert_brand";

$route['category/edit_cat/(:any)'] = "brand/brand/cat_edit_form/$1";
$route['category/update_cat/(:any)'] = "brand/brand/update_cat/$1";
$route['category/delete/id/(:any)'] = "brand/brand/delete_cat/$1";
$route['category/add'] = "brand/brand/add_cat";
$route['category/insert_cat'] = "brand/brand/insert_cat";

$route['tag/edit_tag/(:any)'] = "brand/brand/tags_edit_form/$1";
$route['tags/update_tag/(:any)'] = "brand/brand/update_tag/$1";
$route['tags/delete/id/(:any)'] = "brand/brand/delete_tag/$1";
$route['tags/add'] = "brand/brand/add_tags";
$route['tag/insert_tag'] = "brand/brand/insert_tags";

$route['payment_method/edit_form/(:any)'] = "payment_method/payment_method/type_edit_form/$1";
$route['payment_method/delete/id/(:any)'] = "payment_method/payment_method/delete_method/$1";
$route['payment_method/update_method/id/(:any)'] = "payment_method/payment_method/update_method/$1";
$route['payment_method/add'] = "payment_method/payment_method/add_type";
$route['payment_method/insert_method'] = "payment_method/payment_method/insert_method";
$route['payment_method/get_pay_type_fields'] = "payment_method/payment_method/get_pay_type_fields";
$route['payment_method/get_country_type_fields'] = "payment_method/payment_method/get_country_type_fields";

$route['barcode/make_barcode'] = "barcode/barcode_controller/make_barcode";

$route['setup'] = "setup/setup/make_setup";
$route['setup/loyalty'] = "setup/setup/set_loyalty";
$route['setup/theme_post'] = "setup/setup/theme_post";
$route['setup/update_loyalty'] = "setup/setup/update_loyalty";
$route['setup/outlets_and_registers'] = "setup/setup/outlets_and_registers";
$route['setup/quicktouch'] = "setup/setup/quick_touch";
$route['setup/add_quicktouch'] = "setup/setup/add_quicktouch";
$route['setup/quicktouch/edit/id/(:any)'] = "setup/setup/show_quick_touch/$1";
$route['quicktouch/delete/id/(:any)'] = "setup/setup/delete_quick_touch/$1";
$route['setup/update_quicktouch/id/(:any)'] = "setup/setup/update_quicktouch/$1";
$route['quicktouch/update'] = "setup/setup/update_quick_touch";
$route['quicktouch/get_uuid'] = "setup/setup/get_uuid";
$route['quicktouch/search'] = "setup/setup/quick_touch_search";
$route['quicktouch/add'] = "setup/setup/quicktouch_add";

$route['setup/shipment'] = "setup/setup/shipment";
$route['setup/pay_methods'] = "setup/setup/pay_methods";
$route['setup/taxes'] = "setup/setup/taxes";
$route['setup/add_tax'] = "setup/setup/add_tax";
$route['setup/add_group'] = "setup/setup/add_group";
$route['setup/update_account'] = "setup/setup/update_account";

$route['account/delete_trial_data'] = "account/account/delete_trial_data";
$route['account/trash_form_trial_data'] = "account/account/trash_form_trial_data";
$route['account/delete'] = "account/account/delete_account";
$route['account/find_plan_pricing'] = "account/account/find_plan_pricing";
$route['account/find_term_pricing'] = "account/account/find_term_pricing";
$route['account/terms'] = "account/account/terms";
$route['account/form_delete_account'] = "account/account/form_delete_account";
$route['account/manage_storage_space'] = "account/account/manage_storage_space";
$route['account/manage_space_for_date_range'] = "account/account/manage_space_for_date_range";
$route['account/form_manage_space'] = "account/account/form_manage_space";
$route['account/find_code_discount'] = "account/account/find_code_discount";

$route['setup/update_group_tax/(:any)'] = "setup/setup/update_group_tax/$1";
$route['setup/update_single_tax/(:any)'] = "setup/setup/update_single_tax/$1";


$route['setup/outlet/(:any)/edit'] = "outlet/outlet/update_outlet/$1";
$route['outlet/(:any)/delete'] = "outlet/outlet/delete_outlet/$1";
$route['setup/outlet/update/id/(:any)'] = "outlet/outlet/update_save_outlet/$1";
$route['setup/outlet/(:any)'] = "outlet/outlet/show_outlet/$1";
$route['setup/outlet/add'] = "outlet/outlet/add_outlet";
$route['setup/outlet/create_outlet'] = "outlet/outlet/create_outlet";

$route['setup/register/(:any)/add'] = "register/register/add_register/$1";
$route['setup/register/(:any)/edit'] = "register/register/update_register/$1";
$route['register/delete/id/(:any)'] = "register/register/delete_register/$1";
$route['setup/register/create_register/id/(:any)'] = "register/register/create_register/$1";
$route['register/update_register/id/(:any)'] = "register/register/modify_register/$1";
$route['setup/register/(:any)'] = "register/register/show_register/$1";

$route['users/update/id/(:any)'] = "users/users/save_user/$1";
$route['users/delete/id/(:any)'] = "users/users/delete_user/$1";
$route['users/delete_image/(:any)'] = "users/users/delete_image/$1";
$route['users/(:any)'] = "users/users/find_user/$1";
$route['users/save_target'] = "users/users/save_target";


$route['setup/receipt_template/show'] = "receipt_template/receipt_template/show_receipt_template";
$route['setup/receipt_template/add'] = "receipt_template/receipt_template/add_receipt_template";
$route['setup/receipt_template/create'] = "receipt_template/receipt_template/create_receipt_template";
$route['setup/receipt_template/(:any)/edit'] = "receipt_template/receipt_template/update_receipt_template/$1";
$route['setup/receipt_template/update/id/(:any)'] = "receipt_template/receipt_template/update_save_receipt_template/$1";
$route['receipt_template/delete/id/(:any)'] = "receipt_template/receipt_template/delete_receipt_template/$1";


$route['taxes/group/add'] = "setup/setup/group_tax";
$route['taxes/add'] = "setup/setup/create_tax";
$route['taxes/edit_group/(:any)'] = "setup/setup/edit_group_tax/$1";
$route['taxes/edit_single/(:any)'] = "setup/setup/edit_single_tax/$1";
$route['taxes/group/(:any)/delete'] = "setup/setup/delete_group_tax/$1";
$route['taxes/(:any)/delete'] = "setup/setup/delete_single_tax/$1";

$route['account'] = "account/account/index";
$route['account/process'] = "account/account/process";
$route['account/payment_gateway'] = "account/account/payment_gateway";


$route['loyalty'] = "loyalty/loyalty/index";
$route['supplier'] = "supplier/supplier/index";
$route['supplier/add'] = "supplier/supplier/add";
$route['supplier/create'] = "supplier/supplier/create";
$route['supplier/change/id/(:any)'] = "supplier/supplier/change/$1";
$route['supplier/(:any)/show'] = "supplier/supplier/show/$1";
$route['supplier/(:any)/edit'] = "supplier/supplier/edit/$1";
$route['supplier/(:any)/delete'] = "supplier/supplier/delete/$1";

$route['users'] = "users/users";
$route['users/add'] = "users/users/add";
$route['users/insert_form_user'] = "users/users/add_user";

$route['promotion/edit/id/(:any)/page/(:num)'] = "promotion/promotion/edit_promotion/$1/$2";
$route['promotion/page'] = "promotion/promotion/promotion_detail";
$route['promotion/(:any)/page/(:num)'] = "promotion/promotion/promotion_detail/$1/$2";

$route['promotion'] = "promotion/promotion/index";
$route['promotion/add'] = "promotion/promotion/add";
$route['promotion/insert_promotion'] = "promotion/promotion/insert_promotion";
$route['promotion/promo_autocomplete'] = "promotion/promotion/promo_autocomplete";
$route['promotion/delete_single_product'] = "promotion/promotion/delete_single_product";
$route['promotion/insert_ajax_promo'] = "promotion/promotion/insert_ajax_promo";

$route['promotion/csv_template'] = "promotion/promotion/download_template";
$route['promotion/edit/id/(:any)'] = "promotion/promotion/edit_promotion/$1";
$route['promotion/change/(:any)'] = "promotion/promotion/update_promotion/$1";

$route['promotion/delete/id/(:any)'] = "promotion/promotion/delete_promotion/$1";
$route['promotion/(:any)'] = "promotion/promotion/promotion_detail/$1";

$route['inventory/freight/edit/id/(:any)'] = "inventory/inventory/edit_transfer/$1";
$route['inventory/freight/cancel/id/(:any)'] = "inventory/inventory/cancel/$1";
$route['inventory/freight/(:any)/return'] = "inventory/inventory/receive/$1";
$route['inventory/freight/(:any)/receive'] = "inventory/inventory/receive/$1";
$route['inventory/freight/(:any)/send'] = "inventory/inventory/send/$1";
$route['inventory/freight/(:any)/marksent'] = "inventory/inventory/marksent/$1";
$route['inventory/freight/(:any)/export'] = "inventory/inventory/export/$1";
$route['inventory/freight/(:any)'] = "inventory/inventory/show_transfer/$1";
$route['inventory/send_email/(:any)'] = "inventory/inventory/send_email/$1";
$route['inventory/update_stock_transfer/(:any)'] = "inventory/inventory/update_stock_transfer/$1";
$route['inventory'] = "inventory/inventory/activity";
$route['inventory/stock_transfer'] = "inventory/inventory/stock_transfer";
$route['inventory/return'] = "inventory/inventory/stock_return";
$route['inventory/stock_take'] = "inventory/inventory/stock_take";
$route['inventory/page'] = "inventory/inventory/activity";
$route['inventory/page/(:any)'] = "inventory/inventory/activity/$1";
$route['inventory/lookup'] = "inventory/inventory/search";
$route['inventory/add_stock_take'] = "inventory/inventory/add_stock_take";
$route['inventory/stock_take/edit/id/(:any)'] = "inventory/inventory/edit_stock_take/$1";


$route['inventory/add_stock_transfer'] = "inventory/inventory/add_stock_transfer";
$route['inventory/transfer_autocomplete'] = "inventory/inventory/transfer_autocomplete";
$route['inventory/insert_ajax_transfer'] = "inventory/inventory/insert_ajax_transfer";
$route['inventory/delete_transfer_single_product'] = "inventory/inventory/delete_transfer_single_product";

$route['inventory/csv_sample'] = "inventory/inventory/csv_sample";
$route['inventory/stock_order'] = "inventory/inventory/stock_order";
$route['inventory/add_stock_order'] = "inventory/inventory/add_stock_order";
$route['inventory/add_stock_return'] = "inventory/inventory/add_stock_return";
$route['inventory/products_get/(:any)'] = "inventory/inventory_json/products_get/$1";
?>
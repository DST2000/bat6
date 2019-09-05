virtuemart_products

id = 6

INSERT INTO `xl9ks_virtuemart_products` (`virtuemart_product_id`, `virtuemart_vendor_id`, `product_parent_id`, `product_sku`, `product_gtin`, `product_mpn`, `product_weight`, `product_weight_uom`, `product_length`, `product_width`, `product_height`, `product_lwh_uom`, `product_url`, `product_in_stock`, `product_ordered`, `product_stockhandle`, `low_stock_notification`, `product_available_date`, `product_availability`, `product_special`, `product_discontinued`, `product_sales`, `product_unit`, `product_packaging`, `product_params`, `product_canon_category_id`, `hits`, `intnotes`, `metarobot`, `metaauthor`, `layout`, `published`, `pordering`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES ('6', '1', '0', '9fd1b648-b409-11de-982d-001bfc3567e4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '50', '0', '0', '0', '0000-00-00 00:00:00.000000', NULL, '0', '0', '0', NULL, NULL, '', NULL, NULL, NULL, NULL, NULL, NULL, '1', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0')


virtuemart_products_en_gb

id = 6

INSERT INTO `xl9ks_virtuemart_products_en_gb` (`virtuemart_product_id`, `product_s_desc`, `product_desc`, `product_name`, `metadesc`, `metakey`, `customtitle`, `slug`) VALUES ('6', '', '', 'Good name', '', '', '', 'good-name')

virtuemart_products_ru_ru

id = 6

INSERT INTO `xl9ks_virtuemart_products_en_gb` (`virtuemart_product_id`, `product_s_desc`, `product_desc`, `product_name`, `metadesc`, `metakey`, `customtitle`, `slug`) VALUES ('6', '', '', 'Good name', '', '', '', 'good-name')

virtuemart_product_prices

id = 1

INSERT INTO `xl9ks_virtuemart_product_prices` (`virtuemart_product_price_id`, `virtuemart_product_id`, `virtuemart_shoppergroup_id`, `product_price`, `override`, `product_override_price`, `product_tax_id`, `product_discount_id`, `product_currency`, `product_price_publish_up`, `product_price_publish_down`, `price_quantity_start`, `price_quantity_end`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES (NULL, '1', '0', '101.110000', '0', '0.00000', '0', '0', '194', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0', '0', '2018-08-29 02:13:23', '949', '2018-08-29 18:15:08', '949', '0000-00-00 00:00:00', '0')


virtuemart_categories

id = 14

INSERT INTO `xl9ks_virtuemart_categories` (`virtuemart_category_id`, `virtuemart_vendor_id`, `category_template`, `category_layout`, `category_product_layout`, `products_per_row`, `limit_list_step`, `limit_list_initial`, `hits`, `cat_params`, `metarobot`, `metaauthor`, `ordering`, `shared`, `published`, `created_on`, `created_by`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES ('14', '1', NULL, NULL, NULL, '', NULL, NULL, '0', '', '', '', '0', '0', '1', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0')


virtuemart_product_categories

id = 7

INSERT INTO `xl9ks_virtuemart_product_categories` (`id`, `virtuemart_product_id`, `virtuemart_category_id`, `ordering`) VALUES ('7', '6', '14', '0')


virtuemart_categories_en_gb

id = 14

INSERT INTO `xl9ks_virtuemart_categories_en_gb` (`virtuemart_category_id`, `category_name`, `category_description`, `metadesc`, `metakey`, `customtitle`, `slug`) VALUES ('14', 'categoryname1', '', '', '', '', 'categoryname1')


virtuemart_categories_ru_ru

id = 14

INSERT INTO `xl9ks_virtuemart_categories_ru_ru` (`virtuemart_category_id`, `category_name`, `category_description`, `metadesc`, `metakey`, `customtitle`, `slug`) VALUES ('14', 'Название категории', '', '', '', '', 'nazvanie-kategorii')


virtuemart_category_categories

id = 14

INSERT INTO `xl9ks_virtuemart_category_categories` (`id`, `category_parent_id`, `category_child_id`, `ordering`) VALUES (NULL, '0', '14', '0')


virtuemart_customs

id = 9

INSERT INTO `xl9ks_virtuemart_customs` (`virtuemart_custom_id`, `custom_parent_id`, `virtuemart_vendor_id`, `custom_jplugin_id`, `custom_element`, `admin_only`, `custom_title`, `show_title`, `custom_tip`, `custom_value`, `custom_desc`, `field_type`, `is_list`, `is_hidden`, `is_cart_attribute`, `is_input`, `searchable`, `layout_pos`, `custom_params`, `shared`, `published`, `created_on`, `created_by`, `ordering`, `modified_on`, `modified_by`, `locked_on`, `locked_by`) VALUES ('9', '0', '1', '0', '', '0', 'cUsToMName', '1', '', NULL, NULL, 'F', '0', '0', '0', '0', '1', NULL, '', '0', '1', '0000-00-00 00:00:00.000000', '0', '0', '0000-00-00 00:00:00.000000', '0', '0000-00-00 00:00:00.000000', '0')





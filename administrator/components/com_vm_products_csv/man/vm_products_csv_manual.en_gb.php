<?php
/*
 * @title		com_vm_products_csv
 * @version		3.1.3
 * @package		Joomla
 * @author		ekerner@ekerner.com.au
 * @website		http://www.ekerner.com.au
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * @copyright		Copyright (C) 2012 eKerner.com.au All rights reserved.
 */

defined('_JEXEC') or die('Restricted access!'); 

$admin_label = 'Administrator';
$import_label = 'Import Products ';
$export_label = 'Export Products ';
$page_title_html = 'Documentation for Joomla Component VM Products CSV <span style="color:#204D02;">!ULTIMATE!</span> v3 by <a href="http://www.ekerner.com/" title="Developer website" onclick="window.open(this.href); return false;">ekerner.com</a>';
if ($is_joomla) {
	$admin_label = '<a title="Joomla Administrator" href="' . $admin_url . '">' . $admin_label . '</a>';
	$import_label = '<a title="' . $import_label . '" href="' . $admin_url . 'index.php?option=com_vm_products_csv&amp;view=import_products' . '">' . $import_label . '</a>';
	$export_label = '<a title="' . $export_label . '" href="' . $admin_url . 'index.php?option=com_vm_products_csv&amp;view=export_products' . '">' . $export_label . '</a>';
}
echo('
	<div>
		<div style="margin:0 40px;"><a href="http://www.ekerner.com.au/" title="eKerner - Application Developer" onclick="window.open(this.href); return false;"><img src="http://www.ekerner.com.au/images/logos/ekerner_technical_development_logo_nobg.png" alt="eKerner Logo" title="eKerner - App Developer" /></a></div>
		<h2 style="margin-left:40px;">' . $page_title_html . '</h2>
		<div style="margin:0 40px;">CSV Bulk Product import/export/update for VirtueMart.</div>
		<div style="margin:0 40px;">
			<h3>Language ...</h3>
			<form method="post" action="" style="margin:0 40px;"><div>
				<select name="manual_lang" onchange="this.form.submit();">
					'.$manual_lang_select_options .'
				</select>
			</div></form>
		</div>
		<div style="margin:0 40px;">
			<h3>Menu ...</h3>
			<ul>
				<li><a title="" href="#usage">Usage</a></li>
				<li><a title="" href="#supported_fields">Supported Fields</a></li>
				<li><a title="" href="#features">Features</a></li>
				<li><a title="" href="#languages">Languages</a></li>
				<li><a title="" href="#notes">Notes</a></li>
				<li><a title="" href="#screenshots">Screenshots</a></li>
			</ul>
		</div>
		<a name="usage"></a>
		<div style="margin:0 40px;">
			<h3>Usage ...</h3>
			<div style="margin:0 40px;">
				See: ' . $admin_label . ' -&gt; Main Menu -&gt; Components -&gt; VM Products CSV -&gt; ' . $import_label . '<br />
				And: ' . $admin_label . ' -&gt; Main Menu -&gt; Components -&gt; VM Products CSV -&gt; ' . $export_label . '
			</div>
			<p><a title="Top of page" href="#">[ top ]</a></p>
		</div>
		<a name="supported_fields"></a>
		<div style="margin:0 40px;">
			<h3>Supported Fields ...</h3>
			<ul>
				<li><b>virtuemart_vendor_id</b> :
					The vendor id, A positive integer. Defaults to 1. If you have a single user &lt;-&gt; multi vendor VM configuration then you can edit all vendor products, manufacturers, and categories. If you have a multi user &lt;-&gt; multi vendor VM configuration then an admin can still edit all products however I dont recommend sharing the VM Products CSV components with your users or they will be able to edit each others products. For a multi user &lt;-&gt; multi vendor VM configuration you would need to make a screen to enable you users to submit their csv changes to the admin or to an app the extends/employs VM Products CSV.
				</li>
				<li><b>product_sku</b> :
					If you want to update an existing record then you need to reference it by SKU in your CSV, and also assure that mode \'Update\' or mode \'Syncronize\' is selected. Otherwise SKU is optional and one will be generated if not provided. If you provide a duplicate SKU and mode \'Add\' is selected then the record will be skipped.
				</li>
				<li><b>product_parent_sku</b> :
					To declare a record as a child product then add its parent SKU here. Parents and children may appear in any order.
				</li>
				<li><b>product_name</b> :
					The title of your product. Is also used for page titles and meta data where not provided.
				</li>
				<li><b>product_alias</b> : Also known as \'slug\' and used for SEO friendly URLs. No spaces or non URL characters. Example: \'my-product-name\'. If not provided then a product alias (slug) will be made from the product name.</li>
				<li><b>product_short_desc</b> : Short description of product as per VM field settings.</li>
				<li><b>product_desc</b> : Description of product as per VM field settings.</li>
				<li><b>meta_description</b> : If not provided then meta description is made from sitename and product name.</li>
				<li><b>meta_keywords</b> : If not provided then meta keywords are made from product name.</li>
				<li><b>product_price</b> : The \'product base price \' as you would enter it into VM. So if you normally convert for tax inclusion, like price_less_10_percent_tax = (price / 11) * 10, then that is the price to enter, plus in this example you would also need to set product_tax_id_or_name.</li>
				<li><b>product_currency_code</b> : Eg: \'AUD\'. Currencies entered must be installed and enabledi in your Joomla/VM.</li>
				<li><b>manufacturer_name</b> : The name of the manufacturer. If named manufacturer doesnt exist, and \'Allow manufacturer creation\' is checked, then the manufacturer will be created with manufacturer_desc and manufacturer_image.</li>
				<li><b>manufacturer_desc</b> : The description for the manufacturer. If manufacturer_name exists and manufacturer_desc is both provided and different to the current manufacturer_desc then it will be updated.</li>
				<li><b>manufacturer_image</b> : The image media file url for the manufacturer. If manufacturer_name exists and manufacturer_image is both provided and different to the current manufacturer_image then it will be updated.</li>
				<li><b>category_id</b> : Positive integer Category ID. If you are updating and existing category then it is required. You may assign a category to a product via category_id or category_name. NOTE: If Multiple Category Mode is checked then this is a comma (,) delimited list of existing category IDs, and all other category related fields are ignored. See notes below.</li>
				<li><b>category_name</b> : The name of the category. If named category doesnt exist, and \'Allow category creation\' is checked, then the category will be created with category_desc and category_image. If category_id is provided and category_name is differnt to the current category_name then it will be updated.</li>
				<li><b>category_desc</b> : Category description. If the category exists and the category_desc is provided and the category_desc differs from the current category_desc then it will be updated.</li>
				<li><b>category_image</b> : Category image media file url. If the category exists and the category_image is provided and the category_image differs from the current category_image then it will be updated.</li>
				<li><b>category_parent_id_or_name</b> : The Category ID or Category Name of the parent category. If provided then it will be used. A value of \'0\' means \'Top level category\'. If you are searching for a category by name and you do not know the parent id, or if the named category parent was created by a previous record in the same CSV file (and so you cannot yet know the id), then leave this blank/empty.</li>
				<li><b>product_tax_id_or_name</b> : If you wish to apply a tax to this product then pass its ID or Name here. Eg: \'1\' or \'Australian GST\'. You need to first login to your Virtuemart administrator, create the tax, and record/note its ID or Name. Leave blank or set to \'0\' for \'apply default rule\', set to \'-1\' for \'Apply no rules\'.</li>
				<li><b>product_discount_id_or_name</b> : If you wish to apply a discount/offer to this product then pass its ID or Name here. Eg: \'2\' or \'10 Percent Discount\'. You need to first login to you Virtuemart administrator, create the discount/offer, and record/note its ID or Name. Leave blank or set to \'0\' for \'apply default rule\', set to \'-1\' for \'Apply no rules\'.</li>
				<li><b>product_weight</b> : Product weight as floating point number, and as per product_weight_unit.</li>
				<li><b>product_weight_unit</b> : Product weight unit of measurement. Eg: \'kg\', \'g\'.</li>
				<li><b>product_length</b> : Product length as floating point number, and as per product_lwh_unit.</li>
				<li><b>product_width</b> : Product width as floating point number, and as per product_lwh_unit.</li>
				<li><b>product_height</b> : Product height as floating point number, and as per product_lwh_unit.</li>
				<li><b>product_lwh_unit</b> : Product length/width/height unit of measurement. Eg: \'mm\', \'cm\'.</li>
				<li><b>product_stock_quantity</b> : The number of items in stock. Defaults to \'0\'.</li>
				<li><b>product_availability</b> : As per VM Product Availability. Defaults to \'In Stock\'.</li>
				<li><b>featured_product</b> : Set to \'1\', \'y\', or \'yes\' for featured product.</li>
				<li><b>published</b> : Set to \'1\', \'y\', or \'yes\' for pulished/enabled product. Defaults to \'1\' if not set.</li>
				<li><b>downloadable_media_id</b> : Only applies if you have plg_vmcustom_downloadable installed (See <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Downloadable Products</a>). The Media ID of a \'For Sale\' Virtuemart Media File. Refer your Joomla Admin -&gt; Virtuemart -&gt; Shop -&gt; Media Files. Defaults to \'0\'.</li>
				<li><b>downloadable_order_states</b> : Only applies if you have plg_vmcustom_downloadable installed (See <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Downloadable Products</a>). A comma separated list of valid Virtuemart order status names. Example: \'Confirmed, Shipped\'. You can find your order status names in your Joomla Admin -&gt; Virtuemart -&gt; Configration -&gt; Order Statuses (I do not recommend editing them as it can mess up some plugins). Defaults to \'\' which translates to \'Confirmed, Shipped\' if downloadable_media_id is set.</li>
				<li><b>downloadable_expiry</b> : Only applies if you have plg_vmcustom_downloadable installed (See <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Downloadable Products</a>). The period after which your download link expires. Format: \'<span style="font-style:italic;">quantity period</span> \', where <span style="font-style:italic;">quantity</span> is an iteger from 0 to 11, and <span style="font-style:italic;">period</span> is \'days\', \'weeks\', \'months\', or \'years\'. Examples: \'2 days\', \'6 weeks\', \'1 years\', or \'1 year\'. Defaults to \'\' which translates to \'never expires\' if downloadable_media_id is set.</li>
				<li><b>custom_cart_variants</b> : Sets Cart Variant Custom Fields. For example if the associated product comes in colors red and blue, as well as sizes small and large, and there is an extra 1 dollar charge for large size, then set this field to \'Color:Red:0,Color:Blue:0,Size:Small:0,Size:Large:1\'. NOTES: For each declared type (eg: Color, Size) if a Cart Variant custom field does not exists with the same name then it will be created. The characters comma (,) and colen (:) cannot appear in the title, value, or price of the custom field. Any existing cart variant values will be deleted and the product will be left with the cart variants defined in the CSV.</li>
				<li><b>[ product_image_1 . . .</b> : Product image media file url. Add as many as you like. Best to upload images first.</li>
				<li><b>&nbsp;&nbsp;[ product_thumbnail_1 . . . ] ] </b> : Product image media thumb url. Best to leave this blank and let VM generate the thumbnail images. Best to upload images first if you are going to use it.</li>
			</ul>
			<div style="margin:0 40px;">... implying unlimited images, just increment the number, ie: product_image_&lt;number&gt;</div>
			<p><a title="Top of page" href="#">[ top ]</a></p>
		</div>
		<a name="features"></a>
		<div style="margin:0 40px;">
			<h3>Features ...</h3>
			<ul>
				<li>Supports 3 import modes: \'Add\' (Only add products if they dont exist by SKU), \'Update\' (\'Add\' plus update existing products by SKU), and \'Syncronize\' (\'Update\' plus delete products not found in the CSV).</li>
				<li>Accepts unlimited product images with optional thumbnails. Also handles category and manufacturer images, see below.</li>
				<li>Validates each row before performing database operations.</li>
				<li>Generates Product and Category page title, meta description, and meta keywords where not supplied using names and descriptions.</li>
				<li>Supports multi-lingual Joomla/Virtuemart configurations. Uses VM languages settings. NOTE: In mode \'Syncronize\' the deletion of products is NOT biased by language.</li>
				<li>Validates passed ids (eg: virtuemart_vendor_id, manufacturer_id, product_currency_code, product_tax_id_or_name, product_discount_id_or_name, etc) and takes all precautions to prevent inserting invalid data into the database.</li>
				<li>Supports creation of Category if not exists. Accepts name, description, image location, and parent id or name. Also allows for update of category name, description, image location, and parent category via CSV.</li>
				<li>Alternative to the previous point, you can select \'Multiple Category Mode\' and associate products with multiple existing categories.</li>
				<li>Creates Manufacturer if not exists. Accepts name, description, and image location. Also allows for update of manufacturer description and image location via CSV.</li>
				<li>\'Featured Product\' switch.</li>
				<li>\'Published\' switch.</li>
				<li>Allows Parent &lt;-&gt; Child Product association.</li>
				<li>Supports Product Tax assignment by ID or name</li>
				<li>Supports Product Discount assignment by ID or name</li>
				<li>Records product weight, dimensions, stock quantity, and availability.</li>
				<li>Checks for existing rows and performs update or insert operations.</li>
				<li>Warns if you add an image that hasnt yet been added to the media gallery.</li>
				<li>Supports Virtuemart custom plugin plg_vmcustom_downloadable (See <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Downloadable Products</a>).</li>
                                <li>Supports cart variant custom fields like Size and Color.</li>
			</ul>
			<p><a title="Top of page" href="#">[ top ]</a></p>
		</div>
		<a name="languages"></a>
		<div style="margin:0 40px;">
			<h3>Languages ...</h3>
			<p>
				All languages are handled by the importer and exporter. As long as you select your correct langauge and character encoding, make or export your CSV in the correct character encoding, and your server locale settings are correct, you can import/export in any languages.
			</p>
			<p>
				If your language is not in the \'Character set\' list then it comes under \'Default [CP1252]\'.
			</p>
			<p>
				Your database character encoding is attained by querying your database and if not determinable defaults to UTF8.
			</p>
			<p>
				The interface supports English, Italian, Spanish, and French (Except this document which supports English and French).<br/>
				This of course means that although you can import/export in any language, you will need to understand a supported interface language to use the extension.<br/>
				If you want a copy for which the interface supports your language then work with me to make/translate the language files and I will give you a copy (real transaltion, no google translate).
			</p>
			<p><a title="Top of page" href="#">[ top ]</a></p>
		</div>
		<a name="notes"></a>
		<div style="margin:0 40px;">
			<h3>Notes ...</h3>
			<ul>
				<li>This extension works on your database so as with everything of this nature always make backups first. If you select the wrong character encoding and you dont have a backup you may cry ;)</li>
				<li>When adding images: Its best if the images are uploaded first. You can add as many images as you like to the end of the CSV rows and the thumbnails are optional. Example: product_image_1,product_thumbnail_1,product_image_2,product_image_3,product_thumbnail_3. Its best to leave the image_thumbnail_N fields blank and set VM to generate thumbnails automatically.</li>
				<li>CSV format is standard: record delimiter = newline, field delimiter = comma, field escape character = double quote.</li>
				<li>The interface currently supports English, Italian, Spanish, and French (The manual is only in English). If you make other language files (legitimate translations by a human proficient in the language, not an app) please pass them to me. If I use your lang file(s) I offer you a free extension plus credit you and links to you from the admin screens.</li>
				<li>Some fields are named like *_id_or_name. For such fields, you cannot pass a numeric name or it will be treated as an id. So for example: if you had a category named \'1\' (not very useful in document retrieval like search engines but you can do it), with the id of \'27\', and you want to reference that category as a parent category, then in the category_parent_id_or_name field you need to enter \'27\' not \'1\'.</li>
				<li>If Multiple Category Mode is selected category_id is expected to be a comma delimited list of existing category IDs, and all other category related fields are ignored.</li>
				<li>Record conflict may occur if: You are adding products, youve supplied SKUs for said products in the CSV, you already have products in your shop, and the import mode is \'Update\' or \'Syncronize\'. To avoid record/product conflicts select import mode \'Add\' when first adding products under these conditions.</li>
				<li>You can delete all products by selecting mode \'Syncronize\' and importing a blank/template CSV (containing just the header row).</li>
				<li>Products/records without SKUs will be assigned a unique one made from a combination of the product name abbreviation and unused numbers.</li>
				<li>CSV character encoding defaults to CP1252 (Windows-1252 and Latin-1 compatible) which covers many languages.<br/>The character encoding of your import CSV files should match the encodng you select. If your language is not listed in the \'Character set\' selector then choose \'Default [CP1252]\'.<br/>If your having problems with characters rendering strangely - either in your spreadsheet viewer or your website - then check your Character set/encoding.</li>
				<li>Your database character encoding is determined by querying your database variable \'character_set_database\'.</li>
				<li>Works in multi-vendor store but if you want to allow your  vendors to use this then you need to have me add some stuff. Like for one it doesnt check that the current user has a vendor id and it matches the supplied vendor id. So what Im saying is that your vendors - if allowed to use this as an admin - could edit each others products, which Im guessing you dont want. So to re-iterate: If you for some reason have the need to let your vendors use this app then we need to make some tweaks first.</li>
				<li>I made this into a nice plugin that embeds into the admin vm products manager view. It seemed ideal at first however contained overrides with many hacks to various admin vm products MVCs, and since so many (Id say most) vm shop templates and plugins have vm class and tmpl overrides, it was causing conflicts - like many vm plugins already do. Its not the fault of vm though, the problem is in the design of the Joomla framework and its overriding techniques. Anyways as a component it is standalone and uneffecting on other extensions (although obviously it expects Virtuemart).</li>
				<li>Fields starting with \'downloadable_\' are only used if plg_vmcustom_downloadable is installed (See <a href="http://shop.ekerner.com/index.php/shop/vmcustom-downloadable-detail" title="Buy plg_vmcustom_downloadable" onclick="window.open(this.href); return false;">Virtuemart Downloadable Products</a>).</li>
			</ul>
			<p><a title="Top of page" href="#">[ top ]</a></p>
		</div>
		<a name="screenshots"></a>
		<div style="margin:0 40px;">
			<h3>Screenshots ...</h3>
			<div style="margin:0 40px;">
				<p><b>Import Screen</b></p>
				<p><img style="width:80%; height:;" src="' . $admin_url . 'components/com_vm_products_csv/images/com_vm_products_csv_import_screen.png" alt="Import Screen" /></p>
			</div>
			<div style="margin:0 40px;">
				<p><b>Export Screen</b></p>
				<p><img style="width:80%; height:;" src="' . $admin_url . 'components/com_vm_products_csv/images/com_vm_products_csv_export_screen.png" alt="Export Screen" /></p>
			</div>
			<p><a title="Top of page" href="#">[ top ]</a></p>
		</div>
		<div style="margin:40px;">Have fun,<br />Eugene Kerner.<br /><br /></div>
	</div>
');
?>

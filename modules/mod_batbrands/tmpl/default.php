<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_batbrands
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::register('BatbrandHelper', JPATH_ROOT . '/components/com_batbrands/helpers/batbrand.php');
?>
<div class="batbrandgroup<?php echo $moduleclass_sfx; ?>">
<?php if ($headerText) : ?>
	<?php echo $headerText; ?>
<?php endif; ?>

	<style>
	/* < BRANDS */
	/* растягивающиеся блоки*/
	.row-flex {
	  display: flex;
	  flex-wrap: wrap;
	}
	body {
		min-width: 320px;
	}
	</style>

<div class="row row-flex row-conformity">
	<?php foreach ($list as $item) : ?>
		<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 brandbox">
			<?php $background_color = $item->background_color; ?>
			<div class="<?php echo $item->alias.' brand';?>" 
				 <?php if (!empty($background_color)) echo 'style="background-color: '.$background_color.'"';?> >
				<?php $link = $item->clickurl ; ?>
				<?php $image_big = $item->image_big; ?>
				<?php $image_big_alt = $item->image_big_alt; ?>
				<?php $image_small = $item->image_small; ?>
				<?php $image_small_alt = $item->image_small_alt; ?>
				<?php $header_title = $item->header_title; ?>
				<?php $header_text = $item->header_text; ?>
				<?php $middle_text = $item->middle_text; ?>
				<?php $footer_text = $item->footer_text; ?>
				<?php if (!empty($link)) : ?>
					<a href="<?php echo $link; ?>" target="_self" rel=""
						title="<?php echo htmlspecialchars($item->name, ENT_QUOTES, 'UTF-8'); ?>">
				<?php endif; ?>
					<h2 class="header center">
						<?php if (!empty($header_title)) echo $header_title;?>
					</h2>
					<?php if (BatbrandHelper::isImage($image_big)) : ?>
							<?php $baseurl = strpos($image_big, 'http') === 0 ? '' : JUri::base(); ?>
							<img
								src="<?php echo $baseurl . $image_big; ?>"
								alt="<?php echo $image_big_alt;?>"
								class="img_big img-fluid center"
							/>
					<?php endif; ?>
					
					<ul style="list-style: none;">
						<li>
							<?php if (!empty($header_text)) echo $header_text; ?>
						</li>
						<li>
							<?php if (!empty($middle_text)) echo $middle_text; ?>
						</li>
						<li>
							<?php if (!empty($footer_text)) echo $footer_text; ?>
						</li>
					</ul>
					<div>
						<?php if (BatbrandHelper::isImage($image_small)) : ?>
							<?php $baseurl = strpos($image_small, 'http') === 0 ? '' : JUri::base(); ?>
							<img
								src="<?php echo $baseurl . $image_small; ?>"
								alt="<?php echo $image_small_alt;?>"
								class="img_small"
							/>
						<?php endif; ?>
					</div>	
				<?php if (!empty($link)) : ?>
					</a>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
	
	<script>
		jQuery(window).on('load',  function() {
			jQuery('.brandbox').click(
							function(){
//								$new_src = jQuery(this).find('.img_big').attr('src_big');
//								$new_alt = jQuery(this).find('.img_big').attr('alt_big');
//								jQuery('.img_big').hide();
//								jQuery('.img_big').attr('src', ' ');
//								jQuery('.img_big').attr('alt', ' ');
//								jQuery('.show_big').hide();
//								jQuery('.bigblock').remove();
//								jQuery('.arrows_add').remove();
//								jQuery(this).find('.img_big').css('display', 'block');
//								jQuery(this).find('.img_big').attr('src', $new_src);
//								jQuery(this).find('.img_big').attr('alt', $new_alt);					
//								jQuery(this).find('.show_big').css('display', 'block');	
//							
//								jQuery(this).parent('.col-md-4')
//									.after('<div class="col-md-12 bigblock"></div>');
//								jQuery('.new_image_big').attr('src', $new_src);
//								jQuery(this).clone().appendTo('.bigblock');
//								jQuery('.bigblock').find('.img_big').css('display', 'inline');
//								jQuery('.bigblock').find('.img_big').attr('src', $new_src);
//								jQuery('.bigblock').find('.img_big').attr('alt', $new_alt);					
//								jQuery('.bigblock').find('.img_small').css('display', 'none');	
//								jQuery('.bigblock').css('background', '#42ECEF');	
//								jQuery(this).find('.img_big').css('display', 'none');
//								jQuery(this).find('.show_big').css('display', 'none');
//								jQuery(this).parent('.col-md-4').append('<span class="arrows_add text-info" style="background:#42ECEF">&darr;</span>');
//								jQuery('.bigblock').find('p.header').remove();

	
							}
						);
		});
	</script>
</div>

<?php if ($footerText) : ?>
	<div class="batbrandfooter">
		<?php echo $footerText; ?>
	</div>
<?php endif; ?>
</div>

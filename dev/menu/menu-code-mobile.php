<div class="top-menu hidden-md hidden-lg">
	
	
	<div id="top-menu-mobile" class="navbar navbar-fixed-top">

		<div class="container">
			
			<div class="row">
				
				<div class="col-xs-3">
					<div id="logo-mobile" class="logo-mobile">
						<img id="logo-mobile" class="logo-mobile" src="/dev/logo/logo_bat_auto_trade.svg" alt="logo bat">
					</div>
				</div>	
				<div class="col-xs-1">
					<div class="operator-mobile">
						<img class="phone-mobile" src="/dev/logo/cell-icons_vel_mts_2.svg" alt="mts vel">
					</div>
				</div>	
				<div class="col-xs-6">
					<div id="phone-mobile" class="phone-mobile">
						<span>351 47 47</span>
					</div>
				</div>
				<div class="col-xs-2">
					<div id="menu-mobile" class="menu-mobile">
						<img class="lines-menu-mobile" src="/dev/logo/lines_menu_2.svg" alt="menu">
					</div>
				</div>
			</div>
			
			<div class="row shadow-menu-mobile">
				<div class="col-xs-5">
					<div id="catalog-mobile" class="catalog-mobile">
						КАТАЛОГ ТОВАРОВ
					</div>
				</div>
				<div class="col-xs-2">
					<div id="mobile-search" class="mobile-search">
						<i class="fa fa-search"></i>
					</div>
				</div>
				<div class="col-xs-5">
					<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart'); ?>">
					<div id="mobile-basket" class="mobile-basket">
						<span>КОРЗИНА (0)</span>
					</div>
					</a>
				</div>
			</div>
			<div class="row">
				<div class="header-mid__search" style="display: none;">
					<form action="/catalog" method="GET">
						<div class="row">
							<div class="form-group">
								<div class="col-xs-12">
									<div class="input-group">
										<input type="text" name="keyword" class="inputbox form-control" placeholder="Поиск..">
										<span class="input-group-addon"> <i class="fa fa-search"></i> Найти</span>
										<input type="submit" value="Искать" class="button push" onclick="this.form.keyword.focus();">
									</div>
								</div>
								<input type="hidden" name="view" value="category">
								<input type="hidden" name="option" value="com_virtuemart">
								<input type="hidden" name="virtuemart_category_id" value="0">
								<input type="hidden" name="Itemid" value="117">
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="header-mid__mobile-menu" style="display: none;">
					<ul class="mobile-menu">
						<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=0&virtuemart_manufacturer_id=0'); ?>" class="mobile-menu-item">
							<li class="mobile-menu-item">
								<span>КАТАЛОГ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&Itemid=317'); ?>" >
							<li class="mobile-menu-item">
								<span>КОНТАКТЫ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=category&id=11&Itemid=0'); ?>" >
							<li class="mobile-menu-item">
								<span>АКЦИИ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=category&id=10&Itemid=0'); ?>" >
							<li class="mobile-menu-item">
								<span>НОВОСТИ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=category&id=12&Itemid=0'); ?>" >
							<li class="mobile-menu-item">
								<span>ТЕХНИЧЕСКАЯ ИНФОРМАЦИЯ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=8'); ?>" >
							<li class="mobile-menu-item">
								<span>СДАТЬ СТАРЫЙ АККУМУЛЯТОР</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=9'); ?>" >
							<li class="mobile-menu-item">
								<span>РОЗНИЧНАЯ СЕТЬ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('index.php?option=com_content&view=article&id=10'); ?>" >
							<li class="mobile-menu-item">
								<span>ОПТОВЫЕ ПОКУПАТЕЛИ</span>
							</li>
						</a>
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="header-mid__mobile-catalog" style="display: none;">
					<ul class="mobile-menu">
						<a href="<?php echo JRoute::_('catalog/akkumulators/'); ?>" class="mobile-menu-item">
							<li class="mobile-menu-item">
								<span>АККУМУЛЯТОРЫ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('catalog/filter/q8/'); ?>" >
							<li class="mobile-menu-item">
								<span>МАСЛА Q8</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('catalog/lamps/'); ?>" >
							<li class="mobile-menu-item">
								<span>АВТОЛАМПЫ</span>
							</li>
						</a>
						<a href="" >
							<li class="mobile-menu-item">
								<span>ПРИНАДЛЕЖНОСТИ ДЛЯ АККУМУЛЯТОРОВ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('catalog/filter/zaryadnye-ustrojstva-i-aksessuary/'); ?>" >
							<li class="mobile-menu-item">
								<span>ЗАРЯДНЫЕ УСТРОЙСТВА, ТЕСТЕРЫ</span>
							</li>
						</a>
						<a href="<?php echo JRoute::_('catalog/filter/schetki-stekloochistitelya/'); ?>" >
							<li class="mobile-menu-item">
								<span>ЩЕТКИ ДЛЯ СТЕКОЛ</span>
							</li>
						</a>
					</ul>
				</div>
			</div>
		</div>

	</div>
	<!-- /#top-menu-mobile -->
</div> <!-- /.top-menu xs sm -->

<script>
	jQuery(function($){
		function MyFuncHideMenu(){
			$("div.header-mid__mobile-menu").css({
				"display":"none"
			});
			$("div.header-mid__mobile-catalog").css({
				"display":"none"
			});
			$("div.header-mid__search").css({
				"display":"none"
			});
		};
		$("div#menu-mobile").on("click", function(event){
			MyFuncHideMenu(this); 
			/*$("div.header-mid__mobile-menu").toggle("blind");*/
			$("div.header-mid__mobile-menu").css({
				"display":"block"
			});
		});
		$("div#catalog-mobile").on("click", function(event){
			MyFuncHideMenu(this);
			/*$("div.header-mid__mobile-catalog").toggle("blind");*/
			$("div.header-mid__mobile-catalog").css({
				"display":"block"
			});
		});
		$("div#mobile-search").on("click", function(event){
			MyFuncHideMenu(this);
			/*$("div.header-mid__search").toggle("blind");*/
			$("div.header-mid__search").css({
				"display":"block"
			});
		});
	});
</script>
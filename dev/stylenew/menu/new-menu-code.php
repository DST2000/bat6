<!-- Модальное окно контакт -->  
<div class="modal fade" id="tel-overlay" tabindex="-1" role="dialog" aria-labelledby="tel-iconLabel" aria-hidden="true">
<div class="modal-dialog modal-sm" role="document">
<div class="modal-content py-4" style="background: #F3F3F3">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; right: 15px; top: 20px; ">
<span aria-hidden="true">&times;</span>
</button>
<h4 class="modal-title text-center" style="font-weight: bold; font-size:20px;">Контакт с нами</h4>
      </div>
<div class="modal-body row" style="line-height: 1.8">
      
<div class="col-xs-10 col-md-10 col-xs-offset-3 topTel">
<p><i class="fas fa-phone-square"></i>
<span>Интернет магазин<br></span>
<a href="tel:+375293514747" style="font-size:18px;">+375 29 351 47 47</a><br>
<a href="tel:+375333514747" style="font-size:18px;">+375 33 351 47 47</a></p>
</div>

<div class="col-xs-10 col-md-10 col-xs-offset-3 topTel">
<p><i class="fas fa-phone-square"></i><span>
Оптовым покупателям<br></span>
<a href="tel:+375175106060" style="font-size:18px;">+375 17 510 60 60</a><br>
<a href="tel:+375293106010" style="font-size:18px;">+375 29 310 60 10</a></p>
</div>
</div>
	
<!--
      <div class="modal-footer text-center hidden">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal" style="width: 100%;">Получить консультацию</button>
      </div>
-->
    </div>
  </div>
</div>
<!-- Модальное окно контакт ---------------------------------------------------------------->

<header class="header">


<div class="navbar bg-black navbar-fixed-top">

<!--Верхнее меню---------------------------------------------------------------------------->
<div class="container block-size">   

<a href="/" class="topLogoWhite"><img src="images/logo/logo-white.png" alt=""/></a>


<!--Кнопка десктопного меню---> 
<a class="navbar-catalog-button hidden-xs" data-toggle="collapse" data-target="#topMenuPanel" aria-controls="topMenuPanel" aria-expanded="false" aria-label="Toggle navigation">
<span></span>
</a>
<!--Кнопка десктопного меню--->  
 

<!--Кнопка мобильного меню--->  
<a class="navbar-catalog-button hidden-lg hidden-md" data-toggle="collapse" data-target="#topMenuMobile" aria-controls="topMenuMobile" aria-expanded="false" aria-label="Toggle navigation">
<span></span>
</a>
<!--Кнопка мобильного меню--->  
  
<!----->      
<ul class="nav navbar-nav hidden-xs">                            
        <li><a href="/news?start=0">Новости</a></li>
        <li><a href="/roznichnym-pokupatelyam/46-dostavka">Доставка</a></li>
        <li><a href="/contacts">О компании</a></li>
        <li><a href="/component/content/category/12-tekhnicheskaya-informatsiya?Itemid=0">Техническая информация</a></li>
        <li><a href="/sdat-staryj-akkumulyator">Сдать аккумулятор</a></li>
        <li><a href="/roznichnaya-set">Розничная сеть</a></li>
        <li><a href="/contacts">Контакты</a></li>
		<li><a href="/warranty">Гарантия</a></li>
                        
</ul>
<!--//--->  

<!----->
<div class="topTelDub col-sm-6 col-md-8 col-lg-7 hidden-xs">
<p class="col-md-6 col-lg-5"><i class="fas fa-phone-square"></i>
Интернет магазин<br>
<a class="PhoneNumberMenu" href="tel:+375293514747">+375 29 351 47 47</a> <a href="" class="PhoneNumberMenu"><span class="black">123</span></a> <a class="PhoneNumberMenu" href="tel:+375333514747">+375 33 351 47 47</a>
</p>
<p class="col-md-6 col-lg-5"><i class="fas fa-phone-square"></i>
Оптовым покупателям<br>
<a class="PhoneNumberMenu" href="tel:+375175106060">+375 17 510 60 60</a><a href="" class="PhoneNumberMenu"><span class="black">123</span></a> <a class="PhoneNumberMenu" href="tel:+375293106010">+375 29 310 60 10</a>
</p>

</div>
<!--//--->


<div class="blockTopIco">
<!--Кнопка мобильной корзины--->  
<a id="cart-icon" class="btn-left fas fa-shopping-cart" href="/catalog/cart">
<span class="cart-order-quantity" >0</span>	
</a>
<!--Кнопка мобильной корзины--->  


<!--Кнопка мобильного входа--->  
<a id="user-icon" class="btn-left fas fa-user-tie" href="/optovye-pokupateli"></a>
<!--Кнопка мобильного входа--->  


<!--Кнопка мобильной тел--->  
<a id="tel-icon" class="btn-left fas fa-phone hidden-lg hidden-md" data-toggle="modal" data-target="#tel-overlay"></a>
<!--Кнопка мобильной тел---> 
</div> 

<?php // { DST ?>
<form class="searchForm hidden-xs hidden-md" style="padding-left: 10px;" action="/catalog" method="GET">
	<div class="form-group">
		<button type="submit" class="btn btn-default text-center" onclick="this.form.keyword.focus();"><img src="images/menu/ico-search.png" alt=""></button>
		<input type="text" class="form-control search"  name="keyword" placeholder="Поиск..">
		<input type="hidden" name="view" value="category">
		<input type="hidden" name="option" value="com_virtuemart">
		<input type="hidden" name="virtuemart_category_id" value="0">
		<input type="hidden" name="Itemid" value="117">
	</div>
</form> 
<?php // } DST ?>	                                                                              
                                        
</div>
<!--//Верхнее меню---------------------------------------------------------------------------->

<!--Выезжающее подменю Десктоп----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
		
<div id="topMenuPanel" class="collapse hidden-xs">
<div class="container block-size">

<!----->      
<ul class="hidden-md hidden-lg">                            
        <li><a href="/news?start=0">Новости</a></li>
        <li><a href="/roznichnaya-set">Доставка</a></li>
        <li><a href="/contacts">О компании</a></li>
        <li><a href="/component/content/category/12-tekhnicheskaya-informatsiya?Itemid=0">Техническая информация</a></li>
        <li><a href="/sdat-staryj-akkumulyator">Сдать аккумулятор</a></li>
        <li><a href="/roznichnaya-set">Розничная сеть</a></li>
        <li><a href="/contacts">Контакты</a></li>
		<li><a href="/warranty">Гарантия</a></li>
</ul>
<!--//--->   

<div id="catalog" class="row">
<div style="overflow: auto">
<div class="row menu-back">
<div class="col-sm-5">
<h3 class="invert-h3" ><a class="a-bh3" href="/catalog/akkumulators">Аккумуляторы</a> 

<a href="/sdat-staryj-akkumulyator" class="btn btn-primary center-block popup_button" data-wow-duration="1000ms" data-wow-delay="300ms"  style="float: right; background: #000000 !important; color: #FFFFFF; border: 0; padding: 10px; border-radius: 3px; text-transform: uppercase; margin-top:-50px; font-size:0.6rem;">Сдать старый аккумулятор</a>

</h3>
<div class="colorInvert col-sm-5 col-lg-5">
	<span class="h3-span-subheader" >По производителю</span>
	<hr class="span-line">
</div>	
<div class="colorInvert col-sm-7 col-lg-7">
	<span class="h3-span-subheader" >По назначению</span>
	<hr class="span-line">	
</div>
 
</div>
<div class="col-sm-4">

<h3 class="invert-h3" ><a class="a-bh3" href="/catalog/oils">Моторные масла</a></h3> 
<div class="colorInvert col-md-5 col-lg-4">
<span class="h3-span-subheader">класс / вязкость</span>
<hr class="span-line">
</div>	
<div class="colorInvert col-md-7 col-lg-8">
<span class="h3-span-subheader">По назначению</span>
<hr class="span-line">
</div>
</div>
<div class="col-sm-3">
<h3 class="invert-h3" ><a class="a-bh3" href="/catalog/zaryadnye-ustrojstva">Аксессуары</a></h3> 
<div class="colorInvert col-sm-12">
<span class="h3-span-subheader">Для техники</span>
<hr class="span-line">	
</div>
</div>
</div>
	
<div class="col-sm-5">

<ul style="margin: 0px !important; padding: 0 ">


<div id="akb" class="">
<ul class="colorInvert col-sm-5 col-lg-5" style="list-style: none; padding: 15px 0 10px 0;">

<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/akom">Akom</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/banner">BANNER</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/varta">VARTA</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/aktex">AKTEX</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/akku">Akku</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/atlant">ATLANT</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/blizzaro">BLIZZARO</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/blt">BLT</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/centra">CENTRA</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/champion-pilot">Champion Pilot</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/start-bat">CтартБат</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/delta">Delta</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/exide">EXIDE</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="catalog/kainar">KAINAR</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/kijo">Kijo</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/monbat">MONBAT</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/optima">Optima</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/red-energy">Red Energy</a></li>	
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/reo-ni-mh">REO NI-MH</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/sparta">SPARTA</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/startcraft">Startcraft</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/tenax">TENAX</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/vega">VEGA</a></li>	
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/winmaxx">WINMAXX</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/akkumulators" style="padding:2px 0px; border: 0; color: #E9522C !important; border-radius: 2px; background: #fff; font-size:1rem; text-transform: none; padding: 2px;">Показать все </a></li>


<br style="clear:both;">
<br>
</ul>
      
                
<ul class="colorInvert col-sm-7 col-lg-7" style="list-style: none; padding-left: 0; padding-top: 15px;">  
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313530">Аккумуляторы для легковых автомобилей</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[1]=313536">Аккумуляторы для азиатских авто</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313439">Аккумуляторы для мотоциклов, квадроциклов, скутеров</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313534">Аккумуляторы для грузовых автомобилей и спецтехники</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313531">Аккумуляторы для микроавтобусов и фургонов</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313537">Промышленные аккумуляторы</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313535">Аккумуляторы для Start Stop (AGM)</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313532">АКБ для поломоечных машин, погрузчиков</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313533">Аккумуляторы для лодок, автодомов</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[1]=313636">ВВБ для гибридный автомобилей </a></li>

<?php /*
<a data-toggle="collapse" data-target="#toggleAkbType" style="padding:2px 0px; border: 0; color: #E9522C; border-radius: 2px; background: #fff; font-size:1rem; text-transform: none; padding: 2px;">Показать все <span>›</span></a>

<div id="toggleAkbType" class="collapse">
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313532">АКБ для поломоечных машин, погрузчиков</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[0]=313533">Аккумуляторы для лодок, автодомов</a></li>
<li><a href="/catalog/filter/akkumulators/?custom_f_321[1]=313636">ВВБ для гибридный автомобилей </a></li>
</div>
*/ ?>

<br style="clear:both;">
<br>

</ul>

</div>

</ul>	
</div> 

<div class="col-sm-4">
<ul style="margin-top: 0px; padding: 0">

<div id="maslo" class="">
<ul class="colorInvert col-md-5 col-lg-4" style="list-style: none; padding-left: 0; padding-top: 15px;">

<li><a href="/catalog/filter/oils/?custom_f_320[0]=30572d3330">0W-30</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=35572d3330">5W-30</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=35572d3430">5W-40</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3130572d3330">10W-30</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3130572d3430">10W-40</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3735572d3830">75W-80</a></li>
<a data-toggle="collapse" data-target="#toggleOilType" style="padding:2px 0px; border: 0; color: #000; border-radius: 1.8px; background: #fff; font-size:1rem; text-transform: none; padding: 2px;">Показать все <span>›</span></a>

<div id="toggleOilType" class="collapse">
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3130572d3530">10W-50</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3135572d3430">15W-40</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3735572d3930">75W-90</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3830572d3930">80W-90</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=3830572d313430">80W-140</a></li>
<li><a href="/catalog/filter/oils/?custom_f_320[0]=534145203330">SAE 30</a></li>
<br style="clear:both;">
<br>

</ul>

<ul class="colorInvert col-md-7 col-lg-8" style="list-style: none; padding-left: 0; padding-top: 15px;">

<li><a href="/catalog/filter/oils/?custom_f_25[0]=d0b4d0bbd18f20d0bbd0b5d0b3d0bad0bed0b2d18bd18520d0b0d0b2d182d0bed0bcd0bed0b1d0b8d0bbd0b5d0b9">Моторные масла для легковых автомобилей</a></li>
<li><a href="/catalog/filter/oils/?custom_f_25[0]=d0b4d0bbd18f20d0b3d180d183d0b7d0bed0b2d18bd18520d0b0d0b2d182d0bed0bcd0bed0b1d0b8d0bbd0b5d0b9">Моторные масла для грузовых автомобилей</a></li>
<li><a href="/catalog/filter/oils/?custom_f_25[0]=d0b4d0bbd18f20d0bcd0bed182d0be20d0b820d181d0b0d0b4d0bed0b2d0bed0b920d182d0b5d185d0bdd0b8d0bad0b8">Масла для мото и садовой техники</a></li>
<li><a href="/catalog/filter/oils/?custom_f_25[0]=d090d0bdd182d0b8d184d180d0b8d0b7d18b20d0b820d182d0b5d185d0bdd0b8d187d0b5d181d0bad0b8d0b520d0b6d0b8d0b4d0bad0bed181d182d0b8">Антифризы и технические жидкости</a></li>
<li><a href="/catalog/filter/oils/?custom_f_25[0]=d093d0b8d0b4d180d0b0d0b2d0bbd0b8d187d0b5d181d0bad0b8d0b520d0bcd0b0d181d0bbd0b0">Гидравлические масла</a></li>
<li><a href="/catalog/filter/oils/?custom_f_25[0]=d0a2d180d0b0d0bdd181d0bcd0b8d181d181d0b8d0bed0bdd0bdd18bd0b520d0bcd0b0d181d0bbd0b0">Трансмиссионные масла</a></li>
<li><a href="/catalog/oils/q8/smazki">Смазки</a></li>
<li class="col-md-12 col-lg-12 p-0"><a href="/catalog/oils" style="padding:2px 0px; border: 0; color: #E9522C !important; border-radius: 2px; background: #fff; font-size:1rem; text-transform: none; padding: 2px;">Показать все </a></li>
<?php /*
	<a data-toggle="collapse" data-target="#toggleOil" style="padding:2px 0px; border: 0; color: #E9522C; border-radius: 2px; background: #fff; font-size:1rem; text-transform: none; padding: 2px;">Показать все <span>›</span></a>

<div id="toggleOil" class="collapse">
<li><a href="/catalog/gnv/">GNV</a></li>
<!--
<li><a href="catalog.html">Масла для мото и садовой техники</a></li>
<li><a href="catalog.html">Моторные масла для грузовых автомобилей</a></li>
<li><a href="catalog.html">Моторные масла для легковых автомобилей</a></li>
<li><a href="catalog.html">Трансмиссионные масла, смазки, технические жидкости</a></li>
-->
</div>
*/ ?>

<br style="clear:both;">
<br>

</ul>
</div>

</ul>		
</div>

<div class="col-sm-3">

<div id="acsess">
<ul class="colorInvert col-sm-12" style="list-style: none; padding-left: 0; padding-top: 15px;">
<li><a href="/catalog/zaryadnye-ustrojstva">Зарядные и пуско-зарядные устройства</a></li>
<li><a href="/catalog/akb-testers">Тестеры АКБ</a></li>
<li><a href="/catalog/akb-aksessuary">Аксессуары АКБ</a></li>
<!--<li><a href="catalog.html">Клеммы</a></li>-->
<li><a href="/catalog/lamps">Автолампы</a></li>
<li><a href="/catalog/schetki-stekloochistitelya">Щетки стеклоочистителя</a></li>
<br style="clear:both;">
<br>

</ul>	
</div>
</div> 

<!----->      
<ul class="hidden-md hidden-lg">                            
                 
</ul>
<!--//--->     
  
</div>

</div>
</div>
</div>
<!--//Выезжающее подменю Десктоп----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->   
       
 <!--Выезжающее подменю Мобильный----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->
		
<div id="topMenuMobile" class="collapse bg-red">
<div class="container">

<!----->      
<ul class="mobileMenu">

<!--Catalog Mobile------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------> 
<li data-toggle="collapse" data-target="#catalogMobile" class="catalog-Mobile-Button"><a>Каталог</a></li> 
<!------------------------------------>

<div id="catalogMobile" class="collapse in" aria-expanded="true" style="">
<ul style="margin: 0px !important; padding: 0; border-bottom: 1px solid #EF655B;">
<a href="/catalog/akkumulators/" style="float:left;color: #FFFFFF;font-weight: bolder;text-transform: uppercase;font-size:1.0rem;margin: 20px 0;padding-left:15px;padding-right:15px;">Аккумуляторы 
</a>

<a href="/sdat-staryj-akkumulyator" class="btn center-block popup_button" data-wow-duration="1000ms" data-wow-delay="300ms" data-toggle="modal" data-target="#ModalTel" rel="order_call_window" style="float: right; background: #000000 !important; color: #FFFFFF; border: 0; padding: 10px; border-radius: 3px; text-transform: uppercase; margin-top: 10px; font-size:0.6rem;">Сдать аккумулятор</a>

<br style="clear: both">

<!------------------------------------>


</ul>	

<!------------------------------------->

<ul style="margin: 0px !important; padding: 0; border-bottom: 1px solid #EF655B;">
<h3 style="color: #FFFFFF;font-weight: bolder;text-transform: uppercase;/* font-size: 0; *//* margin-bottom: 8px; */padding-left:15px;padding-right:15px;margin: 14px 0;">
<a href="/catalog/oils" style="color: #FFFFFF; text-transform: uppercase; font-size:1.0rem; padding-right:15px;">Моторные масла</a>
</h3>   
</ul>


<!------------------------------------->

<ul style="margin: 0px !important; padding: 0; border-bottom: 1px solid #EF655B;">
<h3 style="color: #FFFFFF;font-weight: bolder;text-transform: uppercase;font-size:1.8rem;/* margin-bottom: 20px; */padding-left:15px;padding-right:15px;margin: 20px 0;">
<a href="/catalog/zaryadnye-ustrojstva" style="color: #FFFFFF;text-transform: uppercase;font-size:1.0rem;padding-right:15px;">Зарядные и пуско-зарядные устройства</a>
</h3>   
</ul>

</div>

<!--//Catalog Mobile------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------> 
                            
<li><a href="/component/content/category/10-news?Itemid=0">Новости</a></li>
<li><a href="/roznichnaya-set">Доставка</a></li>
<li><a href="/contacts">О компании</a></li>
<li><a href="/component/content/category/12-tekhnicheskaya-informatsiya?Itemid=0">Техническая информация</a></li>
<li><a href="/sdat-staryj-akkumulyator">Сдать аккумулятор</a></li>
<li><a href="/roznichnaya-set">Розничная сеть</a></li>
<li><a href="/contacts">Контакты</a></li>
<li><a href="/warranty">Гарантия</a></li>
</ul>
<!--//--->

<!--//---> 


</div>
</div>
<!--//Выезжающее подменю Мобильный----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------->    
          
     
</div>

</header>
												
						
<!--Лого + контактная группа ------------------------------------------------------------------->
<section id="topInfo" class="topInfo hidden-xs">
<div class="container block-size">
<div class="row">


<div class="col-xs-12 col-sm-6 col-md-4 topLogo">
<a href="/"><img src="images/logo/logo.png" alt="БатАвтотрейд"/>
<p>Аккумуляторы, масла Q8 от официального поставщика по выгодным ценам</p>
</a>
</div>

<div class="col-xs-12 col-sm-6 col-md-2 topTel">
<p><i class="fas fa-phone-square"></i>
<span>Интернет магазин<br></span>
<a href="tel:+375293514747">+375 29 351 47 47</a><br>
<a href="tel:+375333514747">+375 33 351 47 47</a></p>
</div>

<div class="col-xs-12 col-sm-6 col-md-2 topTel">
<p><i class="fas fa-phone-square"></i><span>
Оптовым покупателям<br></span>
<a href="tel:+375175106060">+375 17 510 60 60</a><br>
<a href="tel:+375293106010">+375 29 310-60-10</a></p>
</div>

<div class="col-xs-12 col-sm-6 col-md-4 hidden-sm topCard text-right">
<a href="/optovye-pokupateli" class="user" ><i class="fas fa-user-circle"></i> Личный кабинет</a>
<a href="/catalog/cart" class="cart" ><i class="fas fa-shopping-cart"></i> Корзина 
	<span class="total_products_from_cart">0</span></a>
<?php
$user = JFactory::getUser();
if ($user->id>0){ 	
$username = $user->get( 'name' );
 
/* {/DST manager  */
$userid = $user->get( 'id' );
$db = JFactory::getDBO();
$query = 'SELECT `manager` ' . ' FROM `#__virtuemart_userinfos` ' . ' WHERE `virtuemart_user_id` = ' . $userid;
$db->setQuery( $query );
$usermanager = $db->LoadResult();
/* }/DST manager  */  

}  	
?>
<?php if ($user->id>0): ?>
</br>
<a href="/optovye-pokupateli" class="user">Вы зашли <i class="fas fa-user-circle"></i> как: <?php echo $username; ?> </br> Менеджер: <?php echo $usermanager ?></a>
<?php endif; ?>
</div>

</div>
</div>
</section>	
<!--//Лого + контактная группа ------------------------------------------------------------------->
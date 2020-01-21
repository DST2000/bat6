<section id="news" class="bg-grey pb-2">
<div class="container block-size">
<div class="row">
<div class="col-sm-8 col-md-12">         

<!--------->
<div class="row">
<div class="blockBorder inc-m-15">
<div class="col-md-3 py-4 text-center">
<a href="/news?start=0" class="title">Наши новости</a><br>
<a href="/news?start=0" class="button">Перейти</a>
</div>

<?php	
$db = JFactory::getDbo();
$catid = "10";
$query = $db->getQuery(true);
$query->select('introtext, alias, created, title, id, images')
 ->from('#__content')
 ->where('catid = '.(int)$catid.' AND state = "1"')
 ->order('created DESC');
$db->setQuery($query);
	
$rows = $db->loadObjectList();

$items = 3;	
foreach ( $rows as $row ) {
	if ($items > 0) {
	?>
	
	<div class="col-md-3 py-4">
	<?php
		$link = JRoute::_('news/'.$row->id.'-'.$row->alias);
		echo '<a href="' . $link . '">';
		$image = json_decode($row->images);
		$imagelink = $image->{'image_intro'};
?>
<div class="bannerImg" style="background: url(<?php echo $imagelink;?>) top left / cover no-repeat;">
</div>
		
<strong><?php echo substr($row->title, 0, 250); ?></strong><br>
	
<p><?php
		//<img
		$beforeimg = strpos( $row->introtext , '<img' );

		if ($beforeimg > 0) {
			$firstpart = substr($row->introtext, 0, (int)$beforeimg);	
			$secondpart = substr($row->introtext, (int)$beforeimg);
			$afterimg = strpos( $secondpart , '>' ) + 1;
			$secondpart = substr($secondpart, (int)$afterimg);
			$resulText = $firstpart . $secondpart;  
			echo substr($resulText , 0, 500);
		}
		elseif ($beforeimg = 'FALSE') {
			echo substr($row->introtext, 0, 500);
		} 	
	
	?>
</p>
</a>
</div>
<?
		$items--;
	}
	elseif ($items < 0)  {
		break;
	}
	 
}
?>
	
<!--

<div class="col-md-3 py-4">
<a href="">
<div class="bannerImg"
style="background: url(images/news/n2.jpg) top left / contain no-repeat;">
</div>
<strong>Первая партия аккумуляторов KIJO для ИБП уже на складе</strong><br>
<p>К нам на склад поступила партия ИБП KIJO.<br>
KIJO GROUP, основанная в 1993 году, является предприятием, собравшую в себе науку, промышленность и торговлю. С годовой производственной мощностью 30 миллионов единиц.
</p>
</a>
</div>-->



</div>
<!--------->
</div>
</div>
</div> 
</div>
</section>
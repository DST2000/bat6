<div id="menu-search" class="col-md-2">
<form action="/catalog" method="get">
	<div class="form-group">	
		<!-- <input type="search" class="form-control" name="search" id="search" placeholder="Найти..."> -->
		
		<input name="keyword" class="inputbox form-control" type="text" size="25" value="" placeholder="Вопрос">
		<i class="fas fa-search"></i>
		<input type="submit" value="Искать" class="button push" onclick="this.form.keyword.focus();">
	<!-- input type="hidden" name="showsearch" value="true"/ -->
	<input type="hidden" name="view" value="category">
	<input type="hidden" name="option" value="com_virtuemart">
	<input type="hidden" name="virtuemart_category_id" value="0">
	<input type="hidden" name="Itemid" value="117">
	</div>
</form>
</div>


	<style>
		input.push {
			width: 58px;
			height: 34px;
			border: none;
			font-size: 1.2em;
			top: 0px;
			left: 122px;
			background-color: #B8DADA;
			color: #82281E;
			position: absolute;
		}
	#menu-search .fa-search {
		position: absolute;
		color: #fff;
		padding: 2px;
		font-size: 35px;
		margin: -35px 0 0 -35px;
	}
	</style>
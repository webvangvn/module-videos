<!-- BEGIN: main -->
<div class="row">
	<!-- BEGIN: loop -->
	<div class="col-lg-24 col-md-24 col-sm-24 col-xs-24">
		<!-- BEGIN: img -->
		<div class="row">
			<a href="{ROW.link}" title="{ROW.title}"><img src="{ROW.thumb}" alt="{ROW.title}" class="img-thumbnail center-block"/></a>
		</div>
		<!-- END: img -->
		<a {TITLE} class="show" href="{ROW.link}" <!-- BEGIN: tooltip -->data-placement="{TOOLTIP_POSITION}" data-content="{ROW.hometext}" data-img="{ROW.thumb}" data-rel="tooltip"<!-- END: tooltip -->><h3>{ROW.title}</h3></a>
	</div>
	<!-- END: loop -->
</div>
<!-- END: main -->
<!-- BEGIN: main -->
<ul>
	<!-- BEGIN: loop -->
	<li class="clearfix">
		<!-- BEGIN: img -->
		<a href="{ROW.link}" title="{ROW.title}"><img src="{ROW.thumb}" alt="{ROW.title}" width="{ROW.blockwidth}" class="img-thumbnail pull-left"/></a>
		<!-- END: img -->
		<a {TITLE} class="show" href="{ROW.link}" <!-- BEGIN: tooltip -->data-placement="{TOOLTIP_POSITION}" data-content="{ROW.hometext}" data-img="{ROW.thumb}" data-rel="tooltip"<!-- END: tooltip -->>{ROW.title}</a>
	</li>
	<!-- END: loop -->
</ul>
<!-- END: main -->
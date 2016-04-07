<!-- BEGIN: main -->
<ul class="nav nav-tabs">
<!-- BEGIN: group_info -->
  <li class="{BLOCK_INFO.active}"><a data-toggle="tab" href="#block-news-groups-{BLOCK_INFO.bid}">{BLOCK_INFO.title}</a></li>
<!-- END: group_info -->
</ul>
<div class="tab-content">
<!-- BEGIN: group_content -->
	<ul id="block-news-groups-{BLOCK_INFO.bid}" class="tab-pane fade {BLOCK_INFO.active} in list-group">
		<!-- BEGIN: loop -->
		<li class="list-group-item">
			<!-- BEGIN: img -->
			<a href="{ROW.link}" title="{ROW.title}"><img src="{ROW.thumb}" alt="{ROW.title}" width="{ROW.blockwidth}" class="img-thumbnail"/></a>
			<!-- END: img -->
			<a href="{ROW.link}" title="{ROW.title}">{ROW.title}</a>
		</li>
		<!-- END: loop -->
		<!-- BEGIN: no_loop -->
			<div class="row">No Videos here</div>		
		<!-- END: no_loop -->
	</ul>
<!-- END: group_content -->
</div>
<!-- END: main -->
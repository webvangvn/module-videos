<!-- BEGIN: main -->
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#block-news-groups">{BLOCK_NAME}</a></li>
  <li><a data-toggle="tab" href="#latest-news">Videos mới nhất</a></li>
</ul>

<div class="tab-content">
	<!-- BEGIN: group -->
	<ul id="block-news-groups" class="tab-pane fade in active list-group">
		<!-- BEGIN: loop -->
		<li class="list-group-item">
			<!-- BEGIN: img -->
			<a href="{ROW.link}" title="{ROW.title}"><img src="{ROW.thumb}" alt="{ROW.title}" width="100" class="img-thumbnail"/></a>
			<!-- END: img -->
			<a href="{ROW.link}" title="{ROW.title}">{ROW.title}</a>
		</li>
		<!-- END: loop -->
	</ul>
	<!-- END: group -->

	<!-- BEGIN: latest -->
	<ul id="latest-news" class="tab-pane fade list-group">
		<!-- BEGIN: loop -->
		<li class="list-group-item">
			<!-- BEGIN: img -->
			<a href="{NEWS.link}" title="{NEWS.title}"><img src="{NEWS.thumb}" alt="{NEWS.title}" width="100" class="img-thumbnail"/></a>
			<!-- END: img -->
			<a href="{NEWS.link}" title="{NEWS.title}">{NEWS.title}</a>
		</li>
		<!-- END: loop -->
	</ul>
	<!-- END: latest -->
</div>
<!-- END: main -->
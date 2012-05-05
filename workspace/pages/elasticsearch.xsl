<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:import href="../utilities/pagination.xsl" />

<xsl:output method="xml"
	doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN"
	doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"
	omit-xml-declaration="yes"
	encoding="UTF-8"
	indent="yes" />
	
<xsl:variable name="keywords" select="normalize-space(/data/params/url-keywords)"/>
<xsl:variable name="sections" select="normalize-space(/data/params/url-sections)"/>

<xsl:template match="/">
	
	<html>
		
		<head>
			
			<link href="/workspace/assets/css/screen.css" rel="stylesheet" type="text/css" media="all" />

			<script type="text/javascript" src="/workspace/assets/js/jquery-1.5.1.min.js"></script>
			<script type="text/javascript" src="/workspace/assets/js/jquery-ui-1.8.14.custom.min.js"></script>
			<script type="text/javascript" src="/workspace/assets/js/jquery.ui.autocomplete.html.js"></script>

			<script type="text/javascript">
				$(document).ready(function() {
					$("input[name='keywords']").autocomplete({
						source: function(request, response) {
							$.ajax({
								url: "/elasticsearch/suggest/",
								dataType: "xml",
								data: { keywords: request.term },
								success: function(data) {
									response($.map($(data).find('word'), function(item) {
										var item = $(item);
										return {
											label: item.find('highlighted').text(),
											value: item.find('raw').text()
										}
									}));
								}
							})
						},
						open: function(event, ui) {
							$(this).addClass('auto-complete-open');
						},
						close: function(event, ui) {
							$(this).removeClass('auto-complete-open');
						},
						select: function(event, ui) {
							$(this).val(ui.item.value);
							$(this).parent().submit();
						},
						html: true,
						minLength: 2,
						delay: 200
					});
				});
			</script>
			
		</head>
	
	
	<body>
	
	<form method="get" action="">
		<input type="text" name="keywords" value="{$keywords}" />
		<button type="submit"><span>Search</span></button>
	</form>
	
	<div id="filters">
		<xsl:apply-templates select="/data/elasticsearch" mode="filters"/>
	</div>
	
	<div id="results">
		<xsl:apply-templates select="/data/elasticsearch" mode="results"/>
	</div>
	
	</body>
	
</html>
	
</xsl:template>

<xsl:template match="data/elasticsearch" mode="filters">

	<h2>Filters</h2>

	<ul class="filters">
		<li>
			<xsl:attribute name="class">
				<xsl:text>everything</xsl:text>
				<xsl:if test="$sections=''">
					<xsl:text> selected</xsl:text>
				</xsl:if>
			</xsl:attribute>
			<a href="/elasticsearch/?keywords={$keywords}">
				Everything
				<span>
					<xsl:value-of select="sum(facets/facet[@handle='filtered-sections']/term/@entries)"/>
				</span>
			</a>
		</li>
		<xsl:for-each select="facets/facet[@handle='all-sections']/term">
			<li>
				<xsl:attribute name="class">
					<xsl:value-of select="@handle"/>
					<xsl:if test="@active='yes' and $sections!=''">
						<xsl:text> selected</xsl:text>
					</xsl:if>
				</xsl:attribute>
				<a href="/elasticsearch/?keywords={$keywords}&amp;sections={@handle}">
					<xsl:value-of select="text()"/>
					<span>
						<xsl:variable name="count" select="../../facet[@handle='filtered-sections']/term[@handle=current()/@handle]/@entries"/>
						<xsl:choose>
							<xsl:when test="$count"><xsl:value-of select="$count"/></xsl:when>
							<xsl:otherwise>0</xsl:otherwise>
						</xsl:choose>
					</span>
				</a>
			</li>
		</xsl:for-each>
	</ul>
	
</xsl:template>

<xsl:template match="data/elasticsearch[keywords/@raw='']" mode="results">
	
	<h2>You did not search for anything.</h2>
	
</xsl:template>

<xsl:template match="data/elasticsearch" mode="results">
	
	<xsl:if test="not(entries/entry)">
		<xsl:attribute name="class">no-results</xsl:attribute>
	</xsl:if>
		
	<h2>
		<xsl:text>Your search for </xsl:text>
		<strong><xsl:value-of select="keywords/filtered"/></strong>
		<xsl:text> found </xsl:text>
		<xsl:value-of select="pagination/@total-entries" />
		<xsl:choose>
			<xsl:when test="pagination/@total-entries=1">
				<xsl:text> result</xsl:text>
			</xsl:when>
			<xsl:otherwise>
				<xsl:text> results</xsl:text>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="$sections!=''">
			<span>
				<xsl:text> in </xsl:text>
				<xsl:value-of select="sections/section[@active='yes']/text()"/>
			</span>
		</xsl:if>
	</h2>

	<xsl:choose>
		<xsl:when test="entries/entry">
			<xsl:apply-templates select="entries/entry"/>
		</xsl:when>
		<xsl:otherwise>
			<p>Please try the following:</p>
			<ul>
				<li>use alternative keyword(s) such as replacing "canine" for "dog"</li>
				<li>remove keywords if your search is too specific</li>
			</ul>
		</xsl:otherwise>
	</xsl:choose>
	
	<xsl:call-template name="pagination">
		<xsl:with-param name="pagination" select="pagination"/>
		<xsl:with-param name="pagination-url" select="concat('/elasticsearch/?keywords=', $keywords, '&amp;sections=', $sections, '&amp;page=$')"/>
		<xsl:with-param name="show-range" select="'9'"/>
		<xsl:with-param name="label-next" select="'Next'"/>
		<xsl:with-param name="label-previous" select="'Previous'"/>
	</xsl:call-template>
		
</xsl:template>

<xsl:template match="entry">
	
	<div class="{@section} result">
		<xsl:choose>
			<xsl:when test="@section='speeches'">
				<span class="type speech">Speech</span>
				<h3 class="title">
					<a href="#">
						<span>
							<xsl:call-template name="lowercase">
								<xsl:with-param name="string" select="speaker"/>
							</xsl:call-template>
						</span>
						<xsl:text>&#8217;s line</xsl:text>
					</a>
					<span>
						<xsl:text> from </xsl:text>
						<xsl:value-of select="play/item"/>
					</span>
				</h3>
				<p>
					<xsl:apply-templates select="." mode="excerpt" />
					<xsl:if test="not(highlight[@field='lines'])">
						<xsl:text>&#8230; </xsl:text>
						<xsl:call-template name="truncate">
							<xsl:with-param name="string" select="string(lines)"/>
							<xsl:with-param name="length" select="'140'"/>
						</xsl:call-template>
					</xsl:if>
				</p>
			</xsl:when>
			<xsl:when test="@section='plays'">
				<span class="type play">Play</span>
				<h3 class="title">
					<a href="#"><xsl:value-of select="title"/></a>
					<xsl:text> — Play</xsl:text>
				</h3>
			</xsl:when>
			<xsl:when test="@section='scenes'">
				<span class="type scene">Scene</span>
				<h3 class="title">
					<a href="#"><xsl:value-of select="title"/></a>
					<span>
						<xsl:text> from </xsl:text>
						<xsl:value-of select="play/item"/>
					</span>
				</h3>
			</xsl:when>
			<xsl:when test="@section='acts'">
				<span class="type act">Act</span>
				<h3 class="title">
					<a href="#"><xsl:value-of select="title"/></a>
					<xsl:text> from </xsl:text>
					<xsl:value-of select="play/item"/>
				</h3>
			</xsl:when>
			<xsl:otherwise>
				<p class="title"><xsl:value-of select="title"/></p>
			</xsl:otherwise>
		</xsl:choose>
		<a href="#" class="url"><xsl:value-of select="$root"/></a>
	</div>
	
</xsl:template>

<xsl:template match="entry" mode="excerpt">
	<xsl:for-each select="highlight">
		<xsl:copy-of select="*|text()"/>
		<xsl:if test="position() != last()">&#8230; </xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="lowercase">
	<xsl:param name="string"/>
	<xsl:value-of select="translate($string,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/>
</xsl:template>

<xsl:template name="truncate">
	<xsl:param name="string"/>
	<xsl:param name="length"/>
	<xsl:choose>
		<xsl:when test="string-length($string) &lt;= $length">
			<xsl:value-of select="$string"/>
		</xsl:when>
		<xsl:otherwise>
			<xsl:value-of select="concat(substring($string, 0, $length), '&#8230;')"/>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
// <---- Custom Additions end here.
// Photonic additions begin ----->

$j = jQuery.noConflict();
$j(document).ready(function() {
	var tabs = $j("#photonic-options").tabs({
		fx: {
			opacity: "toggle",
			duration: "fast"
		}
	});
	tabs.tabs('option', 'active', '#' + Photonic_Admin_JS.category);

	$j('.photonic-border-options input[type="text"], .photonic-border-options select').change(function(event) {
		var thisId = event.currentTarget.id;
		thisId = thisId.substring(0, thisId.indexOf('-'));
		var edges = new Array('top', 'right', 'bottom', 'left');
		var border = '';
		for (var x in edges) {
			var edge = edges[x];
			var thisName = thisId + '-' + edge;
			border += edge + '::';
			border += 'color=' + $j("#" + thisName + "-color").val() + ';' +
					'colortype=' + $j("input[name=" + thisName + "-colortype]:checked").val() + ';' +
					'style=' + $j("#" + thisName + "-style").val() + ';' +
					'border-width=' + $j("#" + thisName + "-border-width").val() + ';' +
					'border-width-type=' + $j("#" + thisName + "-border-width-type").val() + ';';
			border += '||';
		}
		$j('#' + thisId).val(border);
	});

	$j('.photonic-border-options input[type="radio"]').change(function(event) {
		var thisId = event.currentTarget.name;
		thisId = thisId.substring(0, thisId.indexOf('-'));
		var edges = new Array('top', 'right', 'bottom', 'left');
		var border = '';
		for (var x in edges) {
			var edge = edges[x];
			var thisName = thisId + '-' + edge;
			border += edge + '::';
			border += 'color=' + $j("#" + thisName + "-color").val() + ';' +
					'colortype=' + $j("input[name=" + thisName + "-colortype]:checked").val() + ';' +
					'style=' + $j("#" + thisName + "-style").val() + ';' +
					'border-width=' + $j("#" + thisName + "-border-width").val() + ';' +
					'border-width-type=' + $j("#" + thisName + "-border-width-type").val() + ';';
			border += '||';
		}
		$j('#' + thisId).val(border);
	});

	$j('.photonic-background-options input[type="text"], .photonic-background-options select').change(function(event) {
		var thisName = event.currentTarget.id;
		thisName = thisName.substring(0, thisName.indexOf('-'));
		$j("#" + thisName).val('color=' + $j("#" + thisName + "-bgcolor").val() + ';' +
			'colortype=' + $j("input[name=" + thisName + "-colortype]:checked").val() + ';' +
			'image=' + $j("#" + thisName + "-bgimg").val() + ';' +
			'position=' + $j("#" + thisName + "-position").val() + ';' +
			'repeat=' + $j("#" + thisName + "-repeat").val() + ';' +
			'trans=' + $j("#" + thisName + "-trans").val() + ';'
		);
	});

	$j('.photonic-background-options input[type="radio"]').change(function(event) {
		var thisName = event.currentTarget.name;
		thisName = thisName.substring(0, thisName.indexOf('-'));
		$j("#" + thisName).val('color=' + $j("#" + thisName + "-bgcolor").val() + ';' +
			'colortype=' + $j("input[name=" + thisName + "-colortype]:checked").val() + ';' +
			'image=' + $j("#" + thisName + "-bgimg").val() + ';' +
			'position=' + $j("#" + thisName + "-position").val() + ';' +
			'repeat=' + $j("#" + thisName + "-repeat").val() + ';' +
			'trans=' + $j("#" + thisName + "-trans").val() + ';'
		);
	});

	$j('.photonic-padding-options input[type="text"], .photonic-padding-options select').change(function(event) {
		var thisId = event.currentTarget.id;
		thisId = thisId.substring(0, thisId.indexOf('-'));
		var edges = new Array('top', 'right', 'bottom', 'left');
		var padding = '';
		for (var x in edges) {
			var edge = edges[x];
			var thisName = thisId + '-' + edge;
			padding += edge + '::';
			padding += 'padding=' + $j("#" + thisName + "-padding").val() + ';' +
					'padding-type=' + $j("#" + thisName + "-padding-type").val() + ';';
			padding += '||';
		}
		$j('#' + thisId).val(padding);
	});

	$j('.photonic-button-bar').draggable();

	$j('.photonic-button-toggler a').on('click', function() {
		var thisClass = this.className;
		thisClass = thisClass.substr(24);
		var dialogClass = '.photonic-button-bar-' + thisClass;
		$j(dialogClass).slideToggle();
		return false;
	});

	$j('.photonic-button-bar a').click(function() {
		var thisParent = $j(this).parent().parent();
		thisParent.slideToggle();
		return false;
	});

	$j("#photonic-options h3").each(function() {
		var text = $j(this).text();
		if (text == '') {
			$j(this).remove();
		}
	});

	$j('.suffusion-options-form').submit(function(event) {
		var field = suffusion_submit_button;
		var value = field.val();

		if (value.substring(0, 5) == 'Reset') {
			if (!confirm("This will reset your configurations to the original values!!! Are you sure you want to continue? This is not reversible!")) {
				return false;
			}
		}
		else if (value.substring(0, 6) == 'Delete') {
			if (!confirm("This will delete all your Photonic configuration options!!! Are you sure you want to continue? This is not reversible!")) {
				return false;
			}
		}
	});
});
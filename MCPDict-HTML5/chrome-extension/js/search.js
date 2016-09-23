function _tabs_(part) {
	var tab = ['dictionary', 'setting'];
	for (var i = tab.length - 1; i >= 0; i--) {
		$('#'+tab[i]).attr('class', '');
		$('#'+tab[i]+'-wrapper').css('display', 'none');
	}
	$('#'+part).attr('class', 'active');
	$('#'+part+'-wrapper').css('display', 'block');
}

function _set_display_(id, lang) {
	$("#dropdown-"+lang).val(id);
	$("#dropdown-"+lang).html($('#'+lang+'-'+id).html()+' <span class="caret"></span>');
}

function _query_(s0, s1) {
	var string = $.trim($("#_search_").val());
	if (string == "") { // query empty, return empty
		$("#results").html("");
		$("#_search_").val("");
		return;
	}
	var mode = '0';
	var flag = [s0, s1, '0'];
	var setting = [$("#dropdown-pu").val(), 
					$("#dropdown-ct").val(), 
					$("#dropdown-kr").val(), 
					$("#dropdown-vn").val(), 
					$("#dropdown-jp").val()];
	// console.log("search: ["+string+"] mode="+mode+", flag="+flag+", setting="+setting+ "...");

	// TODO: query the database
	$.post("http://www.phonicavi.com/dictionary/MCPDict/php/query.php", 
		{
			string: string,
			mode: mode,
			flag: flag,
			setting: setting
		}, 
		function(data, status) { // status: "success", "notmodified", "error", "timeout", or "parsererror"
			// TODO: append results, or show no results
			if (status == "success") {
				if (data.status == "success") {
					$("#results").html(data.HTML);
				} else {
					$("#results").html("未找到符合條件的漢字。");
				}
			} else if (status == "timeout") {
				alert("Timeout...");
			} else if (status == "error") {
				alert("Error...");
			} else if (status == "notmodified") {
				alert("Notmodified...");
			} else if (status == "parsererror") {
				alert("Parsererror...");
			}
		}, 
	"json");
}


document.addEventListener('DOMContentLoaded', function() {
  document.getElementById('dictionary').addEventListener('click', function () { _tabs_('dictionary');});
  document.getElementById('setting').addEventListener('click', function () { _tabs_('setting');});

  document.getElementById('pu-0').addEventListener('click', function () { _set_display_(0, 'pu');});
  document.getElementById('pu-1').addEventListener('click', function () { _set_display_(1, 'pu');});
  document.getElementById('ct-0').addEventListener('click', function () { _set_display_(0, 'ct');});
  document.getElementById('ct-1').addEventListener('click', function () { _set_display_(1, 'ct');});
  document.getElementById('ct-2').addEventListener('click', function () { _set_display_(2, 'ct');});
  document.getElementById('ct-3').addEventListener('click', function () { _set_display_(3, 'ct');});
  document.getElementById('kr-0').addEventListener('click', function () { _set_display_(0, 'kr');});
  document.getElementById('kr-1').addEventListener('click', function () { _set_display_(1, 'kr');});
  document.getElementById('vn-0').addEventListener('click', function () { _set_display_(0, 'vn');});
  document.getElementById('vn-1').addEventListener('click', function () { _set_display_(1, 'vn');});
  document.getElementById('jp-0').addEventListener('click', function () { _set_display_(0, 'jp');});
  document.getElementById('jp-1').addEventListener('click', function () { _set_display_(1, 'jp');});
  document.getElementById('jp-2').addEventListener('click', function () { _set_display_(2, 'jp');});
  document.getElementById('jp-3').addEventListener('click', function () { _set_display_(3, 'jp');});

  document.getElementById('search-button').addEventListener('click', function () {
  	var s0 = document.getElementById('switch-0').checked ? '1' : '0';
  	var s1 = document.getElementById('switch-1').checked ? '1' : '0';
  	_query_(s0, s1);});
});


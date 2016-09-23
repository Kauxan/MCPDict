var version = 1.0;
var disabled_table = [
	new Array(0, 0, 1),
	new Array(1, 1, 0),
	new Array(0, 1, 0),
	new Array(0, 1, 0),
	new Array(0, 1, 0),
	new Array(0, 1, 0),
	new Array(0, 1, 1),
	new Array(0, 1, 0),
	new Array(0, 1, 1),
	new Array(0, 1, 1),
	new Array(0, 1, 1)
];

function tabs(part) {
	var tab = ['dictionary', 'notepad', 'setting', 'instruction'];
	for (var i = tab.length - 1; i >= 0; i--) {
		$('#'+tab[i]).attr('class', '');
		$('#'+tab[i]+'-wrapper').css('display', 'none');
	}
	$('#'+part).attr('class', 'active');
	$('#'+part+'-wrapper').css('display', 'block');
}

// var data_object;

function switch_init() {
	// $.fn.bootstrapSwitch.defaults.size = "normal";
	$.fn.bootstrapSwitch.defaults.size = "small";
	$.fn.bootstrapSwitch.defaults.onColor = "success";
	$.fn.bootstrapSwitch.defaults.onText = "開";
	$.fn.bootstrapSwitch.defaults.offText = "關";
	// initialization
	$("#switch-0").bootstrapSwitch('state', false);
	$("#switch-1").bootstrapSwitch('state', true);
	$("#switch-2").bootstrapSwitch('state', false);
	$("#switch-2").bootstrapSwitch('disabled', true);
}

function change_mode(id) {
	$("#dropdown-selector").val(id);
	$("#dropdown-selector").html($('#selector-'+id).html()+' <span class="caret"></span>');
	for (var i = 0; i < 3; i++) {
		$("#switch-"+i).bootstrapSwitch('disabled', disabled_table[id][i]);
	}
}

function set_display(id, lang) {
	$("#dropdown-"+lang).val(id);
	$("#dropdown-"+lang).html($('#'+lang+'-'+id).html()+' <span class="caret"></span>');
}

function query() {
	var string = $.trim($("#_search_").val());
	if (string == "") { // query empty, return empty
		$("#results").html("");
		$("#_search_").val("");
		return;
	}
	var mode = $("#dropdown-selector").val();
	var flag = new Array();
	for (var i = 2; i >= 0; i--) {
		flag[i] = ~disabled_table[mode][i] & $("#switch-"+i).bootstrapSwitch('state');
	}
	var setting = [$("#dropdown-pu").val(), 
					$("#dropdown-ct").val(), 
					$("#dropdown-kr").val(), 
					$("#dropdown-vn").val(), 
					$("#dropdown-jp").val()];
	
	// console.log("search: ["+string+"] mode="+mode+", flag="+flag+", setting="+setting+ "...");
	// use: console.log("search: ["+string+"] mode="+mode+", flag="+flag+", setting="+setting+ "...");

	// TODO: query the database
	$.post("php/query.php", 
		{
			string: string,
			mode: mode,
			flag: flag,
			setting: setting
		}, 
		function(data, status) { // status: "success", "notmodified", "error", "timeout", or "parsererror"
			// TODO: append results, or show no results
			// alert("Data: " + data + "\nStatus: " + status);
			if (status == "success") {
				// data_object = data;
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
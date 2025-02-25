function logout() {
	var page = "../rebuilding/rebuilding_action.php";
	var params = "_action=unset_user_front";

	$.ajax({
		type: "GET",
		url: page,
		data: params,
		dataType: "html",
		success: function (result) {
			var result = result.split('|');
						
			window.location.href = result[1];   
	
		},
	});

}
function doTokenRemove(){	

	jQuery(document).ready(function($){	

		if($("#wc_2c2p_stored_card").val() === "0"){
			alert("Please select card number to delete.");
			return;
		}

		var ajaxurl  = $("#ajax_url").val();
		var tokenId 	 = {'token_id':  $("#wc_2c2p_stored_card").val()};
				
		jQuery.ajax({
                type: 'POST',
                data: { action: 'paymentajax', data : tokenId },
                url: ajaxurl,
                cache: false,
                success: function (data) { 
                	if(data === "0"){
                		alert("Error ! We cannot remove you stored card Id. Please try to referesh the page.");
                		return;
                	}

                	var isdeleted = $("#wc_2c2p_stored_card option[value="+ tokenId.token_id + "]").remove();
                	if($("#wc_2c2p_stored_card").find("option").length <= 1){
                		$("#tblToken").hide();
                	}

                	if(isdeleted.length === 0){
                		alert("Error ! We cannot remove you stored card Id from UI (dropdown). Please try to referesh the page.")
                	}
                	else{
                		alert("successfully remove you stored card Id.");	
                	}                	
                },
                error: function(data){                	
                	alert("error" + data);
                }
            });
	});
}
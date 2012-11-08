jQuery(document).ready( function() {
	// Put your JS in here, and it will run after the DOM has loaded.
	// jQuery.post( ajaxurl, {
	// 	action: 'my_example_action',
	// 	'cookie': encodeURIComponent(document.cookie),
	// 	'parameter_1': 'some_value'
	// }, 
	// function(response) { 
	// 	... 
	// } );

	jQuery('.like_album, .like_image').live('click', function() {
		
		var type = jQuery(this).attr('class');
		var id = jQuery(this).attr('id');
		
		jQuery(this).addClass('loading');
		
		jQuery.post( ajaxurl, {
			action: 'BPAAlbumLike',
			'cookie': encodeURIComponent(document.cookie),
			'type': type,
			'id': id
		},
		function(data) {
			
			jQuery('#' + id).fadeOut( 100, function() {
				jQuery(this).html(data).removeClass('loading').fadeIn(100);
			});
			
			// Swap from like to unlike
			if (type == 'like') {
				var newID = id.replace("like", "liked");
				jQuery('#' + id).removeClass('like_album').addClass('liked_album').attr('title', 'You like this item').attr('id', newID);
			}
						
		});
		
		return false;
	});
		jQuery('#selected_album').change(function() {
  	var albumID = jQuery('#selected_album').val();
  	BPAAlbumPrivacy(albumID);
	});
});

	function BPADeleteAlbum(theAlbumID, theAlbumTitle)
	{
		if(confirm("Are you sure you want to delete album "+theAlbumTitle+" and all it's contents"))
		{
				ShowLoadingScreen("Please wait while the album and all it's contents is deleted");
				jQuery.post(
									BPAAjax.ajaxurl,
									{
										action: 'BPADeleteAlbum',
										albumID: theAlbumID,
										BPADeleteAlbumNonce: BPAAjax.BPADeleteAlbum
									},
									function(response){
												if (response.indexOf('success') != -1)
												{
													BPAAjaxSuccess = true;
												}
												else
												{
													BPAAjaxSuccess = false;
												}
//										alert(response);
								})	
								.success(function() {
									if(BPAAjaxSuccess)
									{
										alert('Album "'+theAlbumTitle+'" Deleted Successfully ');
										window.location.reload(true);
									}
									else
									{
										alert('Unable to delete album "'+theAlbumTitle+'"');
									}	
					  		})
 								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								}).complete(function() {
 			 						HideLoadingScreen();
 								});

		}
	}
	function BPADeleteImage(theImageID)
	{
		if(confirm("Are you sure you want to delete this image"))
		{
				ShowLoadingScreen('Please wait while your image is deleted');
					jQuery.post(
									BPAAjax.ajaxurl,
									{
										action: 'BPADeleteImage',
										imageID: theImageID,
										BPADeleteImageNonce: BPAAjax.BPADeleteImage
									},
									function(response){
												if (response.indexOf('success') != -1)
												{
													BPAAjaxSuccess = true;
												}
												else
												{
													BPAAjaxSuccess = false;
												}
//										alert(response);
								})	
								.success(function() {
									if(BPAAjaxSuccess)
									{
										alert('Image Deleted Successfully ');
										window.location.reload(true);
									}
									else
									{
										alert('Unable to delete Image');
									}	
					  		})
 								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								}).complete(function() {
 			 						HideLoadingScreen();
 								});

		}
	}
	function BPAFeatureImage(theAlbumID,theAlbumTitle, theImageID)
	{
		if(confirm("Are you sure you want to make this image the feature image for album '"+theAlbumTitle+"'"))
		{
				ShowLoadingScreen('Please wait while the new feature image is set');
				jQuery.post(
									BPAAjax.ajaxurl,
									{
										action: 'BPAFeatureImage',
										albumID: theAlbumID,
										imageID: theImageID,
										BPAFeatureImageNonce: BPAAjax.BPAFeatureImage
									},
									function(response){
												if (response.indexOf('success') != -1)
												{
													BPAAjaxSuccess = true;
												}
												else
												{
													BPAAjaxSuccess = false;
												}
//										alert(response);
								})	
								.success(function() {
									if(BPAAjaxSuccess)
									{
										alert("Image Set Successfully As Feature Image for '"+theAlbumTitle+"'");
										window.location.reload(true);
									}
									else
									{
										alert('Unable To Set Feature Image');
									}	
					  		})
 								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								}).complete(function() {
 			 						HideLoadingScreen();
 								});

		}
	}
	function BPAAlbumPrivacy(theAlbumID)
	{
		jQuery.get(
									BPAAjax.ajaxurl,
									{
									action: 'BPAAlbumPrivacy',
										albumID: theAlbumID,
										BPAAlbumPrivacyNonce: BPAAjax.BPAAlbumPrivacy
										},
									function(response){
												if (response[0].result.indexOf('success') != -1)
												{
													jQuery('#priv_2').prop('checked',false);
													jQuery('#priv_3').prop('checked',false);
													jQuery('#priv_4').prop('checked',false);
													jQuery('#priv_6').prop('checked',false);
													var new_priv = 'priv_'+ response[0].privacy[0].privacy;
													jQuery('#' + new_priv).prop('checked',true);
													if(response[0].privacy[0].privacy == 3) // group privacy
													{
														jQuery('#selected_group').val(response[0].privacy[0].groupID);
													}
												}
												else
												{
													jQuery('#priv_2').prop('checked',true);
												}
									}, "jsonp")	
 								.success(function() {	
					  		})
								.error(function( jqXHR, textStatus, errorThrown) {
       						console.log("error " + textStatus);
        					console.log("errorThrown " + errorThrown);
        					console.log("incoming Text " + jqXHR.responseText);
	       					console.log("contents Text " + jqXHR.contents);
									console.log("get XMLHttpRequest= "+XMLHttpRequest.responseText);
								});

	}
	
function ShowLoadingScreen(txtMessg){
		var maskHeight = jQuery(document).height();
    var maskWidth = jQuery(window).width();
    jQuery('.padder').append( '<div id="loadmask"style="text-align: center;"><p><H3>'+txtMessg+'</H3></p></div>'); 
    //Set height and width to mask to fill up the whole screen
//    jQuery('#loadmask').css({'width':maskWidth,'height':maskHeight});
    
		//transition effect     
		jQuery('#loadmask').fadeIn(1000);    
		jQuery('#loadmask').fadeTo(10,0.5);  
}

function HideLoadingScreen(){
		jQuery('#loadmask').hide();
		jQuery('#loadmask').remove();    
}


jQuery(document).ready(function(){ 

	function callRemoveVideo(element){
	
		jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 		      
			type: "GET",
			dataType: "html",
			async: false,
			data: "namefunction=RemoveVideo&id=" + element.parent().parent().attr("id") , 
			beforeSend: function(){ 
				element.parent().children(".headerBox").children(".icon").hide(); 
				element.parent().children(".headerBox").children(".loader").show();						   
			},
			complete: function(){ 
				element.parent().children(".headerBox").children(".icon").show(); 
				element.parent().children(".headerBox").children(".loader").hide(); 
			},
			success: function(response) {
				var json =  jQuery.parseJSON(response);
				var result = json.result;
				if (result=="SUCCESS"){
					element.parent().parent().hide();
				}
				alert (json.result + " : " + json.message);
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		})

	
	}

	function callviewVideothumbs (element){
		var id = element.parent().parent().parent().parent("li").attr("id");
		jQuery(".icon_viewVideo").colorbox({
	        html:function(){
		    var stateView = jQuery(this).attr("rel").split('|');
		    var roles = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getRoles",
							id : id						
						},
						success: function(response) {
							roles = response;
						}
			}).responseText;
			var users = jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						async: false, 
						data:{ 
							namefunction: "getUsers",
							id : id
						},
						success: function(response) {
							users = response; 	
						}
			}).responseText;

				text  = '<b>Where should see the video?</b>';
				
				text += '<p class="viewThumbs';
				if (stateView[0]=="0") text += " selected";
				text += '" id="0">Invisible</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="1") text += " selected";
				text += '" id="1">Only into widget</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="2") text += " selected";
				text += '" id="2">Only into pages</p>';
				text += '<p class="viewThumbs';
				if (stateView[0]=="3") text += " selected";
				text += '" id="3">Into widget and page</p>';
				
				text  += '<b>Who should see the video?</b><br/>';
				
				text += '<select onChange="viewWho(this);" id="users" multiple="multiple" style="height:150px;width:270px">';
				text += '<option value="All">Everybody</option>';
				text += '<optgroup label="Users" id="optUsers">';
				text += users;
				text += '</optgroup>';
				text += '<optgroup label="Roles" id="optRoles">';
				text += roles;
				text += '</optgroup>';
				text += '<option value="No">Nobody</option>';
				text += '</select>';
				text += '<br/><p class="description">(Multiselect with CTRL) You are selected <br/><strong id="AddUser"></strong><br/><strong id="AddRole"></strong></p>';
				text += '<div class="action"><span class="form_save">Save</span><span class="icon_sync2" style="display:none;">Loading...</span></div>';
				
				return text;
			},
			onComplete: function(){
				
				var stateView = jQuery(this).attr("rel").split('|');
				jQuery(".viewThumbs").click(function(){
					jQuery(".viewThumbs").removeClass("selected");
					jQuery(this).addClass("selected");
					
				});	
				

				jQuery("#users option[value='" + stateView[1]  + "']").attr('selected', 'selected');
				jQuery(".form_save").click(function(){
					var state = jQuery(".selected").attr("id");
					
					var blkstr = jQuery.map(jQuery("#users").val(), function(val,index) {
					     return val;
					}).join(",");
					
					if (state=="0") state += "|No";		
                    else state += "|" + blkstr;
					//alert (id);
					jQuery.ajax({
						context: this,
						url:  url_pathPlugin + "scripts.php", 
						type: "GET",
						dataType: "html",
						data:{ 
							state : state, 
							id : id,
							namefunction: "StateViewThumbs" 
						},
						
						beforeSend: function(){ 
							//jQuery(this).parent().hide(); 
							//jQuery(this).parent().parent().children(".loader").show(); 	
							//jQuery(this).colorBox(wimtvproAddShowtime);	
							jQuery(".icon_sync2").show();	
							jQuery(".form_save").hide();		   
						},
						success: function(response) {
							jQuery.colorbox.close();	
							element.parent().parent().children(".icon").children("span").attr("rel",state); 	
						}
					});
				});
			}
		});
		
	}	

	
	function putST(element,namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id){
		jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 
			type: "GET",
			dataType: "html",
			data:{ 
				coId : coId, 
				id : id,
				namefunction: namefunction, 
				licenseType: licenseType,
				paymentMode:paymentMode,
				ccType:ccType,
				pricePerView:pricePerView,
				pricePerViewCurrency:pricePerViewCurrency
			},
			
			beforeSend: function(){ 
				jQuery(".icon_sync2").show();	
				jQuery(".form_save").hide();			   
			},
			success: function(response) {
				var json =  jQuery.parseJSON(response);
				var result = json.result;

				if (result=="SUCCESS"){
					jQuery.colorbox.close();
					element.parent().parent().children(".icon").children("span").hide();				 	
					element.parent().parent().children(".icon").children("span." + changeClass).show();
					element.parent().parent().children(".icon").children("span." + changeClass).attr("id", json.showtimeIdentifier);			        		
					
					element.parent().parent().children(".icon").children("a.viewThumb").show();
					url=  "admin/config/embedded/" + id + "/" + json.showtimeIdentifier;
					element.parent().parent().children(".icon").children("a.viewThumb").attr("id",url);
					element.parent().remove();			        		
				} else {
					jQuery(this).parent().hide(); 
					jQuery(this).parent().parent().children(".loader").show();
					jQuery(".icon_sync2").hide();	
					jQuery(".form_save").show();
				}
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
	}
	function callViewForm(element){
		element.parent().parent().children(".formVideo").fadeToggle("slow");
	}
	function callPutShowtime(element){
		jQuery(element).colorbox({
			html:function(){
				var thisclass = element.attr("class");
				if (thisclass.indexOf("free") >= 0){	
					text = "<p>Do you want your video to be visible to all for free?</p><div class='action'><span class='form_save'>Save</span><span class='icon_sync2' style='display:none;'>Loading...</span></div>";
				}else if (thisclass.indexOf("cc") >= 0){	
					text  = '<p class="cc_set" id="BY_NC_SA"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial No Derivatives.png" 	title="Attribution Non-Commercial No Derivatives" /> Attribution Non-Commercial No Derivatives</p>';		
					text += '<p class="cc_set" id="BY_NC_ND"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial Share Alike.png" 	title="Attribution Non-Commercial Share Alike" /> Attribution Non-Commercial Share Alike</p>';
					text += '<p class="cc_set" id="BY_NC"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Non-commercial.png" 			title="Attribution Non-Commercial" /> Attribution Non-Commercial</p>';
					text += '<p class="cc_set" id="BY_ND"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution No Derivatives.png" 			title="Attribution No Derivatives" /> Attribution No Derivatives</p>';
					text += '<p class="cc_set" id="BY_SA"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution Share Alike.png" 				title"Attribution Share Alike" /> Attribution Share Alike</p>';
					text += '<p class="cc_set" id="BY"><img src="http://www.wim.tv/wimtv-webapp/images/cclicense/Attribution.png" 						title="Attribution" /> Attribution</p>';      
					text += '<div class="action"><span class="form_save">Save</span><span class="icon_sync2" style="display:none;">Loading...</span></div>';
				} else if (thisclass.indexOf("ppv") >= 0){
					text  = '<form><input type="text" name="amount" class="amount" value="00" />.<input type="text" name="amount_cent" class="amount_cent" value="00" maxlength="2"/>';
					text  += '<select name="currency" class="currency">';
					text  += '	<option selected="selected"  value="EUR">Euro</option>';
					text  += '	<option disabled="true" value="USD">Usd</option>';
					text  += '	<option disabled="true" value="points">Points</option>';
					text  += '</select></form>';
					text += '<div class="action"><span class="form_save">Save</span><span class="icon_sync2" style="display:none;">Loading...</span></div>';
				}
				return text;
			},
			onComplete: function(){
				jQuery(".cc_set").click(function(){
					jQuery(".cc_set").removeClass("selected");
					jQuery(this).addClass("selected");
				});	
				jQuery(".form_save").click(function(){
					var namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id ="";
					var id = element.parent().parent().parent().parent("li").attr("id");
					var icon = element.parent().parent().children(".icon");
					var nomeclass = element.parent().parent().children(".icon").children("span.add").attr("class");		
					var thisclass = element.attr("class");
					if (nomeclass == "add icon_Putshowtime") {
						namefunction = "putST";
						changeClass = "icon_Removeshowtime";	
					}						 	 
					else if (nomeclass == "add icon_AcquiPutshowtime") {	 	
						namefunction = "putAcqST";
						changeClass = "icon_AcqRemoveshowtime";
						coId = "&acquiredId=" + element.attr("id");
					} 
					if (thisclass.indexOf("free") >= 0){
						licenseType ="TEMPLATE_LICENSE";
						paymentMode ="FREEOFCHARGE";
					} else if (thisclass.indexOf("cc") >= 0){
						licenseType ="CREATIVE_COMMONS";
						ccType = jQuery(this).parent().parent().children(".selected").attr("id");
					} else if (thisclass.indexOf("ppv") >= 0){
						licenseType ="TEMPLATE_LICENSE";
						paymentMode ="PAYPERVIEW";
						pricePerView = jQuery(".amount").val() + "." + jQuery(".amount_cent").val();
						pricePerViewCurrency = jQuery(".currency").val();
					}
			
					putST(element,namefunction,licenseType,paymentMode,ccType,pricePerView,pricePerViewCurrency,changeClass,coId,id);
			
				});
			}	
		});
	}	
	function callRemoveShowtime(element){
		nomeclass = element.attr("class");
		coId = "";
		if (nomeclass == "icon_AcqRemoveshowtime") {	 	
			namefunction = "removeST";
			changeClass = "icon_AcquiPutshowtime";
			coId = "&showtimeId=" + element.attr("id");
		} else {
			namefunction = "removeST";
			changeClass = "icon_Putshowtime";
			coId = "&showtimeId=" + element.attr("id");
		}
		jQuery.ajax({
				context: this,
				url:  url_pathPlugin + "scripts.php", 		      
				type: "GET",
				dataType: "html",
				data: "namefunction=" + namefunction + "&id=" + element.parent().parent().parent().parent().attr("id") + coId , 
				beforeSend: function(){ 
				element.parent().hide(); 
				element.parent().parent().children(".loader").show(); 						   
			},
			complete: function(){ 
				element.parent().show(); 
				element.parent().parent().children(".loader").hide(); 
			},
			success: function(response) {
				var json =  jQuery.parseJSON(response);
				var result = json.result;
				if (result=="SUCCESS"){
					//element.removeClass();
					//element.addClass(changeClass);
					element.hide();
					element.parent().children("." + changeClass).show();
					element.parent().children("." + changeClass).attr("id", json.showtimeIdentifier);
					element.parent().children(".icon_remove").show();

					if ((nomeclass == "icon_AcquiRemoveshowtime") || (nomeclass == "icon_Removeshowtime")) {
						element.parent().children(".icon_moveThumbs").hide();
						element.parent().children(".viewThumb").hide();
						element.parent().children(".viewThumb").attr("href","#");
						element.parent().parent().parent().children("div.infos").hide();
					} else
						element.parent().parent().parent().parent().hide();
				} else {
					element.parent().hide(); 
					element.parent().parent().children(".loader").show();
					alert (json.messages[0].message); 
				}
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
	}
	jQuery(".icon_sync0").click(function(){
		jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "sync.php",
			dataType: "html",
			data: {sync:true,showtime:jQuery("ul.items").attr("id")},
			type: "GET",
			beforeSend: function(){ 
				jQuery(this).removeClass();
				jQuery(this).addClass("icon_sync1");
				jQuery("ul.items li").remove(); 						   
			},
			complete: function(){ 
				jQuery(this).removeClass();
				jQuery(this).addClass("icon_sync0");
			},
			success: function(response) {	
				jQuery("ul.items").html(response);
				jQuery("a.viewThumb").click( function(){
                  jQuery(this).colorbox({href:jQuery(this).attr("id")});
                });
				jQuery(".icon_Putshowtime,.icon_AcquiPutshowtime").click(function(){
					callViewForm(jQuery(this));					
				});
			
				jQuery(".icon_AcqRemoveshowtime,.icon_Removeshowtime,.icon_RemoveshowtimeInto").click(function(){
					callRemoveShowtime(jQuery(this));
				});
				jQuery(".free,.cc,.pay").click(function(){
					callPutShowtime(jQuery(this));					
				});
				jQuery(".icon_remove").click(function(){
				callRemoveVideo(jQuery(this));
				});
	
				callviewVideothumbs(jQuery(this));
			},
			
			error: function(response) {	
				jQuery("ul.items").html(response);
			}
		});
	});
	jQuery(".free,.cc,.ppv").click(function(){
		callPutShowtime(jQuery(this));					
	});
	jQuery(".icon_Putshowtime,.icon_AcquiPutshowtime").click(function(){
		callViewForm(jQuery(this));
	});
	jQuery(".icon_AcqRemoveshowtime,.icon_Removeshowtime,.icon_RemoveshowtimeInto").click(function(){
		callRemoveShowtime(jQuery(this));
	});
	
	jQuery(".icon_viewVideo").click(function(){
		callviewVideothumbs(jQuery(this));
	});
	
	
	jQuery(".icon_remove").click(function(){
		callRemoveVideo(jQuery(this));
	});
	
	

	
	//Request new URL for create a wimlive Url
	jQuery(".createUrl").click(function(){
	  jQuery.ajax({
			context: this,
			url:  url_pathPlugin + "scripts.php", 
			type: "GET",
			dataType: "html",
			data:{
				namefunction: "urlCreate",
				titleLive: jQuery("#edit-name").val()	
			},
			success: function(response) {
			  var json =  jQuery.parseJSON(response);
			  var result = json.result;
			  if (result=="SUCCESS"){
			  	jQuery("#edit-url").attr("readonly", "readonly");
			  	jQuery("#edit-url").attr("value", json.liveUrl);
			  	jQuery(this).hide();
				jQuery(".removeUrl").show();
			  } else {
			    //alert (response);
			    alert ("Insert a password for live streaming is required");
			    jQuery(".passwordUrlLive").show();
			    jQuery(".createPass").click(function(){
			     jQuery.ajax({
			     context: this,
			     url:  url_pathPlugin + "scripts.php", 
			     type: "GET",
			     dataType: "html",
			     data:{
				  namefunction: "passCreate",
				  newPass: jQuery("#passwordLive").val()
			     },
                 success: function(response) {
                 	alert (response);
                 	jQuery(".passwordUrlLive").hide();
                 }
			    });
	            });
			  }
			},
			error: function(request,error) {
				alert(request.responseText);
			}	
		});
     });
   jQuery(".removeUrl").click(function(){
     jQuery(this).hide();
     jQuery(".createUrl").show();
     jQuery("#edit-url").removeAttr("readonly");
     jQuery("#edit-url").removeAttr("disabled");
     jQuery("#edit-url").val("");	
   });
      
});



function viewCategories(obj){
    jQuery("#addCategories").html("You are selected");
    var selectedArray = new Array();
    count = 0;
    for (i=0; i<obj.options.length; i++) {
      if (obj.options[i].selected) {
        selectedArray[count] = obj.options[i].value;
        valueSelected = obj.options[i].value;
        count++;
        jQuery("#addCategories").append("<br/>" + valueSelected);
      }
    }
}

function viewWho(obj){  
    var selectedArray = new Array();
    count = 0;
    jQuery("#AddUser").html("");
    jQuery("#AddRole").html("");

    for (i=0; i<obj.options.length; i++) {
      if (obj.options[i].selected) {
        
        selectedArray[count] = obj.options[i].value;
        valueSelected = obj.options[i].text;
        count++;
		
        if ( obj.options[i].parentNode.id == "optUsers"){
            if (jQuery("#AddUser").html() == "") jQuery("#AddUser").html("Users:");
        	jQuery("#AddUser").append(" " + valueSelected);
        }
        if ( obj.options[i].parentNode.id == "optRoles"){
        	if (jQuery("#AddRole").html() == "") jQuery("#AddRole").html("Roles:");
        	jQuery("#AddRole").append(" " + valueSelected);
        }
        	
      }
    }
    for (i=0; i<obj.options.length; i++) {
    	if ((obj.options[i].selected) && (obj.options[i].parentNode.id != "optUsers") && (obj.options[i].parentNode.id != "optRoles")) {
    		
    	    jQuery("#AddUser").html(obj.options[i].text);
        	jQuery("#AddRole").html("");
        	jQuery(obj).children().children().removeAttr("selected");
        	jQuery(obj).children().removeAttr("selected");
        	jQuery(obj).children().eq(i).attr("selected","selected");
        
        }
    }

    
}


function wimtvpro_TestFileType() {	
    fileName = jQuery("input[name=\"files[videoFile]\"]").val();
    fileTypes = [ "", "mov", "mpg", "avi", "flv", "mpeg", "mp4", "mkv", "m4v" ];
    if (!fileName) {
      return;
    }

    dots = fileName.split(".");
    // get the part AFTER the LAST period.
    fileType = "." + dots[dots.length - 1];

    if (fileTypes.join(".").indexOf(fileType.toLowerCase()) != -1) {
      return TRUE;
    } else {
      alert("Please only upload files that end in types: \n\n"
      + (fileTypes.join(" ."))
      + "\n\nPlease select a new file and try again.");
      jQuery("input[name=\"files[videoFile]\"]").val("");
    }
}

jQuery(document).ready(function() { 
  jQuery("ul.itemsPublic li a").colorbox();
  jQuery('.buttonInsert').click(function() {
      var width = jQuery(this).parent().children('.w').val();
      var height = jQuery(this).parent().children('.h').val();
      var id = jQuery(this).attr('id');
	  jQuery.ajax({
	    context: this,
        url:  url_pathPlugin + "scripts.php", 
	    type: "GET",
	    data:{ 
	      HFrame : height,
	      WFrame : width,
	      id : id,
	      namefunction: "getIFrameVideo"
	    },
	    success: function(response){
	  	  var win = window.dialogArguments || opener || parent || top;
          win.send_to_editor(response); 
	    },
	    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
	  });
  });
  
    
  //Playlist
   jQuery('.icon_selectPlay').click(function() {
  	jQuery(".playlist").removeClass("selected");
  	 jQuery(this).parent().addClass("selected");
  });


  jQuery('.icon_createPlay').click(function() {
  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
  	alert (nameNewPlaylist);
  	//add to DB
  	jQuery.ajax({
	    context: this,
        url:  url_pathPlugin + "scripts_playlist.php", 
	    type: "GET",
	    data:{ 
	      HFrame : height,
	      WFrame : width,
	      id : id,
	      namefunction: "createPlaylist"
	    },
	    success: function(response){
	  		var newRiga = '<div class="playlist" id="playlist_' +  response + '" rel=""><input type="text" value="Playlist ' +  response + '" /><span class="icon_selectPlay"></span><span class="icon_createPlay"></span></div>
	  	},
	    error: function(jqXHR, textStatus, errorThrown){alert(errorThrown);} 
	  });
  
  	
  });
  
  
  jQuery('.icon_deletePlay').click(function() {
  	var nameNewPlaylist = jQuery(this).parent().children("input").val();
  	alert (nameNewPlaylist);
  	//remove from DB
  	
  	//remove div
  	jQuery(this).parent().remove();
  	
  });



  //End Playlist

  
}); 
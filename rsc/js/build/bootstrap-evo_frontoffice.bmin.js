/* This includes 7 files: src/bootstrap-evo_modal_window.js, src/evo_images.js, src/evo_user_crop.js, src/evo_user_report.js, src/evo_user_contact_groups.js, src/evo_rest_api.js, src/evo_item_flag.js */
function openModalWindow(a,b,c,d,e,f,g,h,i){var j=void 0===b||"auto"==b?"":"width:"+b+";",k=void 0===c||0==c||""==c?"":"height:"+c,l=k.match(/%$/i)?' style="height:100%;overflow:hidden;"':"",m=c.match(/px/i)?' style="min-height:'+(c.replace("px","")-157)+'px"':"",n=void 0===f||0!=f;if(void 0!==f&&""!=f)if("object"==typeof f)var o=f[0],p=f[1],q=void 0===f[2]?"form":f[2];else var o=f,p="btn-primary",q="form";if(void 0!==g&&g&&jQuery("#modal_window").remove(),0==jQuery("#modal_window").length){var r='<div id="modal_window" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog" style="'+j+k+'"><div class="modal-content"'+l+">";void 0!==e&&""!=e&&(r+='<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">'+e+"</h4></div>"),r+='<div class="modal-body"'+l+m+">"+a+"</div>",n&&(r+='<div class="modal-footer">',void 0!==f&&""!=f&&(r+='<button class="btn '+p+'" type="submit" style="display:none">'+o+"</button>"),r+='<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">'+evo_js_lang_close+"</button></div>"),r+="</div></div></div>",jQuery("body").append(r)}else jQuery("#modal_window .modal-body").html(a);void 0!==i?jQuery("#"+i).load(function(){prepareModalWindow(jQuery(this).contents(),q,n,h),jQuery("#modal_window .loader_img").remove(),jQuery("#"+i).show()}):prepareModalWindow("#modal_window",q,n,h);var s={};modal_window_js_initialized&&(s="show"),jQuery("#modal_window").modal(s),""==j&&(jQuery("#modal_window .modal-dialog").css({display:"table",width:"auto"}),jQuery("#modal_window .modal-dialog .modal-content").css({display:"table-cell"})),jQuery("#modal_window").on("hidden",function(){jQuery(this).remove()}),modal_window_js_initialized=!0}function prepareModalWindow(a,b,c,d){c&&(void 0!==d&&d||(jQuery("legend",a).remove(),jQuery("#close_button",a).remove(),jQuery(".panel, .panel-body",a).removeClass("panel panel-default panel-body")),0==jQuery(b+" input[type=submit]",a).length?jQuery("#modal_window .modal-footer button[type=submit]").hide():(jQuery(b+" input[type=submit]",a).hide(),jQuery("#modal_window .modal-footer button[type=submit]").show()),jQuery(b,a).change(function(){var a=jQuery(this).find("input[type=submit]");a.length>0?(a.hide(),jQuery("#modal_window .modal-footer button[type=submit]").show()):jQuery("#modal_window .modal-footer button[type=submit]").hide()}),jQuery("#modal_window .modal-footer button[type=submit]").click(function(){jQuery(b+" input[type=submit]",a).click()})),jQuery(b+" a.btn",a).each(function(){jQuery("#modal_window .modal-footer").prepend("<a href="+jQuery(this).attr("href")+'><button type="button" class="'+jQuery(this).attr("class")+'">'+jQuery(this).html()+"</button></a>"),jQuery(this).remove()}),jQuery(b+" #current_modal_title",a).length>0&&jQuery("#modal_window .modal-title").html(jQuery(b+" #current_modal_title",a).html())}function closeModalWindow(a){return void 0===a&&(a=window.document),jQuery("#modal_window",a).remove(),!1}function user_crop_avatar(a,b,c){void 0===c&&(c="avatar");var d=750,e=320,f=jQuery(window).width(),g=jQuery(window).height(),h=f,i=g,j=i/h;i=i>d?d:i<e?e:i,h=h>d?d:h<e?e:h;var k=10,l=10;k=h-2*k>e?10:0,l=i-2*l>e?10:0;var m=h>d?d:h,n=i>d?d:i;openModalWindow('<span id="spinner" class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',m+"px",n+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary"],!0);var o={top:parseInt(jQuery("div.modal-dialog div.modal-body").css("paddingTop")),right:parseInt(jQuery("div.modal-dialog div.modal-body").css("paddingRight")),bottom:parseInt(jQuery("div.modal-dialog div.modal-body").css("paddingBottom")),left:parseInt(jQuery("div.modal-dialog div.modal-body").css("paddingLeft"))},p=parseInt(jQuery("div.modal-dialog div.modal-body").css("min-height"))-(o.top+o.bottom),q=m-(o.left+o.right),r={user_ID:a,file_ID:b,aspect_ratio:j,content_width:q,content_height:p,display_mode:"js",crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(r.ctrl="user",r.user_tab="crop",r.user_tab_from=c):(r.blog=evo_js_blog,r.disp="avatar",r.action="crop"),jQuery.ajax({type:"POST",url:evo_js_user_crop_ajax_url,data:r,success:function(a){openModalWindow(a,m+"px",n+"px",!0,evo_js_lang_crop_profile_pic,[evo_js_lang_crop,"btn-primary"])}}),!1}function user_report(a,b){openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"],!0);var c={action:"get_user_report_form",user_ID:a,crumb_user:evo_js_crumb_user};return evo_js_is_backoffice?(c.is_backoffice=1,c.user_tab=b):c.blog=evo_js_blog,jQuery.ajax({type:"POST",url:evo_js_user_report_ajax_url,data:c,success:function(a){openModalWindow(a,"auto","",!0,evo_js_lang_report_user,[evo_js_lang_report_this_user_now,"btn-danger"])}}),!1}function user_contact_groups(a){return openModalWindow('<span class="loader_img loader_user_report absolute_center" title="'+evo_js_lang_loading+'"></span>',"auto","",!0,evo_js_lang_contact_groups,evo_js_lang_save,!0),jQuery.ajax({type:"POST",url:evo_js_user_contact_groups_ajax_url,data:{action:"get_user_contact_form",blog:evo_js_blog,user_ID:a,crumb_user:evo_js_crumb_user},success:function(a){openModalWindow(a,"auto","",!0,evo_js_lang_contact_groups,evo_js_lang_save,!0)}}),!1}function evo_rest_api_request(url,params_func,func_method,method,func_fail){var params=params_func,func=func_method;"function"==typeof params_func&&(func=params_func,params={},method=func_method),void 0===method&&(method="GET"),jQuery.ajax({contentType:"application/json; charset=utf-8",type:method,url:restapi_url+url,data:params}).then(function(data,textStatus,jqXHR){"object"==typeof jqXHR.responseJSON&&eval(func)(data,textStatus,jqXHR)},function(jqXHR){b2evo_show_debug_ajax_error=!1,"function"==typeof func_fail&&"object"==typeof jqXHR.responseJSON&&eval(func_fail)(jqXHR.responseJSON,jqXHR)})}var modal_window_js_initialized=!1;jQuery(document).ready(function(){jQuery("img.loadimg").each(function(){jQuery(this).prop("complete")?jQuery(this).removeClass("loadimg"):jQuery(this).on("load",function(){jQuery(this).removeClass("loadimg")})})}),jQuery(document).on("click","a.evo_post_flag_btn",function(){var a=jQuery(this),b=parseInt(a.data("id"));return b>0&&(a.data("status","inprogress"),jQuery("span",jQuery(this)).addClass("fa-x--hover"),evo_rest_api_request("collections/"+a.data("coll")+"/items/"+b+"/flag",function(b){b.flag?(a.find("span:first").show(),a.find("span:last").hide()):(a.find("span:last").show(),a.find("span:first").hide()),jQuery("span",a).removeClass("fa-x--hover"),setTimeout(function(){a.removeData("status")},500)})),!1}),jQuery(document).on("mouseover","a.evo_post_flag_btn",function(){"inprogress"!=jQuery(this).data("status")&&jQuery("span",jQuery(this)).addClass("fa-x--hover")});
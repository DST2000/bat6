window.onpopstate=function(e){location.href=document.location};var customFilters={eventsAssigned:new Array,uriLocationState:{page:"Results"},counterHist:0,assignEvents:function(e){0!=this.eventsAssigned[e]&&null!=this.eventsAssigned[e]||("btn"!=customFiltersProp[e].results_trigger&&"ajax"!=customFiltersProp[e].results_loading_mode||(document.id("cf_wrapp_all_"+e).addEvent("click:relay(a)",function(t){if(t.stop(),0==customFiltersProp[e].category_flt_parent_link&&this.hasClass("cf_parentOpt"))return!1;var s=this.get("href");customFilters.listen(t,this,e,s)}),document.id("cf_wrapp_all_"+e).addEvent("click:relay(input[type=checkbox],input[type=radio])",function(t){var s="",i=document.id(this.get("id")+"_a");if(i)s=i.get("href");customFilters.listen(t,this,e,s)}),document.id("cf_wrapp_all_"+e).addEvent("change:relay(select[class=cf_flt])",function(t){t.stop();var s="",s=this.options[this.selectedIndex].getAttribute("data-url");customFilters.listen(t,this,e,s)})),"ajax"==customFiltersProp[e].results_loading_mode&&"btn"==customFiltersProp[e].results_trigger&&document.id("cf_wrapp_all_"+e).addEvent("click:relay(input[type=submit],button[type=submit])",function(t){t.preventDefault(),customFilters.listen(t,this,e)}),document.id("cf_wrapp_all_"+e).addEvent("click:relay(button[class=cf_search_button btn])",function(t){t.stop();var s="",i="",r="",l=this.getProperty("id"),n=l.substr(0,l.indexOf("_button")),o=document.id(n+"_url").value,a=o.indexOf("?"),u=document.id(n+"_0"),d=document.id(n+"_1");if(u&&d)var c=u.value,m=d.value,p=u.name,_=d.name;else{c=document.id(n+"_0").value;p=document.id(n+"_0").name}if(-1!=a)f="&";else var f="?";if(c&&(s=p+"="+c),m&&(i=_+"="+m),r+=s&&!i?f+s:!s&&i?f+i:f+s+"&"+i)var v=o+r;v&&("ajax"==customFiltersProp[e].results_loading_mode||"btn"==customFiltersProp[e].results_trigger?customFilters.listen(t,this,e,v):window.top.location.href=v)}),this.eventsAssigned[e]=!0)},listen:function(e,t,s,i){if(s){var r=!1;query_value="",modurl=i;l="";if(void 0!==t.nodeType&&(r=t.hasClass("cf_apply_button")),void 0!==t.getProperty&&t.getProperty("id"))var l=this.getFilterName(t.getProperty("id"));if(("q"==l||r)&&(is_valid=this.validateInput(l,s),!is_valid))return!1;if(void 0!==customFiltersProp[s].mod_type&&"filtering"==customFiltersProp[s].mod_type&&(n=document.id("q_"+s+"_0"))&&(query_value=this.getQueryValue(s),void 0!==t.id&&t.id=="q_"+s+"_clear"&&(query_value=""),void 0===customFilters.previousQueryValue&&(customFilters.previousQueryValue=query_value),customFilters.keyword_search_clear_filters_on_new_search&&query_value!=customFilters.previousQueryValue&&(modurl=customFiltersProp[s].base_url+"index.php?option=com_customfilters&view=module&Itemid="+customFiltersProp[s].Itemid,query_value&&(-1==modurl.indexOf("?")?modurl+="?":modurl+="&",modurl+="q="+query_value))),"ajax"==customFiltersProp[s].results_loading_mode&&("btn"!=customFiltersProp[s].results_trigger||"btn"==customFiltersProp[s].results_trigger&&r)){if(void 0!==customFiltersProp[s].mod_type&&"filtering"==customFiltersProp[s].mod_type&&n){if(customFilters.keyword_search_clear_filters_on_new_search&&query_value!=customFilters.previousQueryValue){var i=customFiltersProp[s].component_base_url;query_value&&(-1==i.indexOf("?")?i+="?":i+="&",i+="q="+query_value)}this.updateSearchModules(query_value)}this.loadResults(s,i)}if(customFiltersProp[s].loadModule&&!r&&this.loadModule(e,t,s,modurl),customFiltersProp[s].loadOtherFilteringModules){query_value="";var n=document.id("cf-searchmod-input_"+s);if(void 0!==n){query_value=this.getQueryValue(s),void 0===customFilters.previousQueryValue&&(customFilters.previousQueryValue=query_value),customFilters.keyword_search_clear_filters_on_new_search&&query_value!=customFilters.previousQueryValue&&(modurl=customFiltersProp[s].base_url+"index.php?option=com_customfilters&view=module&Itemid="+customFiltersProp[s].Itemid,void 0!==t.id&&t.id=="q_"+s+"_clear"&&(query_value=""),query_value&&(-1==modurl.indexOf("?")?modurl+="?":modurl+="&",modurl+="q="+query_value));for(var o=this.getFilteringModules(),a=0;a<o.length;a++)this.updateFilteringModules(o[a],query_value),this.loadModule(e,t,o[a],modurl)}}this.setLastQueryValue(query_value)}},getQueryValue:function(e){return document.id("q_"+e+"_0").value},setLastQueryValue:function(e){customFilters.previousQueryValue=e},generateURL:function(e,t){if(customFilters.keyword_search_clear_filters_on_new_search&&t!=customFilters.previousQueryValue){var s=customFiltersProp[e].component_base_url;if(t)return-1==s.indexOf("?")?s+="?":s+="&",s+="q="+t}return!1},getFilterName:function(e){var t=e.match(/([a-z]+_){1}/i);return t=t[0]?t[0].replace("_",""):""},getFilteringModules:function(){for(var e=$$(".cf_wrapp_all"),t=new Array,s=0;s<e.length;s++){var i=e[s].id;i&&parseInt(t.push(i.substring(13)))}return t},updateFilteringModules:function(e,t){var s=document.id("cf_form_"+e);null!=s&&(s.getElement("input[name=q]").value=t)},updateSearchModules:function(e){for(var t=$$(".cf-form-search"),s=0;s<t.length;s++)t[s].getElement(".cf_message").innerHTML="",t[s].getElement(".cf_message").setStyle("display","none"),t[s].getElement("input[name=q]").value=e},loadModule:function(e,t,s,i){var r=document.id("cf_form_"+s),l=document.id("cf_wrapp_all_"+s),n=document.id("cf_ajax_loader_"+s),o=customFiltersProp[s].use_ajax_spinner,a="";if(1==o&&void 0!==e){var u=r.getPosition();if(void 0===e.page)d=e.pageY;else var d=e.page.y;a=d-u.y}if(i){var c=new URI(i);c.setData("view","module"),c.setData("tmpl","component"),c.setData("format","raw"),c.setData("module_id",s);new Request.HTML({url:c,noCache:!0,onRequest:function(){if(1==o){var e=l.getSize();n.addClass("cf_ajax_loader"),"undefined"!=a&&n.setStyle("background-position","center "+a+"px"),n.setStyle("height",e.y+"px"),n.setStyle("width",e.x+"px")}},onComplete:function(){if("cf_resetAll_link"==t.getProperty("class")&&l.getTop()<window.scrollY)new Fx.Scroll(window).toElement(l)},method:"post",update:l}).post()}else new Form.Request(r,l,{extraData:{view:"module",tmpl:"component",format:"raw",module_id:s,Itemid:"",method:"post"},onSend:function(){if(1==o){var e=l.getSize();n.addClass("cf_ajax_loader"),"undefined"!=a&&n.setStyle("background-position","center "+a+"px"),n.setStyle("height",e.y+"px"),n.setStyle("width",e.x+"px")}}}).send()},loadResults:function(e,t){var s=document.id("cf_form_"+e),i=customFiltersProp[e].results_wrapper,r=document.id("cf_res_ajax_loader"),l=document.id(i),n=customFiltersProp[e].base_url+"index.php?";if(t)o=new URI(t);else{-1==(n=s.action).indexOf("?")?n+="?":n+="&";var o=new URI(n)}o.setData("tmpl","component");var a=new Request.HTML({url:o,link:"cancel",evalScripts:!1,onRequest:function(){if(customFiltersProp[e].use_results_ajax_spinner){var t=l.getSize();r.setStyle("display","block"),r.setStyle("height",t.y+"px"),r.setStyle("width",t.x+"px")}},onSuccess:function(s,n,o,a){r.setStyle("display","none");var u=n.filter("#"+i);if(u&&(l.innerHTML=u[0].innerHTML,"undefined"!=typeof Virtuemart&&Virtuemart.product(jQuery("form.product")),"undefined"!=typeof Stockablecustomfields&&"function"==typeof Stockablecustomfields.setEvents?Stockablecustomfields.setEvents():"undefined"!=typeof CustomfieldsForAll&&"function"==typeof CustomfieldsForAll.eventHandler&&CustomfieldsForAll.eventHandler(),window.eval(a)),!t&&null!=document.id("cf_apply_button_"+e)&&(document.id("cf_apply_button_"+e).blur(),l.getTop()<window.scrollY))new Fx.Scroll(window).toElement(l)},onCancel:function(){},onFailure:function(e){r.setStyle("display","none")}});if(t)a.post(),customFilters.setWindowState(t);else{var u=s.toQueryString();u=n+(u=u.cleanQueryString()),customFilters.setWindowState(u),a.post(s)}},setWindowState:function(e){this.counterHist++;window.history.state;window.history.pushState&&window.history.replaceState&&window.history.pushState({page:this.counterHist},"Search Results",e)},addEventTree:function(e){var t="virtuemart_category_id";0==customFiltersProp[e].parent_link&&document.id("cf_wrapp_all_"+e).addEvent("click:relay(.cf_parentOpt)",function(s,i){s.stop();var r,l=i.getProperty("class").split(" "),n=l.length;i.hasClass("cf_unexpand")?(i.removeClass("cf_unexpand"),i.addClass("cf_expand")):i.hasClass("cf_expand")&&(i.removeClass("cf_expand"),i.addClass("cf_unexpand"));for(d=0;d<n;d++)l[d].indexOf("tree")>=0&&(r=l[d]);var o=i.getProperty("id");if(o=parseInt(o.slice(o.indexOf("_elid")+5)),r){r+="-"+o;var a=document.id("cf_list_"+t+"_"+e).getElements(".li-"+r);if(a[0].hasClass("cf_invisible"))u=!1;else var u=!0;for(var d=0;d<a.length;d++)if(0==u)a[d].removeClass("cf_invisible");else for(var c=document.id("cf_list_"+t+"_"+e).getElements("li[class*="+r+"]"),m=0;m<c.length;m++)c[m].addClass("cf_invisible"),c[m].hasClass("cf_parentLi")&&(c[m].getElement("a").removeClass("cf_expand"),c[m].getElement("a").addClass("cf_unexpand"))}return customFilters.setWrapperHeight(t,e),!1})},setWrapperHeight:function(e,t){var s=document.id("cf_wrapper_inner_"+e+"_"+t);s.getParent().setStyle("height",s.offsetHeight+"px")},addEventsRangeInputs:function(e,t){var s=e+"_"+t,i=document.id(s+"_0"),r=document.id(s+"_1");if(i&&r){customFilters.validateRangeFlt(t,e);var l=document.id(s+"_slider");i.addEvent("keyup",function(s){var i=customFilters.validateRangeFlt(t,e);null!=l&&customFilters.setSliderValues(t,e,i,"min")}),r.addEvent("keyup",function(s){var i=customFilters.validateRangeFlt(t,e);null!=l&&customFilters.setSliderValues(t,e,i,"max")}),"btn"==customFiltersProp[t].results_trigger&&(i.addEvent("change",function(s){customFilters.validateRangeFlt(t,e)&&customFilters.listen(i,t)}),r.addEvent("change",function(s){customFilters.validateRangeFlt(t,e)&&customFilters.listen(r,t)}))}},createToggle:function(e,t){var s=new Fx.Slide("cf_wrapper_inner_"+e,{duration:200,wrapper:!1,resetHeight:!1}),i=Cookie.read(e)?Cookie.read(e):t;s[i](),customFilters.setHeaderClass(e,i),document.id("cfhead_"+e).addEvent("click",function(t){t.stop();var i=s;i.toggle(),i.open?mystate="hide":mystate="show",customFilters.setHeaderClass(e,mystate);Cookie.write(e,mystate)})},setHeaderClass:function(e,t){var s="headexpand_"+e,i=document.id(s);"hide"==t?(i.removeClass("headexpand_show"),i.addClass("headexpand_hide")):(i.removeClass("headexpand_hide"),i.addClass("headexpand_show"))},validateRangeFlt:function(e,t){var t=t+"_"+e,s=document.id(t+"_0"),i=document.id(t+"_1");if("btn"!=customFiltersProp[e].results_trigger)var r=document.id(t+"_button");var l=s.value.replace(",","."),n=l.match(/^[+-]?\d+(\.\d*)?$/),o=i.value.replace(",","."),a=o.match(/^[+-]?\d+(\.\d*)?$/);return n&&0==o.length||a&&0==l.length||n&&a?l.length>0&&o.length>0&&parseFloat(l)>parseFloat(o)?(r&&r.setProperty("disabled","disabled"),this.displayMsg("",t),!1):(r&&r.removeProperty("disabled"),this.displayMsg("",t),new Array(l,o)):(r&&r.setProperty("disabled","disabled"),o.length>0||l.length>0?this.displayMsg(Joomla.JText._("MOD_CF_FILTERING_INVALID_CHARACTER"),t):this.displayMsg("",t),!1)},validateInput:function(e,t){var s=e+"_"+t,i=document.id(s+"_0");return!i||(i.value.length<2?(this.displayMsg(Joomla.JText._("MOD_CF_FILTERING_MIN_CHARACTERS_LIMIT"),s),!1):(this.displayMsg("",s),!0))},displayMsg:function(e,t){var s=document.id(t+"_message");e?(s.setStyle("display","block"),s.innerHTML=e):s.setStyle("display","none")},setSliderValues:function(module_id,filter,valid,minOrMax){var flt_key=filter+"_"+module_id,sliderObj=eval(flt_key+"_sliderObj");if(!1!==valid){var min_val=parseInt(valid[0]);isNaN(min_val)&&(min_val=parseInt(customFiltersProp[module_id].slider_min_value));var max_val=parseInt(valid[1]);isNaN(max_val)&&(max_val=parseInt(customFiltersProp[module_id].slider_max_value)),sliderObj.setMin(min_val),sliderObj.setMax(max_val)}else"min"==minOrMax?sliderObj.setMin(parseInt(customFiltersProp[module_id].slider_min_value)):"max"==minOrMax&&sliderObj.setMax(parseInt(customFiltersProp[module_id].slider_max_value))}},CfElementFilter=new Class({Implements:[Options,Events],options:{module_id:null,isexpanable_tree:!1,filter_key:"",cache:!0,caseSensitive:!1,ignoreKeys:[13,27,32,37,38,39,40],matchAnywhere:!0,optionClass:".cf_option",property:"text",trigger:"keyup",onHide:"",onComplete:"",onStart:function(){this.elements.addClass("cf_hide")},onShow:function(e){e.removeClass("cf_hide")},onMatchText:function(e){var t=this.observeElement.value,s=this.options.caseSensitive?"":"i",i=new RegExp(t,s),r=e.getElements(this.options.optionClass),l=r[0].get(this.options.property),n=l.toLowerCase(),o=t.toLowerCase(),a=n.indexOf(o),u=l.substr(a,t.length),d=l.replace(i,'<span class="cf_match">'+u+"</span>");r[0].set("html",d)}},initialize:function(e,t,s){this.setOptions(s),this.observeElement=document.id(e),this.elements=$$(t),this.matches=this.elements,this.misses=[],this.listen()},listen:function(){this.observeElement.addEvent(this.options.trigger,function(e){this.observeElement.value.length?this.options.ignoreKeys.contains(e.code)||(this.fireEvent("start"),this.findMatches(this.options.cache?this.matches:this.elements),this.fireEvent("complete")):(this.elements.removeClass("cf_hide"),this.clearHtmlFromText(this.elements),this.options.isexpanable_tree&&customFilters.setWrapperHeight(this.options.filter_key,this.options.module_id),this.findMatches(this.elements,!1),this.elements.getElements(".cf_invisible").each(function(e){e.setStyle("display","")}))}.bind(this))},findMatches:function(e,t){var s=this.observeElement.value,i=this.options.matchAnywhere?s:"^"+s,r=this.options.caseSensitive?"":"i",l=new RegExp(i,r);e.each(function(e){var s=void 0==t?l.test(e.get(this.options.property)):t,i=e.getProperty("class").contains("cf_invisible"," ");return s?(i&&e.setStyle("display","block"),this.fireEvent("matchText",[e]),this.fireEvent("show",[e])):(i&&e.setStyle("display",""),e.retrieve("showing")&&this.fireEvent("hide",[e]),e.store("showing",!1)),!0}.bind(this))},clearHtmlFromText:function(e){e.each(function(e){var t=e.getElements(this.options.optionClass),s=t[0].get(this.options.property);t[0].set("html",s)}.bind(this))}});
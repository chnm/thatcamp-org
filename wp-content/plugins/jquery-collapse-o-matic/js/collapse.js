/*!
 * Collapse-O-Matic v1.5.1
 * http://plugins.twinpictures.de/plugins/collapse-o-matic/
 *
 * Copyright 2013, Twinpictures
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, blend, trade,
 * bake, hack, scramble, difiburlate, digest and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
function collapse_init() {
	//force collapse
	jQuery('.force_content_collapse').each(function(index) {
		jQuery(this).css('display', 'none');
	});
	
	//inital collapse
	jQuery('.collapseomatic:not(.colomat-close)').each(function(index) {
		var thisid = jQuery(this).attr('id');
		jQuery('#target-'+thisid).css('display', 'none');
	});
	
	//inital swaptitle for pre-expanded elements
	jQuery('.collapseomatic.colomat-close').each(function(index) {
		var thisid = jQuery(this).attr('id');
		if(jQuery("#swap-"+thisid).length > 0){
			swapTitle(this, thisid);
		}
	});
}

function swapTitle(obj, id){
	var orightml = jQuery(obj).html();
	var swaphtml = jQuery("#swap-"+id).html();
	jQuery(obj).html(swaphtml);
	jQuery("#swap-"+id).html(orightml);
	
	//is cufon involved? if so, do that thing
	if(swaphtml.indexOf("<cufon") != -1){
		var trigelem = jQuery(this).get(0).tagName;
		Cufon.replace(trigelem);
	}
}

function closeOtherGroups(rel){
	jQuery('.collapseomatic[rel!="' + rel +'"]').each(function(index) {
		//add close class if open
		if(jQuery(this).hasClass('colomat-expand-only') && jQuery(this).hasClass('colomat-close')){
			return;
		}	
		if(jQuery(this).hasClass('colomat-close') && jQuery(this).attr('rel') !== undefined){
			jQuery(this).removeClass('colomat-close');
			var id = jQuery(this).attr('id');
			//remove parent highlight class
			jQuery('#parent-'+id).removeClass('colomat-parent-highlight');
			
			//check if the title needs to be swapped out
			if(jQuery("#swap-"+id).length > 0){
				swapTitle(this, id);
			}
	
			//slideToggle
			if(colomatslideEffect == 'slideToggle'){
				jQuery('#target-'+id).slideToggle(colomatduration, function() {
				// Animation complete.
				});
			}
			//slideFade
			else if(colomatslideEffect == 'slideFade'){
				jQuery('#target-'+id).animate({
					height: "toggle",
					opacity: "toggle"
				}, colomatduration);
			}
			
			//check if there are nested children that need to be collapsed
			var ancestors = jQuery('.collapseomatic', '#target-'+id);
			ancestors.each(function(index) {
				jQuery(this).removeClass('colomat-close');
				var thisid = jQuery(this).attr('id');
				jQuery('#target-'+thisid).css('display', 'none');
			})
		}
	});
}

function closeOtherMembers(rel, id){
	jQuery('.collapseomatic[rel="' + rel +'"]').each(function(index) {
		if(jQuery(this).hasClass('colomat-expand-only') && jQuery(this).hasClass('colomat-close')){
			return;
		}
		
		//add close class if open
		if(jQuery(this).attr('id') != id && jQuery(this).hasClass('colomat-close') && jQuery(this).attr('rel') !== undefined){
			//collapse the element
			jQuery(this).removeClass('colomat-close');
			var thisid = jQuery(this).attr('id');
			//remove parent highlight class
			jQuery('#parent-'+thisid).removeClass('colomat-parent-highlight');
			
			//check if the title needs to be swapped out
			if(jQuery("#swap-"+thisid).length > 0){
				swapTitle(this, thisid);
			}
			
			//check for snap-shut
			if(!jQuery(this).hasClass('colomat-close') && jQuery(this).hasClass('snap-shut')){
				jQuery('#target-'+thisid).hide();
			}
			
			//slideToggle
			else if(colomatslideEffect == 'slideToggle'){
				jQuery('#target-'+thisid).slideToggle(colomatduration, function() {
				// Animation complete.
				});
			}
			//slideFade
			else if(colomatslideEffect == 'slideFade'){
				jQuery('#target-'+thisid).animate({
					height: "toggle",
					opacity: "toggle"
				}, colomatduration);
			}
			
			//check if there are nested children that need to be collapsed
			var ancestors = jQuery('.collapseomatic', '#target-'+id);
			ancestors.each(function(index) {
				if(jQuery(this).hasClass('colomat-expand-only') && jQuery(this).hasClass('colomat-close')){
					return;
				}
				if(jQuery(this).attr('id').indexOf('bot-') == '-1'){
					jQuery(this).removeClass('colomat-close');
					var thisid = jQuery(this).attr('id');
					//check if the title needs to be swapped out
					if(jQuery("#swap-"+thisid).length > 0){
						swapTitle(this, thisid);
					}
					jQuery('#target-'+thisid).css('display', 'none');
				}
			})
		}
	});
}

jQuery(document).ready(function() {
	//console.log(colomatduration, colomatslideEffect);
	collapse_init();
	
	//Display the collapse wrapper... use to reverse the show-all on no JavaScript degredation.
	jQuery('.content_collapse_wrapper').each(function(index) {
		jQuery(this).css('display', 'inline');
	});
	
	//hover	
	jQuery('.collapseomatic').livequery(function(){
		jQuery(this).hover(function() {
			jQuery(this).addClass('colomat-hover');
		}, function() {
			jQuery(this).removeClass('colomat-hover');
		});
	}, function() {
		jQuery(this).unbind('mouseover').unbind('mouseout');
	});
    
	
    //jQuery('.collapseomatic').on("click", function(event){
	//jQuery('.collapseomatic').click(function() {
	jQuery('.collapseomatic').livequery('click', function(event) {
		var offest_top;
		
		//alert('phones ringin dude');
		if(jQuery(this).hasClass('colomat-expand-only') && jQuery(this).hasClass('colomat-close')){
			return;
		}
		var id = jQuery(this).attr('id');
		var offset_top = 0;
		
		//deal with any scroll to links
		if(jQuery(this).hasClass('colomat-close') && jQuery(this).hasClass('scroll-to-trigger')){
			offset_top = jQuery('#scrollonclose-'+id).attr('name');
		}
			
		if(id.indexOf('bot-') != '-1'){
			id = id.substr(4);
			jQuery('#'+id).toggleClass('colomat-close');
			
			//deal with any scroll to links from the Internal Collapse Trigger
			if(jQuery(this).hasClass('scroll-to-trigger')){
				var target_offset = jQuery('#'+id).offset();
				offset_top = target_offset.top;
			}
			
			//deal with any scroll to links from the Title Trigger
			if(jQuery('#'+id).hasClass('scroll-to-trigger')){
				offset_top = jQuery('#scrollonclose-'+id).attr('name');
			}
		}
		else{
			jQuery(this).toggleClass('colomat-close');
		}
		
		//check if the title needs to be swapped out
		if(jQuery("#swap-"+id).length > 0){
			swapTitle(jQuery('#'+id), id);
		}
		
		//add visited class
		jQuery(this).addClass('colomat-visited');
		
		//toggle parent highlight class
		var parentID = 'parent-'+id;
		jQuery('#' + parentID).toggleClass('colomat-parent-highlight');
			
		//check for snap-shut
		if(!jQuery(this).hasClass('colomat-close') && jQuery(this).hasClass('snap-shut')){
			jQuery('#target-'+id).hide();
		}
		
		//slideToggle
		else if(colomatslideEffect == 'slideToggle'){
			jQuery('#target-'+id).slideToggle(colomatduration, function() {
			// Animation complete.
			});
		}
		//slideFade
		else if(colomatslideEffect == 'slideFade'){
			jQuery('#target-'+id).removeClass('maptastic');
			jQuery('#target-'+id).animate({
				height: "toggle",
				opacity: "toggle"
			}, colomatduration);
		}
        
        //deal with grouped items if needed
        if(jQuery(this).attr('rel') !== undefined){
            var rel = jQuery(this).attr('rel');
			if(rel.indexOf('-highlander') != '-1'){
				closeOtherMembers(rel, id);
			}
			else{
				closeOtherGroups(rel);
			}   
        }
		
		if(offset_top){
			jQuery('html, body').animate({scrollTop:offset_top}, 500);
		}
    });
	
	jQuery('.expandall').livequery('click', function(event) {
		if(jQuery(this).attr('rel') !== undefined){
			var rel = jQuery(this).attr('rel');
			jQuery('.collapseomatic[rel="' + rel +'"].collapseomatic:not(.colomat-close)').each(function(index) {
					jQuery(this).addClass('colomat-close');
					var thisid = jQuery(this).attr('id');
					jQuery('#parent-'+thisid).addClass('colomat-parent-highlight');
					
					if(jQuery("#swap-"+thisid).length > 0){
						swapTitle(this, thisid);
					}
					
					//slideToggle
					if(colomatslideEffect == 'slideToggle'){
						jQuery('#target-'+thisid).slideToggle(colomatduration, function() {
						// Animation complete.
						});
					}
					//slideFade
					else if(colomatslideEffect == 'slideFade'){
						jQuery('#target-'+thisid).animate({
							height: "toggle",
							opacity: "toggle"
						}, colomatduration);
					}
			});
	    }
		else{
			jQuery('.collapseomatic:not(.colomat-close)').each(function(index) {
				jQuery(this).addClass('colomat-close');
				var thisid = jQuery(this).attr('id');
				jQuery('#parent-'+thisid).addClass('colomat-parent-highlight');
				
				if(jQuery("#swap-"+thisid).length > 0){
					swapTitle(this, thisid);
				}
				
				//slideToggle
				if(colomatslideEffect == 'slideToggle'){
					jQuery('#target-'+thisid).slideToggle(colomatduration, function() {
					// Animation complete.
					});
				}
				//slideFade
				else if(colomatslideEffect == 'slideFade'){
					jQuery('#target-'+thisid).animate({
						height: "toggle",
						opacity: "toggle"
					}, colomatduration);
				}
			});
		}
    });
    
	jQuery('.collapseall').livequery('click', function(event) {
		if(jQuery(this).attr('rel') !== undefined){
			var rel = jQuery(this).attr('rel');
			jQuery('.collapseomatic[rel="' + rel +'"].collapseomatic.colomat-close').each(function(index) {
				if(jQuery(this).hasClass('colomat-expand-only') && jQuery(this).hasClass('colomat-close')){
					return;
				}
				
				jQuery(this).removeClass('colomat-close');
				var thisid = jQuery(this).attr('id');
				jQuery('#parent-'+thisid).removeClass('colomat-parent-highlight');
				
				if(jQuery("#swap-"+thisid).length > 0){
					swapTitle(this, thisid);
				}
				
				//slideToggle
				if(colomatslideEffect == 'slideToggle'){
					jQuery('#target-'+thisid).slideToggle(colomatduration, function() {
					// Animation complete.
					});
				}
				//slideFade
				else if(colomatslideEffect == 'slideFade'){
					jQuery('#target-'+thisid).animate({
						height: "toggle",
						opacity: "toggle"
					}, colomatduration);
				}
			});
		}
		else{
			jQuery('.collapseomatic.colomat-close').each(function(index) {
				if(jQuery(this).hasClass('colomat-expand-only') && jQuery(this).hasClass('colomat-close')){
					return;
				}
				jQuery(this).removeClass('colomat-close');
				var thisid = jQuery(this).attr('id');
				jQuery('#parent-'+thisid).removeClass('colomat-parent-highlight');
				
				if(jQuery("#swap-"+thisid).length > 0){
					swapTitle(this, thisid);
				}
				
				//slideToggle
				if(colomatslideEffect == 'slideToggle'){
					jQuery('#target-'+thisid).slideToggle(colomatduration, function() {
					// Animation complete.
					});
				}
				//slideFade
				else if(colomatslideEffect == 'slideFade'){
					jQuery('#target-'+thisid).animate({
						height: "toggle",
						opacity: "toggle"
					}, colomatduration);
				}
			});
		}
    });
	
	//do we have a find me?
	//jQuery('.find-me').on("click", function(event){
	//jQuery('.find-me').click(function() {
	jQuery('.find-me').livequery('click', function(event) {
		var myFile = document.location.toString();
		if (myFile.match('#')) {
			return;
		}
		//get the top offset of the target anchor
		var thisid = jQuery(this).attr('id');
		var offset_top = jQuery('#find-'+thisid).attr('name');
		if(!offset_top){
			target_offset = jQuery(this).offset();
			offset_top = target_offset.top;
		}
		jQuery('html, body').animate({scrollTop:offset_top}, 500);
		//console.log(offset_top);
	});
	
	//handle new page loads with anchor
	var myFile = document.location.toString();
    if (myFile.match('#')) { // the URL contains an anchor
        // click the navigation item corresponding to the anchor
        var anchor_arr = myFile.split('#');
		if(anchor_arr.length > 1){
			junk = anchor_arr.splice(0, 1);
			anchor = anchor_arr.join('#');
		}
		else{
			anchor = anchor_arr[0];
		}
		jQuery('#' + anchor).click();
		//expand any nested parents
		jQuery('#' + anchor).parents('.collapseomatic_content').each(function(index) {
			parent_arr = jQuery(this).attr('id').split('-');
			junk = parent_arr.splice(0, 1);
			parent = parent_arr.join('-');
			jQuery('#' + parent).click();
		})
    }
	
	//handle anchor links within the same page
	jQuery('a.expandanchor').livequery('click', function(event) {
		event.preventDefault();
		var fullurl = jQuery(this).attr('href');
		if (fullurl.match('#')) { // the URL contains an anchor
			// click the navigation item corresponding to the anchor
			var anchor_arr = fullurl.split('#');
			if(anchor_arr.length > 1){
				junk = anchor_arr.splice(0, 1);
				anchor = anchor_arr.join('#');
			}
			else{
				anchor = anchor_arr[0];
			}
			if(!jQuery('#' + anchor).hasClass('colomat-close')){
				jQuery('#' + anchor).click();
			}
			
			//expand any nested parents
			jQuery('#' + anchor).parents('.collapseomatic_content').each(function(index) {
				parent_arr = jQuery(this).attr('id').split('-');
				junk = parent_arr.splice(0, 1);
				parent = parent_arr.join('-');
				if(!jQuery('#' + parent).hasClass('colomat-close')){
					jQuery('#' + parent).click();
				}
				
			})
		}
	});
	
	//jQuery('a.colomat-nolink').click(function(event) {
	jQuery('a.colomat-nolink').livequery('click', function(event) {
		event.preventDefault();
	});	
});
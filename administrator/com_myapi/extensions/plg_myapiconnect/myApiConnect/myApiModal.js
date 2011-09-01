// JavaScript Document
var myApiModal = {
		open : function(title,subheader,content,buttons){
			myApiModal.create();
			
			$('dialog_title').set('html',title);
			if(subheader == null)
				$('dialog_summary').dispose();
			else
				$('dialog_summary').set('html',subheader);
			$('dialog_body').set('html',content);
			
			if (typeof buttons != 'undefined'){
				buttonsHTML = '<table class="myapi"><tr>'; 
				for(index in buttons){
					buttonsHTML += '<td width="50" align="left"><input type="button" value="'+buttons[index]['name']+'" name="'+buttons[index]['name']+'" onClick="'+buttons[index]['js']+'" /></td>';
				}
				buttonsHTML += '</tr></table>';
				$('dialog_buttons').set('html',buttonsHTML);
			}
			
			this.setHeight();
			
			window.addEvent('resize',function(){
				myApiModal.setHeight();
			});
			
			if(typeof(FB) !== "undefined"){
				FB.XFBML.parse(document.getElementById('pop_dialog_table'));
			}
			
			$('fb-modal').opacityFx.start(1);
		},
		getZindex : function(){
			var highestIndex = 0;
			var currentIndex = 0;
			var elArray = Array();
			elArray = document.getElementsByTagName('*');
			for(var i=0; i < elArray.length; i++){
				if (elArray[i].style && ! window.getComputedStyle){
					currentIndex = parseInt(elArray[i].style['zIndex']);
				}
				if(window.getComputedStyle){
					currentIndex = parseInt(document.defaultView.getComputedStyle(elArray[i],null).getPropertyValue('z-index'));
				}
				if(!isNaN(currentIndex) && currentIndex > highestIndex){ highestIndex = currentIndex; }
			}
			return(highestIndex+1);
		},
		create : function()
		{
			var highestZindex = myApiModal.getZindex();
			var myApiModalEl = new Element('div', {
				'styles': {
				  'display': 'block',
				  'z-index': highestZindex + 1,
				  'opacity': 0
				},
				'class': 'generic_dialog',
				'id': 'fb-modal'
			});
			
			var html  = '<div class="generic_dialog_popup" style="top: 50px;  z-index:'+(highestZindex+1)+';">';
				html += '	<table class="pop_dialog_table" id="pop_dialog_table" style="width: 750px;">'; 
				html += '		<tbody>'; 
				html += '			<tr id="pop_top">'; 
				html += '				<td class="pop_topleft"></td>'; 
				html += '				<td class="pop_border pop_top"></td>';
				html += '				<td class="pop_topright"></td>'; 
				html += '			</tr>'; 
				html += '			<tr>'; 
				html += '				<td class="pop_border pop_side"></td>'; 
				html += '				<td id="pop_content" class="pop_content">'; 
				html += '					<div class="dialog_title" id="dialog_title"></div>'; 
				html += '					<div class="dialog_content">'; 
				html += '						<div class="dialog_summary" id="dialog_summary">';
				html += '                        	<!-- Header -->';      
				html += '                        </div>';
				html += '						<div class="dialog_body" id="dialog_body"> ';
				html += '							<!-- Content -->';	 
				html += '						</div>';
				html += '						<div class="dialog_buttons" id="dialog_buttons">';
				html += '							<table class="myapi"> <tr><td align="left"><input type="button" value="Close" name="close" class="myapicancel" id="fb-close" onClick="myApiModal.close();" /></td></tr> </table>';
				html += '						</div>'; 
				html += '					</div>'; 
				html += '				</td>'; 
				html += '				<td class="pop_border pop_side"></td>'; 
				html += '			</tr>'; 
				html += '			<tr id="pop_bottom">'; 
				html += '				<td class="pop_bottomleft"></td>'; 
				html += '				<td class="pop_border pop_bottom"></td>'; 
				html += '				<td class="pop_bottomright"></td>'; 
				html += '			</tr>'; 
				html += '		</tbody>'; 
				html += '	</table>'; 
				html += '</div>';
			try{
				$('fb-modal').remove();
			}catch(e){}
			myApiModalEl.set('html',html);
			
			document.getElementsByTagName("body")[0].style.zIndex = highestZindex+2;
			 
			myApiModalEl.injectTop(document.getElementsByTagName("body")[0]);		
			$('fb-modal').opacityFx = new Fx.Tween('fb-modal',{duration: 500, property: 'opacity'});
		},
		setHeight : function(){
			var maxHeight = Math.max(100 , window.getSize().y - $('dialog_title').getSize().y - $('dialog_buttons').getSize().y - $('pop_top').getSize().y - $('pop_bottom').getSize().y  - $('dialog_body').getStyle('padding-top').toInt() - $('dialog_body').getStyle('padding-bottom').toInt() - 100); //80 is twice the shadow size and 100 is tiwce the top margin.
			$('dialog_body').setStyle('max-height',maxHeight);
		},
		close : function()
		{
			window.removeEvent('resize');
			$('fb-modal').opacityFx.start(0).chain(function(){
				$('fb-modal').dispose();
			});	
		}
	}
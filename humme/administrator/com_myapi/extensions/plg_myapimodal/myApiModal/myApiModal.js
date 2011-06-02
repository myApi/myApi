// JavaScript Document
var myApiModal = {
		open : function(title,subheader,content,buttons){
			myApiModal.create();
			
			$('dialog_title').setHTML(title);
			if(subheader == null)
				$('dialog_summary').remove();
			else
				$('dialog_summary').setHTML(subheader);
			$('dialog_body').setHTML(content);
			
			if (typeof buttons != 'undefined'){
				buttonsHTML = '<table class="myapi"><tr>'; 
				for(index in buttons){
					buttonsHTML += '<td width="50" align="left"><input type="button" value="'+buttons[index]['name']+'" name="'+buttons[index]['name']+'" onClick="'+buttons[index]['js']+'" /></td>';
				}
				buttonsHTML += '</tr></table>';
				$('dialog_buttons').setHTML(buttonsHTML);
			}
			FB.XFBML.parse(document.getElementById('pop_dialog_table'));
			
			
			$('fb-modal').opacityFx.start(1);
		},
		create : function()
		{
			higestZindex = 1;
			$$('*').each( function(element){         
				if (element.getStyle('z-index').toInt() > higestZindex){
					higestZindex = element.getStyle('z-index').toInt()
				}
			});
			higestZindex++;
			
			var myApiModal = new Element('div', {
				'styles': {
				  'display': 'block',
				  'z-index': higestZindex,
				  'opacity': 0
				},
				'class': 'generic_dialog',
				'id': 'fb-modal'
			});
			
			var html  = '<div class="generic_dialog_popup" style="top: 50px;">';
				html += '	<table class="pop_dialog_table" id="pop_dialog_table" style="width: 750px;">'; 
				html += '		<tbody>'; 
				html += '			<tr>'; 
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
				html += '			<tr>'; 
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
			myApiModal.setHTML(html);
			myApiModal.injectInside($(document.body));
			$('fb-modal').opacityFx = new Fx.Style('fb-modal','opacity',{duration: 500});
		},
		close : function()
		{
			$('fb-modal').opacityFx.start(0).chain(function(){
				$('fb-modal').remove();
			});	
		}
	}
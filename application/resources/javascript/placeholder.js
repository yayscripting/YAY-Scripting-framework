/**
 * 
 * If this file is beeing included into a project, all placeholder-attributes will be replaced by a javascript alternative. This is for backwards compatibility with older browsers.
 *  
 */

window.addEvent('domready', function() {
	
    	if(('placeholder' in document.createElement('input')) === false) {
		
		$$('input[placeholder][type], textarea[placeholder]').each(function(item, index) {
			
			if (item.tagName.toLowerCase() == 'textarea' || item.type.toLowerCase() != 'password') {
				
				// add events
				item.addEvents( {
				
					'focus': function() {
						
						if (this.value == this.getAttribute('data-placeholder')) {
							
							this.style.color = 'black',
							this.value = ''
							
						}
						
					},
					'blur': function() {
						
						if (this.value == '') {
							
							this.style.color = '#6d6d6d',
							this.value = this.getAttribute('data-placeholder')
							
						}
						
					}
				
				} );
				
				// append default status
				item.style.color = '#6d6d6d';
				item.value = item.getAttribute('placeholder');
				item.setAttribute('data-placeholder', item.getAttribute('placeholder'));
				item.setAttribute('placeholder', '');
				
			} else {
				
				
				if (navigator.userAgent.indexOf('MSIE 8.0') == -1) {
				
					item.addEvents( {
					
						'focus': function() {
							
							if (this.value == this.getAttribute('data-placeholder')) {
								
								this.setAttribute('type', 'password');
								this.style.color = 'black',
								this.value = ''
								
							}
							
						},
						'blur': function() {
							
							if (this.value == '') {
								
								this.setAttribute('type', 'text');
								this.style.color = '#6d6d6d',
								this.value = this.getAttribute('data-placeholder')
								
							}
							
						}
					
					} );
					
					// append default settings
					item.setAttribute('type', 'text');
					item.style.color = '#6d6d6d',
					item.setAttribute('data-placeholder', item.getAttribute('placeholder'));
					item.value = item.getAttribute('placeholder')
					item.setAttribute('placeholder', '');
					
				}
				
			}
			
		});
	
	}
	
});
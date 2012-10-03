/**
 * 
 * If this file is beeing included into a project, inputs with attributes type=file & multiple=multiple will be changed into a fancy multiupload script.
 *  
 */


// wait until ready
window.addEvent('domready', function(){
	
	// get all multiupload-elements
	var elements = $$('input[type=file][multiple=multiple]');
	
	if (elements.length > 0) {
		
		elements.each(function(el, i) {
			
			// assign handler
			el.addClass('multipleUpload_'+i);
			
			// assign list
			var list = new Element('div');
			list.addClass('multipleUploadList multipleUploadList_'+i);			
			list.inject(el, 'after');
			
			// changed element
			var handleUpload = function() {
				
				// shorthand for other functions
				var el = this;
				
				// get all files
				var files = el.files;
				var length = typeof(files) == 'undefined' ? 1 : files.length;
				
				// Should be an IE-fix, but IE decided to not support their own standards.
				if (typeof(files) == 'undefined') {
					
					//var activeX = new ActiveXObject("Scripting.FileSystemObject");
					var size = 0;//activeX.getFile(el.value).size;
					var name = el.value.substr(el.value.lastIndexOf('\\') + 1);
					
					files = [
						{
							'fileSize': size,
							'fileName': name
						}
					];
					
				}
								
				for (var j = 0; j < length; j++) {
					
					var file = files[j];
					
					if (typeof(file.fileName) == 'undefined')
						file.fileName = file.name;
						
					if (typeof(file.fileSize) == 'undefined')
						file.fileSize = file.size;
					
					var tmp_id = el.getAttribute('name').replace('[]', '')+ '['+file.fileName.replace('.', '_')+'_'+file.fileSize+']';
					
					// deleted?
					var deleted = $$('input[type=hidden][name='+tmp_id+']');
					if (deleted.length > 0) {
						
						deleted.each(function(inputElement, index){ inputElement.destroy(); });
						$$('span[data-file='+tmp_id+']')[0].setStyle('display', null);
						continue;
						
					}
					
					// duplicate value?
					var duplicate = $$('span[data-file='+tmp_id+']');
					if(duplicate.length > 0) {
						
						continue;
						
					}
					
					// add to span
					var span = new Element('span');
					span.set('html', file.fileName);
					span.set('data-file', tmp_id);
					
					// add delete-link
					var anchor = new Element('a');
					anchor.set('html', '<img src="/application/resources/image/icons/delete.png" />');
					anchor.addEvent('click', function() {
						
						// get name
						var name = this.parentNode.get('data-file');
						
						// create 'deleted' input.
						var del = new Element('input', {'type':'hidden', 'value':'1'});
						del.set('name', name);
						del.inject(el, 'before');
						
						// delete span
						this.parentNode.setStyle('display', 'none');
						
					})
					
					anchor.inject(span, 'top');
					span.adopt(new Element('br'));
					list.adopt(span);
					
				}
				
				// new upload-input
				var upload = new Element('input');
				var attrs  = el.attributes;
				
				for (var j = 0; j < attrs.length; j++)
					upload.setAttribute(attrs[j].nodeName, el.getAttribute(attrs[j].nodeName));
					
				
				upload.addEvent('change', handleUpload);
				
				el.setStyle('display', 'none');
				upload.inject(el, 'after');
				
			};
			
			el.addEvent('change', handleUpload);
			
		});
		
	}
	
});
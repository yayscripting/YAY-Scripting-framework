<?php
return array(
	
	'smart_placeholder' => true, // include smart placeholder script to fix placeholder bug on older browsers
	'wysiwyg' => (object) array
	(
	
		'css_files' => array(
			'/application/resources/style/default.css'
		),
		'plugins' => array(
			 'ExtendedFileManager',
			 'Linker',
			 'CharacterMap',
			 'ContextMenu',
			 'ListType',
			 'Stylist',
			 'SuperClean',
			 'TableOperations',
			 'ImageManager',
		),
		'formats' => array(
			'Plain text' => '',
			'Normal'     => 'p',
			'Heading 1'  => 'h1',
			'Heading 2'  => 'h2',
			'Heading 3'  => 'h3',
			'Heading 4'  => 'h4',
			'Heading 5'  => 'h5',
			'Heading 6'  => 'h6',
			'Heading 7'  => 'h7',
		),
		'toolbar' => array(
			array('popupeditor'),
			array('separator','formatblock','bold','italic','underline','strikethrough'),
			array('separator','forecolor',),
			array('separator','subscript','superscript'),
			array('linebreak','separator','justifyleft','justifycenter','justifyright','justifyfull'),
			array('separator','insertorderedlist','insertunorderedlist','outdent','indent'),
			array('separator','inserthorizontalrule','createlink','insertimage','inserttable'),
			array('linebreak','separator'),
			array('separator','killword','clearfonts','removeformat','toggleborders','splitblock','lefttoright', 'righttoleft'),
			array('separator','htmlmode')
		
		),
		'max_image_size' => 2048 * 1024, // in bytes
		'upload' => true // allow uploading new images
	)

);

/** Default(advanced) toolbar: ('showhelp','about' can be added optional)
array (
	array('popupeditor'),
	array('separator','formatblock','fontname','fontsize','bold','italic','underline','strikethrough'),
	array('separator','forecolor','hilitecolor','textindicator'),
	array('separator','subscript','superscript'),
	array('linebreak','separator','justifyleft','justifycenter','justifyright','justifyfull'),
	array('separator','insertorderedlist','insertunorderedlist','outdent','indent'),
	array('separator','inserthorizontalrule','createlink','insertimage','inserttable'),
	array('linebreak','separator','undo','redo','selectall','print'),
	array('separator','killword','clearfonts','removeformat','toggleborders','splitblock','lefttoright', 'righttoleft'),
	array('separator','htmlmode')
)

/** Less advanced toolbar:
array(
	array('popupeditor'),
	array('separator','formatblock','bold','italic','underline','strikethrough'),
	array('separator','forecolor',),
	array('separator','subscript','superscript'),
	array('linebreak','separator','justifyleft','justifycenter','justifyright','justifyfull'),
	array('separator','insertorderedlist','insertunorderedlist','outdent','indent'),
	array('separator','inserthorizontalrule','createlink','insertimage','inserttable'),
	array('linebreak','separator'),
	array('separator','killword','clearfonts','removeformat','toggleborders','splitblock','lefttoright', 'righttoleft'),
	array('separator','htmlmode')
)

 */
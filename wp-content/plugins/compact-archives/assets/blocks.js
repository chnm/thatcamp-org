/* WPB Compact Archives Block */
( function( blocks, editor, element, components ) {
		var el = element.createElement;
    var RawHTML = element.RawHTML;
		var InspectorControls = editor.InspectorControls;
		var RichText = editor.RichText;  
		var SelectControl = components.SelectControl;

		blocks.registerBlockType( 'wpb-compact-archive/wpb-compact-archive-block', {
				title: wpbca_block_vars.plugin_name,
				icon: 'editor-justify',
				category: 'common',
				attributes: {
          compact_archive_type: {
            type: 'string',
            default: 'block'
          },
          compact_archive_text_case: {
            type: 'string',
            default: 'capitalize'
          },
          compact_archive_title: {
            type: 'string'              
          }
				},

				edit: function( props ) {
						var compactArchiveType = props.attributes.compact_archive_type;
						var compactArchiveTextCase = props.attributes.compact_archive_text_case;
						var compactArchiveTitle = props.attributes.compact_archive_title;
						var selectorOptions = [
              { label: wpbca_block_vars.label_initial, value: 'initial' },
              { label: wpbca_block_vars.label_block, value: 'block' },
              { label: wpbca_block_vars.label_numeric, value: 'numeric' },
            ];
						var selectorOptionsTextCase = [
              { label: wpbca_block_vars.label_none, value: 'none' },
              { label: wpbca_block_vars.label_capitalize, value: 'capitalize' },
              { label: wpbca_block_vars.label_uppercase, value: 'uppercase' },
            ];          

						function onSelectControlChange( selectValue ) {
							props.setAttributes( { compact_archive_type: selectValue } );
						}
          
						function onSelectControlChangeCase( selectValue ) {
							props.setAttributes( { compact_archive_text_case: selectValue } );
						}
          
						function onChangeContent( newContent ) {
							props.setAttributes( { compact_archive_title: newContent } );
						}          
          
            function renderBlockEditContent( compactArchiveType, compactArchiveTextCase ) {
              var returnText = ( compactArchiveType === 'initial' ? wpbca_block_vars.ca_initial : ( compactArchiveType === 'block' ? wpbca_block_vars.ca_block : wpbca_block_vars.ca_numeric ) ),
                  elemStyle = ( compactArchiveTextCase === 'none' ? '' : ( compactArchiveTextCase === 'capitalize' ? '  style="text-transform: capitalize;"  ' : ' style="text-transform: uppercase;" ' ) );
              return ( '<ul ' + elemStyle + '>' + returnText + '</ul>' );
            }

						return [
              el(
                  InspectorControls,
                  { 
                    key : 'controls' 
                  },
                  el(
                    'div',
                    { className : 'wpb-compact-archives-block-type-dropdown' },
                    el(
                      'label',
                      {},
                      wpbca_block_vars.label_sel_archive_type + ':'
                    ),
                    el(
                      SelectControl,
                      {
                        label: '',
                        onChange: onSelectControlChange,
                        options: selectorOptions,
                        value: compactArchiveType
                      }
                    )               
                  ),
                  el(
                    'div',
                    { className : compactArchiveType === 'block' ? 'wpb-compact-archives-block-type-dropdown-case' : 'wpb-compact-archives-block-type-dropdown-case hide' },
                    el(
                      'label',
                      {},
                      wpbca_block_vars.label_sel_archive_type_case + ':'
                    ),
                    el(
                      SelectControl,
                      {
                        label: '',
                        onChange: onSelectControlChangeCase,
                        options: selectorOptionsTextCase,
                        value: compactArchiveTextCase
                      }
                    )               
                  )                
                ),
              el(
                  RichText,
                  {
                      key: 'richtext',
                      className: 'wpb-compact-archive-block-title',
                      onChange: onChangeContent,
                      value: compactArchiveTitle,
                      placeholder: wpbca_block_vars.placeholder,
                      keepPlaceholderOnFocus: true,
                      formattingControls : [ 'bold', 'italic', 'link' ]
                  }
              ),              
              el(
                'div',
                {
                  key: 'compactarchiveblock',
                  className: 'wpb-compact-archive-block',
                  style : {}
                },
                el(
                  RawHTML,
                  null,
                  renderBlockEditContent( compactArchiveType, compactArchiveTextCase )
                )
              )              
						];
				},

				save: function( props ) {
						return null;
				}
    } );
}(
    window.wp.blocks,
    window.wp.editor,
    window.wp.element,
		window.wp.components
) );
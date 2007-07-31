{default attribute_base='ContentObjectAttribute' html_class='full'}

{let data_text=cond( is_set( $#collection_attributes[$attribute.id] ), $#collection_attributes[$attribute.id].data_text, $attribute.content )}

{def $ref=wrap_user_func( 'getServerVariable', array( 'HTTP_REFERER' ) )}
{def $err=false
     $not_numeric=not( is_numeric( $data_text ) )
     $is_empty=eq( $data_text, '' )
     $is_required=eq( $attribute.is_required, 1 )
     $from_hp=or( eq( $ref, false ), not( ezhttp( 'from_little_form' ) ) )
     $invalid_length=or( ne( count( $data_text ), 5 ), ne( count( $data_text ), 10 ) )
}

{if or( gt( count( $data_text ), 5 ), gt( count( $data_text ), 10 ) )}{def $not_numeric=false()}{/if}

{*
      $invalid_length=$#collection_attributes[$attribute.id].contentobject_attribute.has_validation_error
      $invalid_length=or( ne( count( $data_text ), 5 ), ne( count( $data_text ), 10 ) ) *}

{* if eq( $not_numeric, true)}NAN<hr>{/if *}

{if and( $from_hp, $is_required, or( $not_numeric, $invalid_length, $is_empty ))}{def $err=true}{/if}
{if ne( ezhttp('dovalidate'), 1)} {def $supress_validation=true} {/if}
<div id="{$attribute.id}optionsection" class="{if and(ne($supress_validation, true), eq( $err, true))}option_error{else}option_label{/if}"><label id="{$attribute.id}optionlabel" class="{if and(ne($supress_validation, true), eq( $err, true))}option_text_error{/if}">{if $attribute.is_required}<span class="field_error_message">*&nbsp;</span>{/if}{$attribute.contentclass_attribute_name}</label><input class="{eq( $html_class, 'half' )|choose( 'box', 'halfbox' )}{if eq($attribute.is_required, 1)} validate_zip{/if}" onchange="FilterZipInput(this);" type="text" size="70" name="{$attribute_base}_bcpostalcode_data_text_{$attribute.id}" value="{$data_text|wash( xhtml )}" />

<div class="form_field_error_message" id="{$attribute_base}_bcpostalcode_data_text_{$attribute.id}_error" {if or(eq( $supress_validation, true ),ne( $err, true))} onchange="FilterZipInput(this);" style="display:none;"{/if}>Please provide a {$attribute.contentclass_attribute_name|downcase()} in the format 12345 or 12345-1234</div>

{* $#collection_attributes[$attribute.id].contentobject_attribute|attribute(show,1) *}
{* $#collection_attributes[$attribute.id]|attribute(show,2) *}

</div>

{* $#collection_attributes[$attribute.id].data_text *}
{* $attribute|attribute(show,1) *}
{* if eq($attribute.content,'')}<span style="color:red;">* Input Required</span>{/if *}
{* <div style="color:red;">{$attribute|attribute(show,1)}</div> *}

{/let}
{/default}

<?php
/**
 * Edit/Create SlideDeck form
 * 
 * SlideDeck Pro for WordPress 1.3.72 - 2011-03-30
 * Copyright (c) 2011 digital-telepathy (http://www.dtelepathy.com)
 * 
 * BY USING THIS SOFTWARE, YOU AGREE TO THE TERMS OF THE SLIDEDECK 
 * LICENSE AGREEMENT FOUND AT http://www.slidedeck.com/license. 
 * IF YOU DO NOT AGREE TO THESE TERMS, DO NOT USE THE SOFTWARE.
 * 
 * More information on this project:
 * http://www.slidedeck.com/
 * 
 * Full Usage Documentation: http://www.slidedeck.com/usage-documentation 
 * 
 * @package SlideDeck
 * @subpackage SlideDeck Pro for WordPress
 * 
 * @author digital-telepathy
 * @version 1.3.72
 * 
 * @uses slidedeck_action()
 * @uses slidedeck_url()
 * @uses slidedeck_dir()
 * @uses slidedeck_show_message()
 * @uses wp_nonce_field()
 */
?>
<div class="wrap" id="slidedeck_form">
	<div id="icon-edit" class="icon32"></div><h2><?php echo "create" == $form_action ? "Add New SlideDeck" : "Edit SlideDeck"; ?></h2>
    
    <?php echo slidedeck_show_message(); ?>
    
	<form action="" method="post" id="slidedeck_update_form">
	    <?php function_exists( 'wp_nonce_field' ) ? wp_nonce_field( 'slidedeck-for-wordpress', 'slidedeck-' . $form_action . '_wpnonce' ) : ''; ?>
		<input type="hidden" name="dynamic" value="0" />
		<input type="hidden" name="action" value="<?php echo $form_action; ?>" id="form_action" />
		<input type="hidden" name="gallery_id" value="<?php echo $slidedeck['gallery_id']; ?>" id="slidedeck_gallery_id" />
		<?php if( isset( $slidedeck['id'] ) && !empty( $slidedeck['id'] ) ): ?>
			<input type="hidden" name="id" value="<?php echo $slidedeck['id']; ?>" id="slidedeck_id" />
		<?php endif; ?>
		
		<div class="metabox-holder has-right-sidebar">
			<div class="inner-sidebar">
				<div id="slidedeck-options" class="postbox">
					<h3 class="hndle">SlideDeck Options</h3>
					<div class="inside">
						<?php if( $form_action != "create" ): ?>
							<div id="slidedeck-preview"><div class="ajax-masker"></div><a href="<?php echo admin_url('admin-ajax.php'); ?>?action=slidedeck_preview&preview_w=900px&preview_h=370px&slidedeck_id=<?php echo $slidedeck['id']; ?>&width=920&height=570&first_preview" class="thickbox button" onclick="closePreviewWatcher();">Preview SlideDeck</a></div>
						<?php else: ?>
							<div id="slidedeck-preview"><a href="javascript:void(null);" title="You must save first to preview" class="button disabled">Preview SlideDeck</a></div>
						<?php endif; ?>
						<div class="misc-pub-section">
							<label style="display:inline;"><input type="checkbox" name="slidedeck_options[autoPlay]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'autoPlay' ) == 'true' ? ' checked="checked"' : ''; ?>> Autoplay</label>
							<label style="display:inline;"><input type="text" name="slidedeck_options[autoPlayInterval]" value="<?php echo intval( slidedeck_get_option( $slidedeck, 'autoPlayInterval' ) ) / 1000; ?>" size="1" /> seconds per slide</label>
							<label><input type="checkbox" name="slidedeck_options[cycle]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'cycle' ) == 'true' ? ' checked="checked"' : ''; ?> /> Loop SlideDeck</label>
                            <label><input type="checkbox" name="slidedeck_options[activeCorner]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'activeCorner' ) == 'true' ? ' checked="checked"' : ''; ?> /> Show "Active Corner" Indicator</label>
                            <label><input type="checkbox" name="slidedeck_options[keys]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'keys' ) == 'true' ? ' checked="checked"' : ''; ?> /> Allow Keyboard Navigation</label>
                            <label><input type="checkbox" name="slidedeck_options[scroll]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'scroll' ) == 'true' ? ' checked="checked"' : ''; ?> /> Allow Scroll Wheel Navigation</label>
                            <label><input type="checkbox" name="slidedeck_options[continueScrolling]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'continueScrolling' ) == 'true' ? ' checked="checked"' : ''; ?> /> Allow Scrolling Past Vertical Slides</label>
						</div>
						<div class="misc-pub-section">
                            <label><input type="checkbox" name="slidedeck_options[useNewVertical]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'useNewVertical' ) == 'true' ? ' checked="checked"' : ''; ?> /> Use New Vertical Slide HTML Structure</label>
                            <em>Use the new DL based HTML structure for vertical slides. <strong>Note:</strong> May not work with older custom or legacy skins.</em>
						</div>
						<div class="misc-pub-section">
                            <label><input type="checkbox" name="slidedeck_options[hideSpines]" value="true"<?php echo slidedeck_get_option( $slidedeck, 'hideSpines' ) == 'true' ? ' checked="checked"' : ''; ?> /> Hide Slide Title Bars</label>
                            <em><strong>Note:</strong> not all skins work well without title bars</em>
						</div>
						<div class="misc-pub-section"><label>Animation Speed: 
							<select name="slidedeck_options[speed]">
								<?php $speeds = array( 250, 500, 1000, 1500, 2000 ); ?>
								<?php foreach( (array) $speeds as $speed ): ?>
									<option value="<?php echo $speed; ?>"<?php echo slidedeck_get_option( $slidedeck, 'speed' ) == $speed ? ' selected="selected"' : '' ;?>><?php echo $speed; ?></option>
								<?php endforeach; ?>
							</select>
						</label></div>
						<div class="misc-pub-section"><label>Choose Skin:
                            <select name="skin" class="select-wide" id="slide-skin">
                                <?php foreach( (array) $skins as $skin ): ?>
                                    <?php if( $skin['meta']['Skin Type'] != 'dynamic' ): ?>
                                    <option value="<?php echo $skin['slug']; ?>"<?php echo $skin['slug'] == $slidedeck['skin'] ? ' selected="selected"' : ''; ?>><?php echo $skin['meta']['Skin Name']; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </label></div>
                        <div class="misc-pub-section misc-pub-section-last"><label>Start SlideDeck on: 
							<select name="slidedeck_options[start]" class="select-wide" id="slide-start">
								<?php foreach( (array) $slides as $slide ): ?>
									<option value="<?php echo $slide['slide_order']; ?>"<?php echo slidedeck_get_option( $slidedeck, 'start' ) == $slide['slide_order'] ? ' selected="selected"' : ''; ?>><?php echo empty($slide['title']) ? "Slide " . $slide['slide_order'] : $slide['title']; ?></option>
								<?php endforeach; ?>
							</select>
						</label></div>
					</div>
					<div id="major-publishing-actions" class="submitbox">
						<?php if( $form_action != "create" ): ?>
							<div id="delete-action">
								<a href="<?php echo wp_nonce_url( slidedeck_action() . '&action=delete&id=' . $slidedeck['id'], 'slidedeck-delete' ); ?>" class="submitdelete deletion">Delete SlideDeck</a>
							</div>
						<?php endif; ?>
						
						<div id="publishing-action">
							<input type="submit" class="button-primary" value="<?php echo 'create' == $form_action ? 'Save SlideDeck' : 'Update'; ?>" style="float:right;" />
						</div>
						<div class="clear"></div>
					</div>
				</div>
				
				<div id="re-order-slides" class="postbox">
					<h3 class="hndle">Re-order Slides</h3>
					<div class="inside">
						<p>Re-order the slides in this SlideDeck<br /><em>Slide editor order will change after saving.</em></p>
						<ul class="ui-sortable slide-order">
							<?php $count = 1; ?>
							<?php foreach( (array) $slides as $slide ): ?>
								<li><a href="#slide_editor_<?php echo $count; ?>" class="hndle" id="hndle_for_slide_editor_<?php echo $count; ?>"><?php echo empty( $slide['title'] ) ? "Slide " . $count : $slide['title']; ?></a></li>
								<?php $count++; ?>
							<?php endforeach; ?>
						</ul>
						<div id="add-another-slide"><div class="ajax-masker"></div><a href="<?php echo admin_url('admin-ajax.php'); ?>?action=slidedeck_add_slide" id="btn_add-another-slide" class="preview button">Add Another Slide</a></div>
					</div>
				</div>
				
				<?php if( isset( $slidedeck['id'] ) && !empty( $slidedeck['id'] ) ): ?>
					<div id="get-slidedeck-template-snippet" class="postbox">
						<h3 class="hndle">Theme Code Snippet</h3>
						<div class="inside">
							<p>Want to place this SlideDeck in your WordPress theme template? Define the dimensions you want and copy-and-paste this in the appropriate theme file.</p>
							<textarea cols="20" rows="5" id="slidedeck-template-snippet" readonly="readonly">&lt;?php slidedeck( <?php echo $slidedeck['id']; ?>, array( 'width' => '100%', 'height' => '300px' ) ); ?></textarea>
							<div class="misc-pub-section misc-pub-section-last">
								<label>Dimensions:</label> 
								<input type="text" name="width" value="100%" id="template_snippet_w" /> 
								<input type="text" name="height" value="300px" id="template_snippet_h" />
							</div>
						</div>
					</div>
				<?php endif; ?>
                
                <div class="follow-twitter callout-button"><p>For tips, tricks &amp; discounts</p><a href="http://twitter.com/slidedeck" target="_blank" class="button"><span class="inner"><img src="<?php echo slidedeck_url( '/images/twitter.png' ); ?>" /> Follow Us on Twitter</span></a></div>
                <div class="view-screencasts callout-button"><p>For how-to's and troubleshooting</p><a href="http://www.slidedeck.com/screencasts/" target="_blank" class="button"><span class="inner"><img src="<?php echo slidedeck_url( '/images/youtube.png' ); ?>" /> View Our Screencasts</span></a></div>
                <div class="bug-report callout-button"><p>Help us squash the bugs</p><a href="http://www.getsatisfaction.com/slidedeck/topics" target="_blank" class="button"><span class="inner"><img src="<?php echo slidedeck_url( '/images/bug.png' ); ?>" /> Report a bug for SlideDeck</span></a></div>
			</div>
			
			<div class="editor-wrapper">
				<div class="editor-body">
					<div id="titlediv">
						<div id="titlewrap">
							<label for="name">Name this SlideDeck</label>
							<input type="text" name="title" size="40" maxlength="255" value="<?php echo !empty( $slidedeck['title'] ) ? $slidedeck['title'] : 'My SlideDeck'; ?>" id="title" />
						</div>
					</div>
				
					<div class="slides">
						<?php $count = 1; ?>
						<?php foreach( (array) $slides as $slide ): ?>
							<?php include( slidedeck_dir( '/views/_edit-slide.php' ) ); ?>
							<?php $count++; ?>
						<?php endforeach; ?>
					</div>
		
				</div>
			</div>
		</div>
	</form>
</div>

<div style="display:none;"><div id="preview_content"></div></div>
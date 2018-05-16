<?php
/*
Plugin Name: Raw HTML Snippets
Plugin URI: http://theandystratton.com
Version: 1.1.3
Author: theandystratton
Author URI: http://theandystratton.com
Description: Uses a shortcode to give users multiple methods of properly inserting RAW HTML content without disabling core WordPress content filters.
*/
add_shortcode('raw_html_snippet', 'rhs_raw_html_snippet_shortcode');
function rhs_raw_html_snippet_shortcode( $atts, $content = '' ) {
	extract( shortcode_atts(array(
		'id' => false
	), $atts) );
	
	if ( !isset($id) || !$id ) 
		return '';
	
	$snippet = get_option('rhs_snippet-' . $id);
	
	return $snippet;
}

add_action('admin_menu', 'rhs_raw_html_snippet_admin_menu');
function rhs_raw_html_snippet_admin_menu() {
	add_submenu_page('options-general.php', 'Raw HTML Snippets', 'Raw HTML Snippets', 'edit_posts', 'raw-html-snippets', 'rhs_raw_html_snippet_settings');
}

function rhs_raw_html_snippet_settings() {
	
	if ( isset($_GET['edit']) && $_GET['edit'] )
		return rhs_raw_html_snippet_editor();
		
	if ( isset($_GET['add']) && $_GET['add'] )
		return rhs_raw_html_snippet_add();
	
	$errors = array();
	$clean = array();
	
	if ( isset($_GET['rhs_del']) && $_GET['rhs_del'] && wp_verify_nonce($_GET['rhs_nonce'], 'rhs_delete') ) {
		delete_option('rhs_snippet-' . $_GET['rhs_del']);
		$snippet_list = get_option('rhs_snippet_list');
		if ( is_array($snippet_list) && in_array($_GET['rhs_del'], $snippet_list) ) {
			$snippet_list = array_diff( $snippet_list, array( $_GET['rhs_del'] ) );
			update_option('rhs_snippet_list', $snippet_list);
			$success = 'Snippet with ID &quot;' . esc_html($_GET['rhs_del']) . '&quot; successfully deleted.';
		}
	}

	
	$snippet_list = get_option('rhs_snippet_list');
	if ( !is_array($snippet_list) )
		$snippet_list = array();
	
?>
<div class="wrap">
	<h2>Manage Raw HTML Snippets</h2>
	<p>
		Create and manage your HTML snippets here. Name them with an id (letters, numbers, and dashes only) and then
		call them within your content via shortcode: <br />
		<code>[raw_html_snippet id="my-unique-id"]</code>
	</p>
	<p>
		This will use maintain core WordPress content filtering and autoformatting for all other elements within your content while 
		allowing you to easily insert managed RAW HTML.
	</p>
	<p>
		<strong>WARNGING:</strong> This does not filter your HTML for errors or
		for malicious scripts. Use at your own risk.
	</p>
	
	<?php if ( count($snippet_list) > 0 ) : ?>
	
	
	<form method="get" action="">
		<p class="alignright">
			<input type="hidden" name="page" value="raw-html-snippets" />
			<input type="hidden" name="add" value="1" />
			<input type="submit" class="button-primary" value="Add a New Raw HTML Snippet &raquo;" />
		</p>
	</form>
	
	<h2>Your Snippet Library</h2>
	
	<table class="widefat fixed">
	<thead>
	<tr>
		<th>Snippet Name</th>
		<th>Shortcode</th>
		<th>Actions</th>
	</tr>
	</thead>
	<tbody>
	
	<?php foreach ( $snippet_list as $snippet_id ) : ?>
	<tr>
		<td>
			<?php echo esc_html($snippet_id);?>
			<div class="row-actions">
				<a href="?page=raw-html-snippets&amp;edit=<?php echo rawurlencode($snippet_id); ?>">Edit</a> | 
				<span class="trash"><a class="submitdelete" onclick="return confirm('Are you sure you want to delete this snippet?');" href="?page=raw-html-snippets&amp;rhs_nonce=<?php echo esc_attr(wp_create_nonce('rhs_delete')); ?>&amp;rhs_del=<?php echo esc_attr($snippet_id); ?>">Delete</a></span>
			</div>
		</td>
		<td>
			<code>[raw_html_snippet id="<?php echo esc_html($snippet_id); ?>"]</code>
		</td>
		<td>
			<a href="?page=raw-html-snippets&amp;edit=<?php echo rawurlencode($snippet_id); ?>">Edit Snippet</a> | 
			<span class="trash"><a onclick="return confirm('Are you sure you want to delete this snippet?');" href="?page=raw-html-snippets&amp;rhs_nonce=<?php echo esc_attr(wp_create_nonce('rhs_delete')); ?>&amp;rhs_del=<?php echo rawurlencode($snippet_id); ?>">Delete Snippet</a></span>
		</td>
	</tr>
	<?php endforeach; ?>
	
	</tbody>
	</table>
	
	<?php else : ?>
		<h2>Your Snippets Library is Empty</h2>
		<p>You have no snippets, please <a href="?page=raw-html-snippets&amp;add=1">please add one</a>.</p>
	<?php endif; ?>
</div>
<?php 
}

function rhs_raw_html_snippet_editor() {
	$snippet_id = $_GET['edit'];
	$errors = array();
	if ( !empty($_POST) && wp_verify_nonce($_POST['rhs_nonce'], 'rhs_nonce') ) {
		$snippet = stripslashes($_POST['snippet_code']);
		if ( empty($snippet) ) 
			$errors[] = 'Enter some HTML for this snippet.';
		if ( count($errors) <= 0 ) {
			update_option('rhs_snippet-' . $snippet_id, $snippet);
			$success = 'Your changes have been saved.';
		}
	}
	$snippet = get_option('rhs_snippet-' . $snippet_id);
	$clean = array(
		'snippet_code' => $snippet
	);
?>
<div class="wrap">
	<h2>Edit Raw HTML Snippet: &quot;<?php echo esc_html($snippet_id); ?>&quot;</h2>
	<p><a href="?page=raw-html-snippets">&laquo; Back to main page</a></p>
	<form method="post" action="">
		
		<?php if ( count($errors) > 0 ) : ?>
		<div class="message error"><?php echo wpautop(implode("\n", $errors)); ?></div>
		<?php endif; ?>
		<?php if ( isset($success) && !empty($success) ) : ?>
		<div class="message updated"><?php echo wpautop($success); ?></div>
		<?php endif; ?>
		
		<?php wp_nonce_field('rhs_nonce', 'rhs_delete'); ?>
		
		<p><label for="snippet_code">Snippet Code:</label></p>
		<textarea dir="ltr" dirname="ltr" id="snippet_code" name="snippet_code" rows="10" style="font-family:Monaco,'Courier New',Courier,monospace;font-size:12px;width:80%;color:#555;"><?php
			if ( isset($clean['snippet_code']) )
				echo esc_attr($clean['snippet_code']);
		?></textarea>
		
		<p>
			<input type="submit" class="button-primary" value="Save Snippet &raquo;" /> 
			<?php wp_nonce_field('rhs_nonce', 'rhs_nonce'); ?>
			<input type="button" class="button" value="Delete This Snippet" onclick="if ( confirm('Are you sure you want to delete this snippet?') ) window.location = '?page=raw-html-snippets&amp;rhs_del=<?php echo esc_attr($snippet_id); ?>&amp;rhs_nonce=<?php echo esc_attr(wp_create_nonce('rhs_delete')); ?>';" />
		</p>
	</form>	
</div>
<?php
}

function rhs_raw_html_snippet_add() {
	$snippet_list = get_option('rhs_snippet_list');
	if ( !is_array($snippet_list) )
		$snippet_list = array();
		
	$errors = array();
	$clean = array();
	
	if ( !empty($_POST) && wp_verify_nonce($_POST['rhs_nonce'], 'rhs_nonce') ) {

		foreach ( $_POST as $k => $v )
			$clean[$k] = stripslashes($v); 
			
		if ( empty($clean['snippet_id']) ) 
			$errors[] = 'Please enter a unique snippet ID.';
		elseif ( in_array(strtolower($clean['snippet_id']), $snippet_list) )
			$errors[] = 'You have entered a snippet ID that already exists. IDs are NOT case-sensitive.';
		
		if ( empty($clean['snippet_code']) ) 
			$errors[] = 'Enter some HTML for this snippet.';
		
		if ( count($errors) <= 0 ) {
			// save snippet
			$snippet_id = strtolower($clean['snippet_id']);
			$snippet_list[] = $snippet_id;
			update_option('rhs_snippet_list', $snippet_list);
			update_option('rhs_snippet-' . $snippet_id, $clean['snippet_code']);
			$success = 'Your snippet has been saved.';
			$clean = array();
		}
	}
	
?>
<div class="wrap">
	<h2>Add Raw HTML Snippet:</h2>
	
	<p><a href="?page=raw-html-snippets">&laquo; Back to main page</a></p>
		
	<form method="post" action="" style="margin: 1em 0;padding: 1px 1em;background: #fff;border: 1px solid #ccc;">
		
		<?php if ( count($errors) > 0 ) : ?>
		<div class="message error"><?php echo wpautop(implode("\n", $errors)); ?></div>
		<?php endif; ?>
		<?php if ( $success ) : ?>
		<div class="message updated"><?php echo wpautop($success); ?></div>
		<?php endif; ?>
		
		<?php wp_nonce_field('rhs_nonce', 'rhs_nonce'); ?>
		
		<p>
			<label for="snippet_id">Snippet ID:</label>
			<br />
			<input type="text" name="snippet_id" id="snippet_id" size="40" value="<?php
			if ( isset($clean['snippet_id']) ) 
				echo esc_attr($clean['snippet_id']);
		?>" />
		</p>
		
		<p><label for="snippet_code">Snippet Code:</label></p>
		<textarea dir="ltr" dirname="ltr" id="snippet_code" name="snippet_code" rows="10" style="font-family:Monaco,'Courier New',Courier,monospace;font-size:12px;width:80%;color:#555;"><?php
			if ( isset($clean['snippet_code']) )
				echo esc_attr($clean['snippet_code']);
		?></textarea>
		
		<p><input type="submit" class="button-primary" value="Add Snippet &raquo;" />
	</form>	
</div>
<?php	
}

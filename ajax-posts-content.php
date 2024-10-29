<?php
/*
Plugin Name: Ajax Posts Content
Plugin URI: https://wordpress.org/plugins/ajax-posts-content/
Description: Plugin for creating infinite feed posts on a single post page.
Version: 0.1
Author: Popov Vladimir
Author URI: https://novatechno.ru/
License: GPL2
Text Domain: ajax-posts-content
*/


/*Настройки*/
class AjaxPostsContent
{
	private $options;
	public function __construct()
	{
		add_action('admin_menu', array($this, 'apc_add_plugin_page'));
		add_action('admin_init', array($this, 'apc_page_init'));
	}

	/*Добавить в настройки*/
	public function apc_add_plugin_page()
	{
		add_options_page("Ajax PostsContent",__( 'Ajax PostsContent', 'ajax-posts-content' ),'manage_options','apc-settings',array($this, 'apc_create_admin_page'));
	}
	public function apc_create_admin_page()
	{
		$this->options = get_option('apc_option');
			echo '<form method="post" action="options.php">';
				settings_fields('apc_option_group');			
				echo '<table> <caption>'.__( 'Options for Ajax PostContent', 'ajax-posts-content' ).'</caption>';
					
					echo '<tr><td>'.__( 'Only admin (test mode)','ajax-posts-content' ).'</td><td> 
					<input type="checkbox" id="apc_test" name="apc_option[apc_test]" value="'.(boolval($this->options["apc_test"]) ? 'true' : 'false').'"'. checked((boolval($this->options["apc_test"]) ? true : false),true,false). ' />
					</td></tr>';

					echo '<tr><td>'.__( 'Article block', 'ajax-posts-content' ).':</td><td> 
					<input size="35" type="text" id="apc_article_block" name="apc_option[apc_article_block]" value="'.$this->options['apc_article_block'].'" />
					<br>'.__( 'Where to upload new posts? Element selector', 'ajax-posts-content' ).'</td></tr> ';

					echo '<tr><td>'.__( 'Download type', 'ajax-posts-content' ).':</td><td> 
							<select id="apc_type" size="1" width="200" name="apc_option[apc_type] value="'.$this->options['apc_type'].'">
								<option '. ($this->options['apc_type']=='scroll'?'selected':'') .' value="scroll">'.__( 'Scroll', 'ajax-posts-content' ).'</option>
								<option '. ($this->options['apc_type']=='button'?'selected':'') .' value="button">'.__( 'Button', 'ajax-posts-content' ).'</option>
							</select>
						  </td></tr>';

					echo '<tr><td>'.__( 'The queue of posts', 'ajax-posts-content' ).':</td><td> 
							<select id="apc_order" size="1" width="200" name="apc_option[apc_order] value="'.$this->options['apc_order'].'">
								<option '. ($this->options['apc_order']=='new'?'selected':'') .' value="new">'.__( 'New', 'ajax-posts-content' ).'</option>
								<option '. ($this->options['apc_order']=='rand'?'selected':'') .' value="rand">'.__( 'Random', 'ajax-posts-content' ).'</option>
								<option '. ($this->options['apc_order']=='cat'?'selected':'') .' value="cat">'.__( 'In one Category + Random', 'ajax-posts-content' ).'</option>
							</select>
						  </td></tr>';

					echo '<tr><td>'.__( 'Count Loads', 'ajax-posts-content' ).': </td><td> 
					<input min="-1" max="99" type="number" id="apc_сount" name="apc_option[apc_сount]" value="'.(empty($this->options['apc_сount'])?'3':$this->options['apc_сount']).'" />
					<br>'.__( 'Maximum ajax downloads. "-1" - no restrictions', 'ajax-posts-content' ).'</td></tr> ';

					echo '<tr><td>'.__( 'Height from bottom', 'ajax-posts-content' ).': </td><td> 
					<input min="0" max="3000" type="number" id="apc_height" name="apc_option[apc_height]" value="'.(empty($this->options['apc_height'])?'100':$this->options['apc_height']).'" />
					<br>'.__( 'The height from the bottom of the document to start loading (scroll)', 'ajax-posts-content' ).'</td></tr> ';

					echo '<tr><td>'.__( 'Enable on mobile','ajax-posts-content' ).'</td><td> 
					<input type="checkbox" id="apc_mobile" name="apc_option[apc_mobile]" value="'.(boolval($this->options["apc_mobile"]) ? 'true' : 'false').'"'. checked((boolval($this->options["apc_mobile"]) ? true : false),true,false). ' />
					</td></tr>';

					echo '<tr><td>'.__( 'Enable on desktop','ajax-posts-content' ).'</td><td> 
					<input type="checkbox" id="apc_desktop" name="apc_option[apc_desktop]" value="'.(boolval($this->options["apc_desktop"]) ? 'true' : 'false').'"'. checked((boolval($this->options["apc_desktop"]) ? true : false),true,false). ' />
					</td></tr>';

				echo '</table>';


				/*Выбор типов постов где будет работать*/
				echo '<table><caption>'.__( 'Enable for types & Template:', 'ajax-posts-content' ).'</caption>';
					$post_types = get_post_types(array('public'=> true),'names');
						foreach( $post_types as $post_type ){ 			
							echo '<tr> <td>'.__( $post_type, 'ajax-posts-content' ).':</td> <td>
								<input type="checkbox" id="apc_type_'.$post_type.'" name="apc_option[apc_type_'.$post_type.']" value="'.(!empty($this->options["apc_type_".$post_type]) &&boolval($this->options["apc_type_".$post_type]) ? 'true' : 'false').'"'. checked((!empty($this->options["apc_type_".$post_type]) && boolval($this->options["apc_type_".$post_type]) ? 'true' : 'false'),'true',false). ' /></td> <td> {theme_dir}/
								<input size="35" type="text" id="apc_template_'.$post_type.'" name="apc_option[apc_template_'.$post_type.']" value="'.$this->options['apc_template_'.$post_type].'" />
								</td></tr>';
							}
							echo "<tr><td style='width:100%;'>". __( 'If the field is empty, the standard template will be used: "[plugin_girectory]/template/def.php"', 'ajax-posts-content' )."</td></tr>";

				echo '</table>';
				submit_button(''.__( 'SaveSettings', 'ajax-posts-content' ).'');
				echo '</form>';
			?>
			<style>
			tr {margin-top: 5px;display: block;background-color: white;}
			tr td:first-child { padding: 10px 15px ;border: none;background-color: #f6f6f6; width: 200px;font-size: 16px;}
			table caption { background-color: #45a4ba; padding: 10px 20px; color: white; margin: 0; font-size: 16px; font-weight: bold; }
			table {display: inline-block;border: 2px solid #ffffff;vertical-align: top;}
			table select {width: 260px;}
			</style>
			<?php
	}

	/* Регистрируем настройки*/
	public function apc_page_init()
	{
		register_setting('apc_option_group','apc_option');
		add_settings_field('apc_article_block', '','apc-settings','apc_section_1' );
		add_settings_field('apc_сount', '','apc-settings','apc_section_1' );
		add_settings_field('apc_type', '','apc-settings','apc_section_1' );
		add_settings_field('apc_order', '','apc-settings','apc_section_1' );
		add_settings_field('apc_test', '','apc-settings','apc_section_1' );
		add_settings_field('apc_desktop', '','apc-settings','apc_section_1' );
		add_settings_field('apc_mobile', '','apc-settings','apc_section_1' );	
		add_settings_field('apc_height', '','apc-settings','apc_section_1' );	
		//Для каких типов постов добавлять
			$post_types = get_post_types(array('public'=> true),'names');
			foreach( $post_types as $post_type ) {
			  add_settings_field('apc_type_'.$post_type, '','apc-settings','apc_section_2' );
			  add_settings_field('apc_template_'.$post_type, '','apc-settings','apc_section_3' );
			}

		//Стандартные значения tckb
		if(empty(get_option('apc_option')))
		{
			$default_settings = array(
				'apc_article_block' => 'article',
				'apc_сount' => '3',
				'apc_type' => 'scroll',
				'apc_order' => 'cat',
				'apc_test' => '0',
				'apc_type_post' => 'true',
				'apc_desktop' => 'true',
				'apc_mobile' => 'true',
				'apc_mobile' => '100',
				);
			update_option('apc_option',$default_settings, "");
		}
	}
}

if( is_admin() ) $apc_settings_page = new AjaxPostsContent();

////////////////////////

/*Обаботчик*/
function apc_get_content_post(){
	$apc_option = get_option('apc_option');
	$args['post_type'] = get_post_type(sanitize_text_field($_POST['this_id']));
	/*Рандом*/
	if ($apc_option['apc_order']=='rand' || empty($apc_option['apc_order'])) 
			$args['orderby'] = 'rand';
	/*Одинаковые категории + Рандом или новые*/
	elseif($apc_option['apc_order']=='cat' || $apc_option['apc_order']=='new') 
	{
		$cats = get_the_category(sanitize_text_field($_POST['this_id']));
		if ($cats) 
			$args['cat'] = $cats[0]->cat_ID;
		if($apc_option['apc_order']=='cat') $args['orderby'] = 'rand';
	}

	$args['post__not_in'] = explode(",", sanitize_text_field($_POST['not_in']));
	$args['posts_per_page'] = 1;
	//query_posts( $args );
	$query = new WP_Query($args);
		while( $query->have_posts() ): $query->the_post();
			$file_template=$apc_option['apc_template_'.get_post_type(get_the_ID())];
			//Шаблон сушествует - грузим!
			if(!empty($file_template))
				get_template_part(str_replace(".php","",$file_template));
			else
				include 'template/def.php';
			echo('<meta class="apc-meta" content="'.get_the_ID().'">');
		endwhile;
	die();
}
add_action('wp_ajax_loadmore', 'apc_get_content_post');
add_action('wp_ajax_nopriv_loadmore', 'apc_get_content_post');

/*Функция вывода*/
function apc_load_post_ajax(){
	$apc_option = get_option('apc_option');
	require_once ('style.php');
	// Режим темтирования или глобальный режим
	if(is_single() && ((!empty($apc_option['apc_test']) && $apc_option['apc_test']=='true' && is_user_logged_in()) || empty($apc_option['apc_test']))):
	// Выставлено кол-во и открыт правильный тип поста
	if(!empty($apc_option['apc_сount']) && $apc_option['apc_type_'.get_post_type(get_the_ID())]=='true' ): 
	// Включен или мобильный или десктопный режим
	if(($apc_option['apc_mobile']==true && wp_is_mobile()) || ($apc_option['apc_desktop']==true && !wp_is_mobile())):	
	?>
	
	<script>
		jQuery(function($){
			var ajaxurl = '<?=admin_url("admin-ajax.php");?>';
			var type = '<?=$apc_option['apc_type']?>';
			var not_in = '<?=get_the_ID()?>';
			var this_id = '<?=get_the_ID()?>';
			var max_loads = <?=$apc_option['apc_сount']?>;
			var append_block = $('<?=$apc_option['apc_article_block']?>');
			//Кнопки после контента
			if(append_block) {
				append_block.append("<div id='next-post-place'></div><button class='apc-anim' id='apc_load_post'><span>Загрузить ещё</span></button>");
			<?php if($apc_option['apc_type']=='scroll'):?>
				$('#apc_load_post').hide();
				/*Тип загрузки при скроле до низу*/
				$(window).scroll(function(){
					var data = {'action': 'loadmore','not_in': not_in,'this_id':this_id};
					var scrollBottom = $(document).outerHeight(true)-$(window).scrollTop()-$(window).height();

					if( scrollBottom<100 && !$('#apc_load_post').hasClass('loading') && max_loads!=0){
						$.ajax({url:ajaxurl, data:data, type:'POST',
							beforeSend: function( xhr){
								$('#apc_load_post').show().addClass('loading');
							},
						success:function(data){
							if(data) { 
								$('#next-post-place').before("<div id='post-"+max_loads+"'>"+data+"</div>");
								$("#post-"+max_loads).hide().fadeIn(2000);
								max_loads--; 
								not_in +=','+$("meta.apc-meta").last().attr("content");
								$('#apc_load_post').removeClass('loading').hide();
					}}});
					}
				});
			<?php endif;?>

			<?php if($apc_option['apc_type']=='button'):?>
				/*Тип загрузки по кнопке*/
				$('#apc_load_post span').text('Загрузить ещё');
				$('#apc_load_post').click(function(){
					$('#apc_load_post span').text('');
					$('#apc_load_post').addClass('loading');
					var data = {'action': 'loadmore','not_in': not_in,'this_id':this_id};
					$.ajax({url:ajaxurl, data:data, type:'POST',
						success:function(data){
							if(data ) { 
								$('#next-post-place').before("<div class='apc-post' id='post-"+max_loads+"'>"+data+"</div>");
								$("#post-"+max_loads).hide().fadeIn(2000);
								max_loads--; 
								not_in +=','+$("meta.apc-meta").last().attr("content");
								if (max_loads==0) $('#apc_load_post').remove(); 
								$('#apc_load_post').removeClass('loading');
								$('#apc_load_post').find('span').text('Загрузить ещё...');
					}}});
				});
			<?php endif;?>
			}	
		});
	</script>
	<?php endif;endif;endif;
}
add_filter('wp_footer', 'apc_load_post_ajax');
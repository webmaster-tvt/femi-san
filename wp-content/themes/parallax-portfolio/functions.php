<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION

require get_stylesheet_directory() . '/additional-settings.php';


if ( !function_exists( 'parallax_portfolio_scripts' ) ):
    function parallax_portfolio_scripts() {

        $themeVersion = wp_get_theme()->get('Version');
        
        
        wp_enqueue_style( 'parallax-portfolio-style-vars', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/css-vars.css',array(),$themeVersion);
        wp_enqueue_style( 'parallax-portfolio-parent', trailingslashit( get_template_directory_uri() ) . 'style.css', array( 'font-awesome','cww-portfolio-keyboard' ),$themeVersion );
        wp_enqueue_style('parallax-portfolio-style', trailingslashit( get_template_directory_uri() ) . '/style.css',array(), $themeVersion);

        
        wp_enqueue_script( 'parallax-portfolio', trailingslashit(get_stylesheet_directory_uri()).'assets/parallax-portfolio.js', array('jquery'), $themeVersion, true );
    }
endif;
add_action( 'wp_enqueue_scripts', 'parallax_portfolio_scripts' );



if ( !function_exists( 'parallax_portfolio_parent_modify_css' ) ):
    function parallax_portfolio_parent_modify_css() {

        $themeVersion = wp_get_theme()->get('Version');
        wp_deregister_style('cww-portfolio-style-vars');
        wp_enqueue_style('parallax-portfolio-responsive', trailingslashit( get_stylesheet_directory_uri() ) . 'assets/css/responsive-style.css',array(), $themeVersion);
       
        
        
    }
endif;
add_action( 'wp_enqueue_scripts', 'parallax_portfolio_parent_modify_css',20 );





// END ENQUEUE PARENT ACTION


/**
 * Theme Option Default Values
 * 
 * 
 * 
 */ 
add_filter('cww_portfolio_default_theme_options','parallax_portfolio_customizer_defaults');
if( ! function_exists('parallax_portfolio_customizer_defaults')):
    function parallax_portfolio_customizer_defaults(){

        $defaults = array();

        $defaults['cww_home_banner']                    = 0;
        $defaults['cww_header_cta_enable']              = 0;
        $defaults['cww_header_cta_text']                = esc_html__('Contact Now','parallax-portfolio');
        $defaults['cww_header_cta_url']                 = '#';
        $defaults['cww_header_cta_bg']                  = '#6138bd';
        $defaults['cww_header_bg']                      = '#fff';
        $defaults['cww_menu_link_color']                = '#11204d';
        $defaults['cww_menu_link_color_hover']          = '';
        $defaults['cww_icon_fb']                        = '';
        $defaults['cww_icon_insta']                     = '';
        $defaults['cww_icon_twitter']                   = '';
        $defaults['cww_icon_lnkedin']                   = '';
        $defaults['cww_theme_color']                    = '#BC885D';

        $defaults['cww_banner_image']                   = '';
        $defaults['cww_banner_text_sm']                 = esc_html__("Hi There, I'm",'parallax-portfolio');
        $defaults['cww_banner_text_lg']                 = esc_html__('John Doe','parallax-portfolio');
        $defaults['cww_banner_text_sm2']                = esc_html__('based in Los Angeles, USA','parallax-portfolio');
        $defaults['cww_banner_btn_text']                = esc_html__('View My Works','parallax-portfolio');
        $defaults['cww_banner_btn_url']                 = '#';
        $defaults['cww_banner_btn_text_sec']            = esc_html__('Contact Me','parallax-portfolio');
        $defaults['cww_banner_btn_url_sec']             = '#';
        $defaults['cww_banner_bg']                      = 'rgba(108,85,224, 0.1)';
        $defaults['cww_banner_animated_color']          = '#BC885D';

        $defaults['cww_about_title']                    = esc_html__('About Me','parallax-portfolio');
        $defaults['cww_about_sub_title']                = '';
        $defaults['cww_about_image']                    = '';
        $defaults['cww_about_counter_value_first']      = 155;
        $defaults['cww_about_counter_text_first']       = esc_html__('Completed projects','parallax-portfolio');
        $defaults['cww_about_counter_value_sec']        = 120;
        $defaults['cww_about_counter_text_sec']         = esc_html__('Positive reviews','parallax-portfolio');


        $defaults['cww_service_title']                  = esc_html__('What We Offer','parallax-portfolio');
        $defaults['cww_service_sub_title']              = '';
        $defaults['cww_portfolio_title']                = esc_html__('Our Portfolio','parallax-portfolio');
        $defaults['cww_portfolio_sub_title']            = '';
        $defaults['cww_portfolio_post']                 = 0;

        $defaults['cww_service_enable']                 = 1;
        $defaults['cww_portfolio_enable']               = 1;
        $defaults['cww_blog_enable']                    = 1;
        $defaults['cww_contact_enable']                 = 1;
        $defaults['cww_cta_enable']                     = 1;
        $defaults['cww_about_enable']                   = 1;

        $defaults['cww_header_toggle_color']            = '#333';
        

        
        return $defaults;
    }
endif;


add_action( 'after_setup_theme', 'parallax_portfolio_setup' );
if( ! function_exists('parallax_portfolio_setup')):
    function parallax_portfolio_setup(){
        remove_action('cww_portfolio_mobile_menu','cww_portfolio_mobile_menu');
        remove_action('cww_portfolio_nav','cww_portfolio_nav');
    }
endif;


add_action('cww_portfolio_nav','parallax_portfolio_nav');
if(! function_exists('parallax_portfolio_nav')):
	function parallax_portfolio_nav(){
		?>
        <header id="masthead" class="site-header">
			<div class="container cww-flex">
				<?php do_action('cww_portfolio_logo'); ?>
				<div class="menu-wrapp">
					<?php do_action('parallax_portfolio_nav_toggle'); ?>
					<div class="cww-menu-outer-wrapp">
                        <div class="close-wrapp">
                            <button class="button is-text close-icon">
                                <div class="text">
                                    <span><?php esc_html_e('Close','parallax-portfolio')?></span>
                                </div>
                                <div class="burger">
                                    <svg viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-close" data-v-649bbaab="">
                                        <line x1="13.788" y1="1.28816" x2="1.06011" y2="14.0161" stroke="currentColor" stroke-width="1.2" style="opacity: 1; stroke-dasharray: 20, 999px;"></line>
                                        <line x1="1.06049" y1="1.43963" x2="13.7884" y2="14.1675" stroke="currentColor" stroke-width="1.2" style="opacity: 1; stroke-dasharray: 20, 999px;"></line>
                                    </svg>
                                </div>
                            </button>
                        </div>
                        <div class="inner-iemnu-wrapp cww-flex">
						<nav id="site-navigation" class="main-navigation">
                            

							<?php
							wp_nav_menu(
								array(
									'theme_location' 	=> 'menu-1',
									'menu_id'        	=> 'primary-menu',
									'menu_class' 	 	=> 'cww-main-nav',
									'container_class' 	=> 'cww-nav-container'
								)
							);
							?>
						</nav><!-- #site-navigation -->
                        <div class="menu-text-wrapp">
                            <?php 
                            $cww_menu_toggle_title  = get_theme_mod('cww_menu_toggle_title');
                            $cww_menu_toggle_desc   = get_theme_mod('cww_menu_toggle_desc');
                            ?>
                        <h3><?php echo esc_html($cww_menu_toggle_title); ?></h3>
                        <p><?php echo wp_kses_post($cww_menu_toggle_desc); ?></p>
                        <?php do_action('cww_portfolio_social_icons'); ?>
                        
                        </div>
                       
                        </div>
                        <div class="menu-last-focus-item"></div>
						<?php do_action('cww_portfolio_header_button'); ?>
						
                        
					</div>
				</div>
			</div>
		</header><!-- #masthead -->

		
		<?php 
	}
endif;

add_action('parallax_portfolio_nav_toggle','parallax_portfolio_nav_toggle');
if( ! function_exists('parallax_portfolio_nav_toggle')):
    function parallax_portfolio_nav_toggle(){
        ?>
        <button class="button is-text toggle-icon-wrapp mob-toggle-menu-button">
            <div class="open-event">
                <div class="text">
                    <span><?php esc_html_e('Menu','parallax-portfolio')?></span>
                </div>
                <div class="burger">
                    <svg viewBox="0 0 18 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="icon-burger" data-v-649bbaab="">
                        <line x1="18" y1="0.6" y2="0.6" stroke-width="1.2" data-v-649bbaab="" transform="matrix(1,0,0,1,0,0)" style="transform-origin: 0px 0px;"></line>
                        <line x1="18" y1="5.7167" y2="5.7167" stroke-width="1.2" data-v-649bbaab="" transform="matrix(1,0,0,1,0,0)" style="transform-origin: 0px 0px;"></line>
                        <line x1="18" y1="10.8334" y2="10.8334" stroke-width="1.2" data-v-649bbaab="" transform="matrix(1,0,0,1,0,0)" style="transform-origin: 0px 0px;"></line>
                    </svg>
                </div>
            </div>
    </button>
        <?php 
    }
endif;

/**
 * Dynamic styles
 * 
 */
add_action( 'wp_enqueue_scripts', 'parallax_portfolio_dynamic_style' );
if( ! function_exists('parallax_portfolio_dynamic_style')):
    function parallax_portfolio_dynamic_style(){
        ob_start();

        $defaults                   = parallax_portfolio_customizer_defaults();
        $cww_header_toggle_color    = get_theme_mod('cww_header_toggle_color', $defaults['cww_header_toggle_color']); 

        ?>
        header.site-header .open-event{
            color: <?php echo  cww_portfolio_sanitize_color($cww_header_toggle_color);?>;
        }
        header.site-header svg.icon-burger line{
            stroke: <?php echo  cww_portfolio_sanitize_color($cww_header_toggle_color);?>;
        }

        <?php 
        $custom_css = ob_get_clean();
		$custom_css = cww_portfolio_strip_css_whitespace( $custom_css );

		wp_add_inline_style( 'parallax-portfolio-style', $custom_css );

    }
endif;
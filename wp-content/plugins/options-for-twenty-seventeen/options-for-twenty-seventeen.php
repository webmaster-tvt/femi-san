<?php
/*
 * Plugin Name: Options for Twenty Seventeen
 * Version: 2.4.8
 * Plugin URI: https://webd.uk/product/options-for-twenty-seventeen-upgrade/
 * Description: Adds powerful customizer options to modify all aspects of the default Wordpress theme Twenty Seventeen
 * Author: Webd Ltd
 * Author URI: https://webd.uk
 * Text Domain: options-for-twenty-seventeen
 */



if (!defined('ABSPATH')) {
    exit('This isn\'t the page you\'re looking for. Move along, move along.');
}



if (!class_exists('options_for_twenty_seventeen_class')) {

	class options_for_twenty_seventeen_class {

        public static $version = '2.4.8';

		function __construct() {

            add_action('customize_register', array($this, 'ofts_customize_register'), 999);
            add_action('wp_head' , array($this, 'ofts_header_output'), 11);
            add_action('customize_controls_enqueue_scripts', array($this, 'ofts_enqueue_customizer_css'));
            add_action('customize_preview_init', array($this, 'ofts_enqueue_customize_preview_js'));
            add_action('after_setup_theme', array($this, 'ofts_change_custom_header_image'), 9);
            add_action('after_setup_theme', array($this, 'ofts_fix_custom_logo_crop_bug'), 11);
            add_action('after_setup_theme', array($this, 'ofts_twentyseventeen_default_image_setup'), 11);
            add_action('widgets_init', array($this, 'ofts_header_sidebar_init'));
            add_action('widgets_init', array($this, 'ofts_site_info_sidebar_init'));
            add_action('wp_footer', array($this, 'ofts_wp_footer'));
            add_shortcode('social-links', array($this, 'ofts_social_links_shortcode'));

            if (is_admin()) {

                add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'ofts_add_plugin_action_links'));
                add_action('admin_notices', 'oftsCommon::admin_notices');
                add_action('wp_ajax_dismiss_ofts_notice_handler', 'oftsCommon::ajax_notice_handler');

            }

		}

		function ofts_add_plugin_action_links($links) {

			$settings_links = oftsCommon::plugin_action_links(admin_url('customize.php'));

			return array_merge($settings_links, $links);

		}

        function ofts_customize_register($wp_customize) {

            $section_description = oftsCommon::control_section_description();
            $upgrade_nag = oftsCommon::control_setting_upgrade_nag();



            $wp_customize->add_section('theme_options', array(
                'title'     => __('Theme Options', 'twentyseventeen'),
                'description'  => __('Use these options to customise the page layout and static front page sections.', 'options-for-twenty-seventeen') . ' ' . $section_description
            ));

            $wp_customize->add_control('page_layout', array(
               'label'           => __( 'Page Layout', 'twentyseventeen' ),
               'section'         => 'theme_options',
                'type'            => 'radio',
                'description'     => __( 'When the two-column layout is assigned, the page title is in one column and content is in the other.', 'twentyseventeen' ),
                'choices'         => array(
                    'one-column' => __( 'One Column', 'twentyseventeen' ),
                    'two-column' => __( 'Two Column', 'twentyseventeen' ),
                ),
                'priority'   => 1
            ));



            $wp_customize->add_section('ofts_general', array(
                'title'     => __('General Options', 'options-for-twenty-seventeen'),
                'description'  => __('Use these options to customise the overall site design.', 'options-for-twenty-seventeen') . ' ' . $section_description,
                'priority'     => 0
            ));



            if (class_exists('AdvancedTwentySeventeen')) {

                $wp_customize->add_setting('allow_ats_js', array(
                    'default'       => '',
                    'transport'     => 'postMessage',
                    'sanitize_callback' => 'oftsCommon::sanitize_boolean'
                ));
                $wp_customize->add_control('allow_ats_js', array(
                    'label'         => __('Allow Advanced Twenty Seventeen JS', 'options-for-twenty-seventeen'),
                    'description'   => __('The "Advanced Twenty Seventeen" plugin breaks the Wordpress Customizer Color Control so the plugin JavaScript is disabled to prevent this. Tick this box if you\'d like to enable the "Advanced Twenty Seventeen" JavaScript. You\'ll need to refresh the customizer after saving.', 'options-for-twenty-seventeen'),
                    'section'       => 'ofts_general',
                    'settings'      => 'allow_ats_js',
                    'type'          => 'checkbox'
                ));

            }

            $wp_customize->add_setting('page_max_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_max_width', array(
                'label'         => __('Page Max Width', 'options-for-twenty-seventeen'),
                'description'   => __('Sets the maximum width of the website container.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'page_max_width',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('100% (full width)', 'options-for-twenty-seventeen'),
                    '80em' => '80em (1280px)',
                    '75em' => '75em (1200px)',
                    '62.5em' => '62.5em (1000px)',
                    '48em' => '48em (768px)',
                    '46.25em' => '46.25em (740px)'
                )
            ));

            $wp_customize->add_setting('transparent_content_background', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('transparent_content_background', array(
                'label'         => __('Transparent Content Background', 'options-for-twenty-seventeen'),
                'description'   => __('Allow the page background to be seen in content areas.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'transparent_content_background',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('page_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_background_color', array(
                'label'         => __('Page Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Set the color of the website background.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
            	'settings'      => 'page_background_color'
            )));

            $wp_customize->add_setting('page_border_width', array(
                'default'           => 0,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_border_width', array(
                'label'         => __('Page Border Width', 'options-for-twenty-seventeen'),
                'description'   => __('Set the width of the website container border.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'page_border_width',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 0,
                    'max'   => 10,
                    'step'  => 1
                ),
		    	'active_callback' => 'options_for_twenty_seventeen_class::ofts_has_header_image_or_nivo_slider'
            ));

            $wp_customize->add_setting('page_border_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_border_color', array(
                'label'         => __('Page Border Color', 'options-for-twenty-seventeen'),
                'description'   => __('Set the color of the website container border.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
            	'settings'      => 'page_border_color',
		    	'active_callback' => 'options_for_twenty_seventeen_class::ofts_has_header_image_or_nivo_slider'
            )));

            $wp_customize->add_setting('page_border_style', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_border_style', array(
                'label'         => __('Page Border Style', 'options-for-twenty-seventeen'),
                'description'   => __('Set a border style for the website container.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'page_border_style',
                'type'          => 'select',
                'choices'       => array(
                    '' => 'Default (no border)',
                    'dotted' => __('Dotted', 'options-for-twenty-seventeen'),
                    'dashed' => __('Dashed', 'options-for-twenty-seventeen'),
                    'solid' => __('Solid', 'options-for-twenty-seventeen'),
                    'double' => __('Double', 'options-for-twenty-seventeen'),
                    'groove' => __('3D Groove', 'options-for-twenty-seventeen'),
                    'ridge' => __('3D Ridge', 'options-for-twenty-seventeen'),
                    'inset' => __('3D Inset', 'options-for-twenty-seventeen'),
                    'outset' => __('3D Outset', 'options-for-twenty-seventeen')
                ),
		    	'active_callback' => 'options_for_twenty_seventeen_class::ofts_has_header_image_or_nivo_slider'
            ));

            $wp_customize->add_setting('page_border_location', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_border_location', array(
                'label'         => __('Page Border Location', 'options-for-twenty-seventeen'),
                'description'   => __('Set the border location for the website container.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'page_border_location',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Default (Top, Right, Bottom and Left)', 'options-for-twenty-seventeen'),
                    'border-right-width: 0; border-left-width: 0;' => __('Top and Bottom only', 'options-for-twenty-seventeen'),
                    'border-top-width: 0; border-bottom-width: 0;' => __('Right and Left only', 'options-for-twenty-seventeen')
                ),
		    	'active_callback' => 'options_for_twenty_seventeen_class::ofts_has_header_image_or_nivo_slider'
            ));

            if (true === get_theme_mod('remove_link_underlines')) { set_theme_mod('remove_link_underlines', '1'); }

            $wp_customize->add_setting('remove_link_underlines', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('remove_link_underlines', array(
                'label'         => __('Remove Link Underlines', 'options-for-twenty-seventeen'),
                'description'   => __('Choose to remove all box-shadow properties that create underlines on links or add underlines to widget unordered lists.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'remove_link_underlines',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Show Link Underlines (Default)', 'options-for-twenty-seventeen'),
                    'unordered' => __('Widget Unordered List Underlines', 'options-for-twenty-seventeen'),
                    true => __('Remove Link Underlines', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('search_images', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('search_images', array(
                'label'         => __('Show Images in Search', 'options-for-twenty-seventeen'),
                'description'   => __('Show featured images in search results.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_general',
                'settings'      => 'search_images',
                'type'          => 'checkbox'
            ));



            $wp_customize->add_section('ofts_header', array(
                'title'     => __('Header Options', 'options-for-twenty-seventeen'),
                'description'  => __('Use these options to customise the header.', 'options-for-twenty-seventeen') . ' ' . $section_description,
                'priority'     => 0
            ));

            $wp_customize->add_setting('header_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('header_width', array(
                'label'         => __('Header Width', 'options-for-twenty-seventeen'),
                'description'   => __('Change the width of the site\'s header.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'header_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-seventeen'),
                    '80em' => '80em (1280px)',
                    '75em' => '75em (1200px)',
                    '' => '62.5em (1000px)',
                    '48em' => '48em (768px)',
                    '46.25em' => '46.25em (740px)'
                )
            ));

            $wp_customize->add_setting('no_full_cover_header_video', array(
                'default'           => false,
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('no_full_cover_header_video', array(
                'label'         => __('No Full Cover Header Video', 'options-for-twenty-seventeen'),
                'description'   => __('Prevents the plugin from enlarging the header video to fit the screen.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'no_full_cover_header_video',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('remove_header_video_button', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_header_video_button', array(
                'label'         => __('Remove Header Video Button', 'options-for-twenty-seventeen'),
                'description'   => __('Removes the play / pause button at the top right of the header if a video is shown.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'remove_header_video_button',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('site_identity_background_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_identity_background_color', array(
                'label'         => __('Site Identity Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Set the site logo, title and description background color.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
            	'settings'      => 'site_identity_background_color'
            )));

            $wp_customize->add_setting('site_branding_text_align', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_branding_text_align', array(
                'label'         => __('Site Branding Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the site branding to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_branding_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('remove_link_hover_opacity', array(
                'default'           => false,
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_link_hover_opacity', array(
                'label'         => __('Remove Link Hover Opacity', 'options-for-twenty-seventeen'),
                'description'   => __('Removes the opaque hover effect from header title and logo.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'remove_link_hover_opacity',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('site_title_text_align', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_title_text_align', array(
                'label'         => __('Site Title Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the site title to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_title_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('site_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_title_text_transform', array(
                'label'         => __('Site Title Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of the site title.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    'none' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalise', 'options-for-twenty-seventeen'),
                    '' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('remove_site_title_letter_spacing', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_site_title_letter_spacing', array(
                'label'         => __('Remove Site Title Letter Spacing', 'options-for-twenty-seventeen'),
                'description'   => __('Remove the letter spacing from the site title.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'remove_site_title_letter_spacing',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('site_title_font_size', array(
                'default'           => 2250,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_title_font_size', array(
                'label'         => __('Site Title Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of the site title.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 875,
                    'max'   => 3625,
                    'step'  => 125
                ),
            ));

            $wp_customize->add_setting('site_title_font_weight', array(
                'default'           => 800,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_title_font_weight', array(
                'label'         => __('Site Title Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of the site title.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('site_title_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_title_color', array(
                'label'         => __('Site Title Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font color of the site title.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
            	'settings'      => 'site_title_color'
            )));

            $wp_customize->add_setting('site_description_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_description_text_align', array(
                'label'         => __('Site Description Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the site description to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_description_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('site_description_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('site_description_text_transform', array(
                'label'         => __('Site Description Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of the site description.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_description_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalise', 'options-for-twenty-seventeen'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('site_description_font_size', array(
                'default'           => 1000,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_description_font_size', array(
                'label'         => __('Site Description Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of the site description.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_description_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 500,
                    'max'   => 1500,
                    'step'  => 125
                ),
            ));

            $wp_customize->add_setting('site_description_font_weight', array(
                'default'           => 400,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('site_description_font_weight', array(
                'label'         => __('Site Description Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of the site description.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'site_description_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('site_description_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'site_description_color', array(
                'label'         => __('Site Description Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font color of the site description.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
            	'settings'      => 'site_description_color'
            )));

            $wp_customize->add_setting('remove_header_gradient', array(
                'default'           => false,
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_header_gradient', array(
                'label'         => __('Remove Header Gradient', 'options-for-twenty-seventeen'),
                'description'   => __('Removes the grey background from the bottom of the cover image.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'remove_header_gradient',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('header_gradient_height', array(
                'default'           => 33,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('header_gradient_height', array(
                'label'         => __('Header Gradient Height', 'options-for-twenty-seventeen'),
                'description'   => __('Change the height of the header gradient.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'header_gradient_height',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 100,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('header_gradient_color', array(
                'default'       => '#000000',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'header_gradient_color', array(
                'label'         => __('Header Gradient Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color header gradient.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
            	'settings'      => 'header_gradient_color'
            )));


            $wp_customize->add_setting('header_gradient_opacity', array(
                'default'           => 30,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('header_gradient_opacity', array(
                'label'         => __('Header Gradient Opacity', 'options-for-twenty-seventeen'),
                'description'   => __('Change the opacity of the header gradient.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'header_gradient_opacity',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 100,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('remove_header_background', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_header_background', array(
                'label'         => __('Remove Header Background', 'options-for-twenty-seventeen'),
                'description'   => __('Removes the grey background from the header.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_header',
                'settings'      => 'remove_header_background',
                'type'          => 'checkbox'
            ));

            if (!get_theme_mod('external_header_video')) {

                $wp_customize->add_setting('full_cover_image', array(
                    'default'           => false,
                    'type'              => 'theme_mod',
                    'transport'         => 'refresh',
                    'sanitize_callback' => 'oftsCommon::sanitize_options'
                ));
                $wp_customize->add_control('full_cover_image', array(
                    'label'         => __('Full Cover Image', 'options-for-twenty-seventeen'),
                    'description'   => __('Forces the cover image to retain its aspect ratio. The main menu JavaScript prevents the site title from being shown on top of the cover image on small screens so if you need the full cover image on small screens you will need to select to show the site title below the image.', 'options-for-twenty-seventeen'),
                    'section'       => 'ofts_header',
                    'settings'      => 'full_cover_image',
                    'type'          => 'select',
                    'choices'       => array(
                        '' => __('Disabled', 'options-for-twenty-seventeen'),
                        '3' => __('Disabled - Use Page Style', 'options-for-twenty-seventeen'),
                        '1' => __('Site Title Below', 'options-for-twenty-seventeen'),
                        '2' => __('Site Title Overlay', 'options-for-twenty-seventeen')
                    ),
    		    	'active_callback' => array($this, 'ofts_has_header_image_or_nivo_slider')
                ));

            }



            $wp_customize->add_section('ofts_navigation', array(
                'title'        => __('Nav Options', 'options-for-twenty-seventeen'),
                'description'  => __('Use these options to customise the navigation.', 'options-for-twenty-seventeen') . ' ' . $section_description,
                'priority'     => 0
            ));



            $wp_customize->add_setting('prevent_nav_transition', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('prevent_nav_transition', array(
                'label'         => __('Prevent Nav Transition', 'options-for-twenty-seventeen'),
                'description'   => __('Prevent the nav from moving after the page has loaded.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'prevent_nav_transition',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('nav_bar_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('nav_bar_width', array(
                'label'         => __('Navigation Bar Width', 'options-for-twenty-seventeen'),
                'description'   => __('Change the width of the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'nav_bar_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-seventeen'),
                    '80rem' => '80rem (1280px)',
                    '75rem' => '75rem (1200px)',
                    '' => '62.5em (1000px)',
                    '48rem' => '48rem (768px)',
                    '46.25rem' => '46.25rem (740px)'
                )
            ));

            $wp_customize->add_setting('sticky_nav_bar_width', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('sticky_nav_bar_width', array(
                'label'         => __('Sticky Nav Bar Width', 'options-for-twenty-seventeen'),
                'description'   => __('Keep the width of the main menu when you scroll down.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'sticky_nav_bar_width',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('nav_background_image', array(
                'default'           => false,
                'transport'         => 'refresh',
                'sanitize_callback' => 'esc_attr'
            ));
            $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'nav_background_image', array(
                'label'         => __('Navigation Background Image', 'options-for-twenty-seventeen'),
                'description'   => __('Choose or upload an image to show in the main menu background.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'nav_background_image'
            )));

            $wp_customize->add_setting('nav_background_image_style', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('nav_background_image_style', array(
                'label'         => __('Navigation Background Image Style', 'options-for-twenty-seventeen'),
                'description'   => __('Set the type of background image.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'nav_background_image_style',
                'type'          => 'select',
                'choices'       => array(
                    '' => 'Default (Full Cover Image)',
                    'tiled' => __('Tiled / Repeating', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('nav_remove_padding_vertical', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('nav_remove_padding_vertical', array(
                'label'         => __('Remove Navigation Vertical Padding', 'options-for-twenty-seventeen'),
                'description'   => __('Remove the padding above and below the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'nav_remove_padding_vertical',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('navigation_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('navigation_text_transform', array(
                'label'         => __('Navigation Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of the navigation menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'navigation_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalise', 'options-for-twenty-seventeen'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('navigation_font_size', array(
                'default'           => 875,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('navigation_font_size', array(
                'label'         => __('Navigation Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of the navigation menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'navigation_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 750,
                    'max'   => 1000,
                    'step'  => 125
                ),
            ));

            $wp_customize->add_setting('navigation_font_weight', array(
                'default'           => 600,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('navigation_font_weight', array(
                'label'         => __('Navigation Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of the navigation menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'navigation_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('nav_link_padding_vertical', array(
                'default'           => 15,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('nav_link_padding_vertical', array(
                'label'         => __('Navigation Link Vertical Padding', 'options-for-twenty-seventeen'),
                'description'   => __('Change the padding above and below links in the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'nav_link_padding_vertical',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 32,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('nav_link_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_link_color', array(
                'label'         => __('Navigation Link Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of links in the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
            	'settings'      => 'nav_link_color'
            )));

            $wp_customize->add_setting('nav_current_link_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_current_link_color', array(
                'label'         => __('Navigation Current Page Link Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of current page links in the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
            	'settings'      => 'nav_current_link_color'
            )));

            $wp_customize->add_setting('nav_link_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_link_hover_color', array(
                'label'         => __('Navigation Hover Link Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of hovered links in the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
            	'settings'      => 'nav_link_hover_color'
            )));

            $wp_customize->add_setting('nav_link_hover_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_link_hover_background_color', array(
                'label'         => __('Navigation Hover Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the background color of hovered links in the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
            	'settings'      => 'nav_link_hover_background_color'
            )));

            $wp_customize->add_setting('nav_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'nav_background_color', array(
                'label'         => __('Navigation Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the background color of the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
            	'settings'      => 'nav_background_color'
            )));

            $wp_customize->add_setting('nav_background_opacity', array(
                'default'           => 0,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('nav_background_opacity', array(
                'label'         => __('Navigation Background Opacity', 'options-for-twenty-seventeen'),
                'description'   => __('Change the background opacity of the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'nav_background_opacity',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 0,
                    'max'   => 100,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('sub_menu_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'sub_menu_background_color', array(
                'label'         => __('Sub Menu Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the background color of dropdown menus in the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
            	'settings'      => 'sub_menu_background_color'
            )));

            $wp_customize->add_setting('rotate_sub_menu_arrow', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('rotate_sub_menu_arrow', array(
                'label'         => __('Rotate Sub Menu Arrow', 'options-for-twenty-seventeen'),
                'description'   => __('Rotates the arrow below a main menu item that has a sub menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'rotate_sub_menu_arrow',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('remove_nav_scroll_arrow', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_nav_scroll_arrow', array(
                'label'         => __('Remove Navigation Scroll Down Arrow', 'options-for-twenty-seventeen'),
                'description'   => __('Removes the arrow at the end of the main menu.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_navigation',
                'settings'      => 'remove_nav_scroll_arrow',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_section('ofts_content', array(
                'title'     => __('Content Options', 'options-for-twenty-seventeen'),
                'description'  => __('Use these options to customise the content.', 'options-for-twenty-seventeen') . ' ' . $section_description,
                'priority'     => 0
            ));

            $wp_customize->add_setting('content_width', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('content_width', array(
                'label'         => __('Content Width', 'options-for-twenty-seventeen'),
                'description'   => __('Change the width of the site\'s content.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'content_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-seventeen'),
                    '80em' => '80em (1280px)',
                    '75em' => '75em (1200px)',
                    '62.5em' => '62.5em (1000px)',
                    '48em' => '48em (768px)',
                    '' => '46.25em (740px)'
                )
            ));

            $wp_customize->add_setting('content_background_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'content_background_color', array(
                'label'         => __('Content Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Set the color of the content background.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'content_background_color'
            )));

            $wp_customize->add_setting('page_sidebar', array(
                'default'           => false,
                'transport'         => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('page_sidebar', array(
                'label'         => __('Page Sidebar', 'options-for-twenty-seventeen'),
                'description'   => __('Adds the Blog Sidebar widget area to pages.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_sidebar',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('content_padding_top', array(
                'default'           => 12,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('content_padding_top', array(
                'label'         => __('Content Padding Top', 'options-for-twenty-seventeen'),
                'description'   => __('Change the padding at the top of the content below the navigation (not on front page).', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'content_padding_top',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 2,
                    'max'   => 24,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('featured_image_border_width', array(
                'default'           => 2,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('featured_image_border_width', array(
                'label'         => __('Featured Image Border Width', 'options-for-twenty-seventeen'),
                'description'   => __('Set the width of the border at the bottom of the featured image.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'featured_image_border_width',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 11,
                    'step'  => 1
                )
            ));

            $wp_customize->add_setting('page_header_title_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_header_title_text_align', array(
                'label'         => __('Archive Title Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the archive titles to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_header_title_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('page_header_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_header_title_text_transform', array(
                'label'         => __('Archive Title Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of archive titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_header_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    'none' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalize', 'options-for-twenty-seventeen'),
                    '' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('remove_page_header_title_letter_spacing', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_page_header_title_letter_spacing', array(
                'label'         => __('Remove Archive Title Letter Spacing', 'options-for-twenty-seventeen'),
                'description'   => __('Remove the letter spacing from archive titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'remove_page_header_title_letter_spacing',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('page_header_title_font_size', array(
                'default'           => 875,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_header_title_font_size', array(
                'label'         => __('Archive Title Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of archive titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_header_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 875,
                    'max'   => 2750,
                    'step'  => 125
                ),
            ));

            $wp_customize->add_setting('page_header_title_font_weight', array(
                'default'           => 800,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_header_title_font_weight', array(
                'label'         => __('Archive Title Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of archive titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_header_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('page_header_title_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_header_title_color', array(
                'label'         => __('Archive Title Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font color of archive titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'page_header_title_color'
            )));

            $wp_customize->add_setting('post_meta_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('post_meta_text_align', array(
                'label'         => __('Post Meta Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the post meta to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_meta_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('post_meta_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('post_meta_text_transform', array(
                'label'         => __('Post Meta Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of post meta.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_meta_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    'none' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalise', 'options-for-twenty-seventeen'),
                    '' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('post_meta_font_size', array(
                'default'           => 6875,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('post_meta_font_size', array(
                'label'         => __('Post Meta Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of post meta.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_meta_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 6875,
                    'max'   => 17500,
                    'step'  => 625
                ),
            ));

            $wp_customize->add_setting('post_meta_font_weight', array(
                'default'           => 800,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('post_meta_font_weight', array(
                'label'         => __('Post Meta Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of post meta.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_meta_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('post_meta_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'post_meta_color', array(
                'label'         => __('Post Meta Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font color of post meta.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'post_meta_color'
            )));

            $wp_customize->add_setting('post_entry_header_title_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('post_entry_header_title_text_align', array(
                'label'         => __('Post Title Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the post titles to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_entry_header_title_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('post_entry_header_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('post_entry_header_title_text_transform', array(
                'label'         => __('Post Title Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of post titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_entry_header_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalise', 'options-for-twenty-seventeen'),
                    'uppercase' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('post_entry_header_title_font_size', array(
                'default'           => 1625,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('post_entry_header_title_font_size', array(
                'label'         => __('Post Title Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of post titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_entry_header_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 875,
                    'max'   => 2750,
                    'step'  => 125
                ),
            ));

            $wp_customize->add_setting('post_entry_header_title_font_weight', array(
                'default'           => 300,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('post_entry_header_title_font_weight', array(
                'label'         => __('Post Title Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of post titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'post_entry_header_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('post_entry_header_title_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'post_entry_header_title_color', array(
                'label'         => __('Post Title Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font color of post titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'post_entry_header_title_color'
            )));

            $wp_customize->add_setting('page_entry_header_title_text_align', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_entry_header_title_text_align', array(
                'label'         => __('Page Title Alignment', 'options-for-twenty-seventeen'),
                'description'   => __('Align the page titles to the left, center or right.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_entry_header_title_text_align',
                'type'          => 'select',
                'choices'       => array(
                    '' => __('Left', 'options-for-twenty-seventeen'),
                    'center' => __('Center', 'options-for-twenty-seventeen'),
                    'right' => __('Right', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('page_entry_header_title_text_transform', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('page_entry_header_title_text_transform', array(
                'label'         => __('Page Title Font Case', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font case of page titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_entry_header_title_text_transform',
                'type'          => 'select',
                'choices'       => array(
                    'none' => __('None', 'options-for-twenty-seventeen'),
                    'capitalize' => __('Capitalise', 'options-for-twenty-seventeen'),
                    '' => __('Uppercase', 'options-for-twenty-seventeen'),
                    'lowercase' => __('Lowercase', 'options-for-twenty-seventeen')
                )
            ));

            $wp_customize->add_setting('remove_page_entry_header_title_letter_spacing', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_page_entry_header_title_letter_spacing', array(
                'label'         => __('Remove Page Title Letter Spacing', 'options-for-twenty-seventeen'),
                'description'   => __('Remove the letter spacing from page titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'remove_page_entry_header_title_letter_spacing',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('page_entry_header_title_font_size', array(
                'default'           => 1625,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_entry_header_title_font_size', array(
                'label'         => __('Page Title Font Size', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font size of page titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_entry_header_title_font_size',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 875,
                    'max'   => 2750,
                    'step'  => 125
                ),
            ));

            $wp_customize->add_setting('page_entry_header_title_font_weight', array(
                'default'           => 800,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_entry_header_title_font_weight', array(
                'label'         => __('Page Title Font Weight', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font weight of page titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_entry_header_title_font_weight',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 100,
                    'max'   => 900,
                    'step'  => 100
                ),
            ));

            $wp_customize->add_setting('page_entry_header_title_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'page_entry_header_title_color', array(
                'label'         => __('Page Title Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the font color of page titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'page_entry_header_title_color'
            )));

            $wp_customize->add_setting('page_entry_header_title_margin_bottom', array(
                'default'           => 9,
                'transport'         => 'refresh',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('page_entry_header_title_margin_bottom', array(
                'label'         => __('Page Title Margin Bottom', 'options-for-twenty-seventeen'),
                'description'   => __('Change the margin height below the page title.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'page_entry_header_title_margin_bottom',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 9,
                    'step'  => 1
                ),
            ));

            $wp_customize->add_setting('content_link_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'content_link_color', array(
                'label'         => __('Content Link Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of links in the content.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'content_link_color'
            )));

            $wp_customize->add_setting('content_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'content_hover_color', array(
                'label'         => __('Content Hover Link Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of hovered links in the content.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
            	'settings'      => 'content_hover_color'
            )));

            $wp_customize->add_setting('hide_categories', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_categories', array(
                'label'         => __('Hide Categories', 'options-for-twenty-seventeen'),
                'description'   => __('Hide the post categories on single posts.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'hide_categories',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_tags', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_tags', array(
                'label'         => __('Hide Tags', 'options-for-twenty-seventeen'),
                'description'   => __('Hide the post tags on single posts.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'hide_tags',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('hide_post_navigation', array(
                'default'           => false,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('hide_post_navigation', array(
                'label'         => __('Hide Post Navigation', 'options-for-twenty-seventeen'),
                'description'   => __('Hide the "Previous" and "Next" post navigation.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_content',
                'settings'      => 'hide_post_navigation',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_section('ofts_footer', array(
                'title'     => __('Footer Options', 'options-for-twenty-seventeen'),
                'description'  => __('Use these options to customise the footer.', 'options-for-twenty-seventeen') . ' ' . $section_description,
                'priority'     => 0
            ));

            $wp_customize->add_setting('footer_width', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'oftsCommon::sanitize_options'
            ));
            $wp_customize->add_control('footer_width', array(
                'label'         => __('Footer Width', 'options-for-twenty-seventeen'),
                'description'   => __('Change the width of the site\'s footer.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
                'settings'      => 'footer_width',
                'type'          => 'select',
                'choices'       => array(
                    '100%' => __('100% (full width)', 'options-for-twenty-seventeen'),
                    '80rem' => '80rem (1280px)',
                    '75rem' => '75rem (1200px)',
                    '' => '62.5em (1000px)',
                    '48rem' => '48rem (768px)',
                    '46.25rem' => '46.25rem (740px)'
                )
            ));

            $wp_customize->add_setting('footer_border_width', array(
                'default'           => 2,
                'transport'         => 'postMessage',
                'sanitize_callback' => 'absint'
            ));
            $wp_customize->add_control('footer_border_width', array(
                'label'         => __('Footer Border Width', 'options-for-twenty-seventeen'),
                'description'   => __('Set the width of the border at the top of the footer.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
                'settings'      => 'footer_border_width',
                'type'          => 'range',
                'input_attrs' => array(
                    'min'   => 1,
                    'max'   => 11,
                    'step'  => 1
                )
            ));

            $wp_customize->add_setting('footer_background_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_background_color', array(
                'label'         => __('Footer Background Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the background color of the footer area.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
            	'settings'      => 'footer_background_color'
            )));

            $wp_customize->add_setting('footer_title_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_title_color', array(
                'label'         => __('Footer Title Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of the footer widget titles.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
            	'settings'      => 'footer_title_color'
            )));

            $wp_customize->add_setting('footer_text_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_text_color', array(
                'label'         => __('Footer Text Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of the footer text.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
            	'settings'      => 'footer_text_color'
            )));

            $wp_customize->add_setting('footer_link_color', array(
                'default'       => '',
                'transport'     => 'postMessage',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_link_color', array(
                'label'         => __('Footer Link Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of the footer links.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
            	'settings'      => 'footer_link_color'
            )));

            $wp_customize->add_setting('footer_link_hover_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'footer_link_hover_color', array(
                'label'         => __('Footer Link Hover Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of the hovered footer links.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
            	'settings'      => 'footer_link_hover_color'
            )));

            $wp_customize->add_setting('remove_powered_by_wordpress', array(
                'default'       => false,
                'transport'     => 'refresh',
                'sanitize_callback' => 'oftsCommon::sanitize_boolean'
            ));
            $wp_customize->add_control('remove_powered_by_wordpress', array(
                'label'         => __('Remove Powered by WordPress', 'options-for-twenty-seventeen'),
                'description'   => __('Removes the "Proudly powered by WordPress" text displayed in the website footer and replaces with the content of the "Site Info" widget area.', 'options-for-twenty-seventeen'),
                'section'       => 'ofts_footer',
                'settings'      => 'remove_powered_by_wordpress',
                'type'          => 'checkbox'
            ));

            $wp_customize->add_setting('paragraph_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'paragraph_color', array(
                'label'         => __('Paragraph Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of paragraphs.', 'options-for-twenty-seventeen'),
                'section'       => 'colors',
            	'settings'      => 'paragraph_color'
            )));

            $wp_customize->add_setting('heading_color', array(
                'default'       => '',
                'transport'     => 'refresh',
                'sanitize_callback' => 'sanitize_hex_color'
            ));
            $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'heading_color', array(
                'label'         => __('Heading Color', 'options-for-twenty-seventeen'),
                'description'   => __('Change the color of headings.', 'options-for-twenty-seventeen'),
                'section'       => 'colors',
            	'settings'      => 'heading_color'
            )));

            $control_label = __('Search / Archive Page Layout', 'options-for-twenty-seventeen');
            $control_description = __( 'When the two-column layout is assigned, the page title is in one column and content is in the other.', 'twentyseventeen' );
            oftsCommon::add_hidden_control($wp_customize, 'search_archive_page_layout', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Front Page Sections', 'options-for-twenty-seventeen');
            $control_description = __('Set the number of sections on the static home page. You will need to save and re-load the Customiser to see changes.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'front_page_sections', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Panel Image Height', 'options-for-twenty-seventeen');
            $control_description = __('Set the height of the frontpage section parallax images.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'panel_image_height', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Blog Featured Images', 'options-for-twenty-seventeen');
            $control_description = __('Enable featured images in front page section blog page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'front_page_section_blog_thumbnails', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Parallax Off', 'options-for-twenty-seventeen');
            $control_description = __('Turn on "true parallax" or turn off parallax effect altogether on front page section featured images and show full image in the usual flow of the page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'front_page_sections_parallax', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Back to Top Link', 'options-for-twenty-seventeen');
            $control_description = __('Add a "Back to Top" link at the end of all pages.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'footer_back_to_top', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Fixed Back to Top Link', 'options-for-twenty-seventeen');
            $control_description = __('Fix the "Back to Top" link to the bottom right of the browser window.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'fix_footer_back_to_top', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Front Page Section Back to Top Link', 'options-for-twenty-seventeen');
            $control_description = __('Add a "Back to Top" link to front page sections.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'front_page_sections_back_to_top', 'theme_options', $control_label, $control_description . ' ' . $upgrade_nag, 5);

            $control_label = __('Footer Background Opacity', 'options-for-twenty-seventeen');
            $control_description = __('Choose the opacity of the footer background color.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'footer_background_opacity', 'ofts_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Prevent Font Loading', 'options-for-twenty-seventeen');
            $control_description = __('Prevent theme from loading Libre Franklin Google font.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'prevent_font_loading', 'ofts_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Page Background Image', 'options-for-twenty-seventeen');
            $control_description = __('Choose or upload an image to show in the website background.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'page_background_image', 'ofts_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Page Background Image Style', 'options-for-twenty-seventeen');
            $control_description = __('Set the type of background image.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'page_background_image_style', 'ofts_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Auto Excerpt Posts', 'options-for-twenty-seventeen');
            $control_description = __('Show first 55 words of a post with "Continue reading" link on home page and archive pages.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'auto_excerpt', 'ofts_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Set Number of Excerpt Words', 'options-for-twenty-seventeen');
            $control_description = __('Set the maximum number of words for the excerpt.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'auto_excerpt_words', 'ofts_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Reset Tag Cloud Widget', 'options-for-twenty-seventeen');
            $control_description = __('Reverts Twenty Seventeen Tag Cloud Widget styling.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'reset_tag_cloud', 'ofts_general', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Header Height', 'options-for-twenty-seventeen');
            $control_description = __('Set the height of the header image on the home page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'header_height', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Use Featured Image as Header Image', 'options-for-twenty-seventeen');
            $control_description = __('This option moves the featured image on single posts and pages to the header and makes the header image the same size as on the home page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'featured_header_image', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide Header Image', 'options-for-twenty-seventeen');
            $control_description = __('This option hides the header image on all pages except the home page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'hide_header_image', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide Site Branding', 'options-for-twenty-seventeen');
            $control_description = __('This option hides the site title and description on all pages except the home page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'hide_site_branding', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Header Sidebar', 'options-for-twenty-seventeen');
            $control_description = __('Add a widget ready sidebar to the header area of the theme.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'header_sidebar', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide YouTube Until Loaded', 'options-for-twenty-seventeen');
            $control_description = __('Hide the YouTube video in the header until it has started playing. The header image will be shown instead if there is one.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'hide_youtube_until_loaded', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Pause YouTube on Scroll', 'options-for-twenty-seventeen');
            $control_description = __('Pause the YouTube video when the user scrolls down. Play again when the user scrolls back to the top of the page.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'pause_youtube_on_scroll', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Video Looping Off', 'options-for-twenty-seventeen');
            $control_description = __('Prevent the header video from looping. This currently is developed for .mp4 videos, contact us if you need this feature for a YouTube video.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'video_looping_off', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('True Parallax Cover Image', 'options-for-twenty-seventeen');
            $control_description = __('Turn on "true parallax" scrolling for the header cover image.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'true_parallax_cover_image', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('True Parallax Speed', 'options-for-twenty-seventeen');
            $control_description = __('Set the parallax scroll speed.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'true_parallax_speed', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Slider Cover', 'options-for-twenty-seventeen');
            $control_description = sprintf(wp_kses(__('Replaces the cover image with a <a href="%s">Nivo</a>, Sliderspack or MetaSlider Slider. Remember to set "Image Size" to "Twenty-Seventeen-featured-image" in your slider settings for best results.', 'options-for-twenty-seventeen'), array('a' => array('href' => array()))), esc_url(admin_url('plugin-install.php?s=nivo-slider-lite&tab=search&type=term')));
            oftsCommon::add_hidden_control($wp_customize, 'nivo_slider_cover', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Enable Nivo Captions', 'options-for-twenty-seventeen');
            $control_description = __('Overlay slide captions on Nivo Slider.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'enable_nivo_captions', 'ofts_header', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Add Logo to Navigation Bar', 'options-for-twenty-seventeen');
            $control_description = __('Move or copy the Site Logo to the Navigation Bar.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'add_logo_to_nav', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Animate Logo in Navigation Bar', 'options-for-twenty-seventeen');
            $control_description = __('Shrinks the logo in the Navigation Bar when the user scrolls.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'animate_nav_logo', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Move Navigation Bar to Top', 'options-for-twenty-seventeen');
            $control_description = __('Moves the main menu to the top of the custom header.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'move_nav_bar_to_top', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Fix Mobile Navigation Bar to Top', 'options-for-twenty-seventeen');
            $control_description = __('Fixes the mobile navigation bar to the top of the screen.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'fix_mobile_nav_bar_to_top', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Navigation Responsive Breakpoint', 'options-for-twenty-seventeen');
            $control_description = __('Increase or decrease the point at which the main menu becomes a mobile menu.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'nav_responsive_breakpoint', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Navigation Hamburger Alignment', 'options-for-twenty-seventeen');
            $control_description = __('Align the navigation menu hamburger on smaller screens to the left, center or right.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'nav_hamburger_align', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Navigation Alignment', 'options-for-twenty-seventeen');
            $control_description = __('Align the navigation menu items to the left, center or right.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'nav_text_align', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Navigation Logo Alignment', 'options-for-twenty-seventeen');
            $control_description = __('Align the logo in the main menu, if present.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'nav_logo_align', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Bounce Navigation Scroll Down Arrow', 'options-for-twenty-seventeen');
            $control_description = __('Animates the arrow at the end of the main menu.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'bounce_nav_scroll_arrow', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Move Scroll Down Arrow', 'options-for-twenty-seventeen');
            $control_description = __('Moves the arrow at the end of the main menu above the navigation.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'move_nav_scroll_arrow', 'ofts_navigation', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide Blog Sidebar for Mobile', 'options-for-twenty-seventeen');
            $control_description = __('Hides the Blog Sidebar widget area on small screens.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'hide_blog_sidebar', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Match Content and Sidebar Height', 'options-for-twenty-seventeen');
            $control_description = __('Matches the height of the Blog Sidebar to the Primary Content.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'match_primary_secondary_height', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Primary Content Area Width', 'options-for-twenty-seventeen');
            $control_description = __('Change the width of the Primary Content area.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'primary_width', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Content Margin / Gutter', 'options-for-twenty-seventeen');
            $control_description = __('Change the gap between the Primary Content area and the Blog Sidebar.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'content_gutter', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Swap Content and Sidebar (Desktop)', 'options-for-twenty-seventeen');
            $control_description = __('Moves the Blog Sidebar to the left of the Primary Content on larger screens.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'swap_content', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Swap Content and Sidebar (Mobile)', 'options-for-twenty-seventeen');
            $control_description = __('Moves the Blog Sidebar above the Primary Content on smaller screens.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'swap_content_mobile', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Implement Yoast SEO Breadcrumbs', 'options-for-twenty-seventeen');
            $control_description = sprintf(wp_kses(__('Inject <a href="%s">Yoast SEO</a> breadcrumbs above and / or below single post and page content.', 'options-for-twenty-seventeen'), array('a' => array('href' => array()))), esc_url(admin_url('plugin-install.php?s=wordpress-seo&tab=search&type=term')));
            oftsCommon::add_hidden_control($wp_customize, 'implement_yoast_breadcrumbs', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Inject Featured Image Caption', 'options-for-twenty-seventeen');
            $control_description = __('Overlays the image caption onto the featured image.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'inject_featured_image_caption', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Featured Image Caption Font Size', 'options-for-twenty-seventeen');
            $control_description = __('Change the font size of featured image captions.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'featured_image_caption_font_size', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Featured Image Caption Font Weight', 'options-for-twenty-seventeen');
            $control_description = __('Change the font weight of featured image captions.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'featured_image_caption_font_weight', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Move Featured Image', 'options-for-twenty-seventeen');
            $control_description = __('Move the featured image into the post content.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'move_featured_image', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide Archive Featured Images', 'options-for-twenty-seventeen');
            $control_description = __('Hide post featured images on Archive pages.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'hide_archive_featured_images', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide Post Dates', 'options-for-twenty-seventeen');
            $control_description = __('Prevents Wordpress from displaying the date of a post.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'remove_posted_on', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Hide Post Author', 'options-for-twenty-seventeen');
            $control_description = __('Prevents Wordpress from displaying the author of a post.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'remove_author', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Remove Category and Tag from Archive Titles', 'options-for-twenty-seventeen');
            $control_description = __('Remove the words "Category: " and "Tag: " from archive page titles.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'remove_category_tag', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Show Page Content on Posts Page', 'options-for-twenty-seventeen');
            $control_description = __('Show the content of the chosen posts page above the posts.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'show_content_on_posts_page', 'ofts_content', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Footer Background Image', 'options-for-twenty-seventeen');
            $control_description = __('Choose or upload an image to show in the background of the footer area.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'footer_background_image', 'ofts_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Footer Sidebars', 'options-for-twenty-seventeen');
            $control_description = __('Add a third or fourth widget ready sidebar to the footer area of the theme.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'footer_sidebars', 'ofts_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Fix Social Links', 'options-for-twenty-seventeen');
            $control_description = __('Fix the social links to the left or right for large screens.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'fix_social_links', 'ofts_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Square Social Links', 'options-for-twenty-seventeen');
            $control_description = __('Make the social links menu items square.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'square_social_links', 'ofts_footer', $control_label, $control_description . ' ' . $upgrade_nag);

            $control_label = __('Add Colors to Social Links Menu', 'options-for-twenty-seventeen');
            $control_description = __('Changes the background of the social links menu items to their relevant corporate colors.', 'options-for-twenty-seventeen');
            oftsCommon::add_hidden_control($wp_customize, 'colored_social_links_menu', 'ofts_footer', $control_label, $control_description . ' ' . $upgrade_nag);



            if (!class_exists('sidemenu_class')) {

                $wp_customize->add_section('sidemenu', array(
                    'title'     => __('SideMenu', 'options-for-twenty-seventeen'),
                    'description'  => __('Add a sliding SideMenu to Twenty Seventeen.', 'options-for-twenty-seventeen'),
                    'priority'     => 0
                ));

                oftsCommon::add_hidden_control($wp_customize, 'install_sidemenu', 'sidemenu', 'Install SideMenu', sprintf(wp_kses(__('Add a slide-in side menu and widget area to the site by <a href="%s">installing SideMenu plugin</a>.', 'options-for-twenty-seventeen'), array('a' => array('href' => array()))), esc_url(add_query_arg(array('s' => 'sidemenu+injects', 'tab' => 'search', 'type' => 'term'), self_admin_url('plugin-install.php')))));

            }

        }

		public static function ofts_has_header_image_or_nivo_slider() {
        	return (has_custom_header() || get_theme_mod('nivo_slider_cover'));
        }

		public static function ofts_has_no_header_image_or_nivo_slider() {
        	return (!has_custom_header() && !get_theme_mod('nivo_slider_cover'));
        }

		public static function ofts_has_youtube_video() {
        	return (!get_theme_mod('external_header_video') == false);
        }

        function ofts_header_output() {

            global $post;

?>
<!--Customizer CSS--> 
<style type="text/css">
.admin-bar :target:before
{
   height: 117px;
   margin-top: -117px;
}
.single-post:not(.has-sidebar) #primary,
.page.page-one-column:not(.twentyseventeen-front-page) #primary,
.archive.page-one-column:not(.has-sidebar) .page-header,
.archive.page-one-column:not(.has-sidebar) #primary {
    max-width: none;
}
.navigation-top {
    z-index: 1001 !important;
}
@supports (-webkit-touch-callout: none) {
	@media screen and (min-width: 48em) {
		.background-fixed .panel-image {
			background-size: cover;
			height: auto;
			background-attachment: scroll;
		}
	}
}
<?php

            oftsCommon::generate_css('.colors-custom .colors-custom input[type="datetime-local"]:focus, .colors-custom .comment-awaiting-moderation, .colors-custom .comment-body, .colors-custom .comment-metadata, .colors-custom .comment-metadata a, .colors-custom .comment-metadata a.comment-edit-link, .colors-custom .comment-reply-link .icon, .colors-custom .dropdown-toggle, .colors-custom .entry-content blockquote.alignleft, .colors-custom .entry-content blockquote.alignright, .colors-custom .entry-footer .cat-links a, .colors-custom .entry-footer .tags-links a, .colors-custom .entry-meta, .colors-custom .entry-meta a, .colors-custom .format-quote blockquote, .colors-custom .gallery-caption, .colors-custom .menu-toggle, .colors-custom .no-comments, .colors-custom .page-links .page-number, .colors-custom .page-links a .page-number, .colors-custom .page-numbers.current, .colors-custom .post-navigation a:focus .icon, .colors-custom .post-navigation a:hover .icon, .colors-custom .site-content .site-content-light, .colors-custom .site-content .wp-playlist-light .wp-playlist-current-item .wp-playlist-item-album, .colors-custom .site-content .wp-playlist-light .wp-playlist-current-item .wp-playlist-item-artist, .colors-custom .site-info a, .colors-custom .twentyseventeen-panel .recent-posts .entry-header .edit-link, .colors-custom .twentyseventeen-panel .recent-posts .entry-header .edit-link , .colors-custom .wp-caption, .colors-custom a, .colors-custom blockquote, .colors-custom button, .colors-custom button.secondary, .colors-custom h5, .colors-custom input, .colors-custom input[type="button"].secondary, .colors-custom input[type="color"], .colors-custom input[type="color"]:focus, .colors-custom input[type="date"], .colors-custom input[type="date"]:focus, .colors-custom input[type="datetime"], .colors-custom input[type="datetime"]:focus, .colors-custom input[type="datetime-local"], .colors-custom input[type="email"], .colors-custom input[type="email"]:focus, .colors-custom input[type="month"], .colors-custom input[type="month"]:focus, .colors-custom input[type="number"], .colors-custom input[type="number"]:focus, .colors-custom input[type="password"], .colors-custom input[type="password"]:focus, .colors-custom input[type="range"], .colors-custom input[type="range"]:focus, .colors-custom input[type="reset"], .colors-custom input[type="reset"].secondary, .colors-custom input[type="search"], .colors-custom input[type="search"]:focus, .colors-custom input[type="submit"].secondary, .colors-custom input[type="tel"], .colors-custom input[type="tel"]:focus, .colors-custom input[type="text"], .colors-custom input[type="text"]:focus, .colors-custom input[type="time"], .colors-custom input[type="time"]:focus, .colors-custom input[type="url"], .colors-custom input[type="url"]:focus, .colors-custom input[type="week"], .colors-custom input[type="week"]:focus, .colors-custom label, .colors-custom mark, .colors-custom select, .colors-custom textarea, .colors-custom textarea:focus, .colors-custom.archive .entry-meta a.post-edit-link, .colors-custom.blog .entry-meta a.post-edit-link, .colors-custom.search .entry-meta a.post-edit-link, .colors-custom.twentyseventeen-front-page .panel-content .recent-posts article, .colors-dark .comment-awaiting-moderation, .colors-dark .comment-body, .colors-dark .comment-content a:focus, .colors-dark .comment-content a:hover, .colors-dark .comment-metadata, .colors-dark .comment-metadata a, .colors-dark .comment-metadata a.comment-edit-link, .colors-dark .comment-metadata a.comment-edit-link:focus, .colors-dark .comment-metadata a.comment-edit-link:hover, .colors-dark .comment-metadata a:focus, .colors-dark .comment-metadata a:hover, .colors-dark .comment-reply-link .icon, .colors-dark .comment-reply-link:focus, .colors-dark .comment-reply-link:hover, .colors-dark .comments-pagination a:not(.prev):not(.next):focus, .colors-dark .comments-pagination a:not(.prev):not(.next):hover, .colors-dark .dropdown-toggle, .colors-dark .edit-link a:focus, .colors-dark .edit-link a:hover, .colors-dark .entry-content a:focus, .colors-dark .entry-content a:hover, .colors-dark .entry-content blockquote.alignleft, .colors-dark .entry-content blockquote.alignright, .colors-dark .entry-footer .cat-links a, .colors-dark .entry-footer .cat-links a:focus, .colors-dark .entry-footer .cat-links a:hover, .colors-dark .entry-footer .tags-links a, .colors-dark .entry-footer .tags-links a:focus, .colors-dark .entry-footer .tags-links a:hover, .colors-dark .entry-meta, .colors-dark .entry-meta a, .colors-dark .entry-meta a:focus, .colors-dark .entry-meta a:hover, .colors-dark .entry-summary a:focus, .colors-dark .entry-summary a:hover, .colors-dark .format-quote blockquote, .colors-dark .logged-in-as a:focus, .colors-dark .logged-in-as a:hover, .colors-dark .menu-toggle, .colors-dark .no-comments, .colors-dark .page-links .page-number, .colors-dark .page-links a .page-number, .colors-dark .page-links a:focus .page-number, .colors-dark .page-links a:hover .page-number, .colors-dark .page-numbers.current, .colors-dark .pagination a:not(.prev):not(.next):focus, .colors-dark .pagination a:not(.prev):not(.next):hover, .colors-dark .post-navigation a:focus, .colors-dark .post-navigation a:focus .icon, .colors-dark .post-navigation a:hover, .colors-dark .post-navigation a:hover .icon, .colors-dark .posts-navigation a:focus, .colors-dark .posts-navigation a:hover, .colors-dark .site-footer .widget-area a:focus, .colors-dark .site-footer .widget-area a:hover, .colors-dark .site-info a, .colors-dark .site-info a:focus, .colors-dark .site-info a:hover, .colors-dark .taxonomy-description, .colors-dark .widget a:focus, .colors-dark .widget a:hover, .colors-dark .widget ul li a:focus, .colors-dark .widget ul li a:hover, .colors-dark .widget_authors a:focus strong, .colors-dark .widget_authors a:hover strong, .colors-dark .wp-caption, .colors-dark a, .colors-dark a:active, .colors-dark a:hover, .colors-dark blockquote, .colors-dark button, .colors-dark button.secondary, .colors-dark input, .colors-dark input[type="button"].secondary, .colors-dark input[type="color"], .colors-dark input[type="color"]:focus, .colors-dark input[type="date"], .colors-dark input[type="date"]:focus, .colors-dark input[type="datetime"], .colors-dark input[type="datetime"]:focus, .colors-dark input[type="datetime-local"], .colors-dark input[type="datetime-local"]:focus, .colors-dark input[type="email"], .colors-dark input[type="email"]:focus, .colors-dark input[type="month"], .colors-dark input[type="month"]:focus, .colors-dark input[type="number"], .colors-dark input[type="number"]:focus, .colors-dark input[type="password"], .colors-dark input[type="password"]:focus, .colors-dark input[type="range"], .colors-dark input[type="range"]:focus, .colors-dark input[type="reset"], .colors-dark input[type="reset"].secondary, .colors-dark input[type="search"], .colors-dark input[type="search"]:focus, .colors-dark input[type="submit"].secondary, .colors-dark input[type="tel"], .colors-dark input[type="tel"]:focus, .colors-dark input[type="text"], .colors-dark input[type="text"]:focus, .colors-dark input[type="time"], .colors-dark input[type="time"]:focus, .colors-dark input[type="url"], .colors-dark input[type="url"]:focus, .colors-dark input[type="week"], .colors-dark input[type="week"]:focus, .colors-dark label, .colors-dark mark, .colors-dark select, .colors-dark textarea, .colors-dark textarea:focus, .colors-dark.archive .entry-meta a.post-edit-link, .colors-dark.archive .entry-meta a.post-edit-link:focus, .colors-dark.archive .entry-meta a.post-edit-link:hover, .colors-dark.blog .entry-meta a.post-edit-link, .colors-dark.blog .entry-meta a.post-edit-link:focus, .colors-dark.blog .entry-meta a.post-edit-link:hover, .colors-dark.search .entry-meta a.post-edit-link, .colors-dark.search .entry-meta a.post-edit-link:focus, .colors-dark.search .entry-meta a.post-edit-link:hover, .colors-dark.twentyseventeen-front-page .panel-content .recent-posts article, .comment-body, .comment-content a:focus, .comment-content a:hover, .comment-metadata a.comment-edit-link:focus, .comment-metadata a.comment-edit-link:hover, .comment-metadata a:focus, .comment-metadata a:hover, .comment-reply-link:focus, .comment-reply-link:hover, .comments-pagination a:not(.prev):not(.next):focus, .comments-pagination a:not(.prev):not(.next):hover, .edit-link a:focus, .edit-link a:hover, .entry-content a:focus, .entry-content a:hover, .entry-footer .cat-links a, .entry-footer .cat-links a:focus, .entry-footer .cat-links a:hover, .entry-footer .tags-links a, .entry-footer .tags-links a:focus, .entry-footer .tags-links a:hover, .entry-footer a:focus, .entry-footer a:hover, .entry-meta a, .entry-meta a:focus, .entry-meta a:hover, .entry-summary a:focus, .entry-summary a:hover, .format-quote blockquote, .logged-in-as a:focus, .logged-in-as a:hover, .page-links a:focus .page-number, .page-links a:hover .page-number, .pagination a:not(.prev):not(.next):focus, .pagination a:not(.prev):not(.next):hover, .post-navigation a:focus, .post-navigation a:hover, .posts-navigation a:focus, .posts-navigation a:hover, .site-footer .widget-area a:focus, .site-footer .widget-area a:hover, .site-info a, .site-info a:focus, .site-info a:hover, .twentyseventeen-front-page .panel-content .recent-posts article, .widget a:focus, .widget a:hover, .widget ul li a:focus, .widget ul li a:hover, .widget_authors a:focus strong, .widget_authors a:hover strong, a, body, body.colors-dark, button, input, label, select,.colors-custom .colors-custom .taxonomy-description,body.colors-custom', 'color', 'paragraph_color');
            oftsCommon::generate_css('.colors-custom .entry-title a, .colors-custom .page .panel-content .entry-title, .colors-custom .page-title, .colors-custom h2, .colors-custom h2.widget-title, .colors-custom h3, .colors-custom h4, .colors-custom h6, .colors-custom.page:not(.twentyseventeen-front-page) .entry-title, .colors-dark .entry-title a, .colors-dark .page .panel-content .entry-title, .colors-dark .page-title, .colors-dark .site-title, .colors-dark .site-title a, .colors-dark .widget .widget-title a, .colors-dark h2.widget-title, .colors-dark.page:not(.twentyseventeen-front-page) .entry-title, .entry-title a, .entry-title a:focus, .entry-title a:hover, .page-title, .widget .widget-title a, h2, h3, h4, h6,.colors-dark h2', 'color', 'heading_color');

            if (get_theme_mod('external_header_video')) {

                add_action('wp_footer', array($this, 'ofts_disable_youtube_on_ie11'));

                if (!get_theme_mod('no_full_cover_header_video')) {

?>
.has-header-video .custom-header-media iframe {
    -o-object-fit: fill;
    object-fit: fill;
    top: auto;
    -ms-transform: none;
    -moz-transform: none;
    -webkit-transform: none;
    transform: none;
	left: auto;
}
@media (min-aspect-ratio: 16/9) {
    #wp-custom-header > iframe {
        height: 300%;
        top: -100%;
    }
}
@media (max-aspect-ratio: 16/9) {
    #wp-custom-header > iframe {
        width: 300%;
        left: -100%;
    }
}
<?php

                }

?>
.wp-custom-header .wp-custom-header-video-button {
    background-color: #a1a1a1;
    border-radius: 0;
    transition: none;
    color: white;
    border: 1px solid white;
}
.wp-custom-header .wp-custom-header-video-button:hover,
.wp-custom-header .wp-custom-header-video-button:focus {
    border-color: white;
    background-color: #555555;
    color: white;
}
<?php
            }

            if (get_theme_mod('footer_back_to_top') || get_option('show_on_front') == 'page') {

                add_action('wp_footer', array($this, 'ofts_inject_smooth_scrolling'));

            }

            if (get_theme_mod('front_page_sections_parallax_off')) {

                set_theme_mod('front_page_sections_parallax', 'off');
                remove_theme_mod('front_page_sections_parallax_off');

            }

            if (get_theme_mod('footer_back_to_top') || get_theme_mod('front_page_sections_back_to_top')) {
?>
.back-to-top {
	text-align: right;
}
.back-to-top a {
	-webkit-box-shadow: none;
	box-shadow: none;
}
.back-to-top a:hover {
	-webkit-box-shadow: none;
	box-shadow: none;
}
.back-to-top .icon {
	-webkit-transform: rotate(-90deg);
	-ms-transform: rotate(-90deg);
	transform: rotate(-90deg);
}
<?php
            }

            if (get_theme_mod('page_max_width') == '1200px') {

                set_theme_mod('page_max_width', '75em');

            }

            oftsCommon::generate_css('#page', 'max-width', 'page_max_width');

            if (get_theme_mod('full_cover_image') && get_theme_mod('full_cover_image') != 3 && get_theme_mod('page_max_width')) {

                oftsCommon::generate_css('.has-header-image .custom-header-media img, .has-header-video .custom-header-media video, .has-header-video .custom-header-media iframe', 'min-width', 'page_max_width', '', '', '0');

            }

            oftsCommon::generate_css('#page', 'margin', 'page_max_width', '', '' ,'0 auto');

            if (get_theme_mod('transparent_content_background')) {

?>
.colors-dark .site-content-contain, .colors-dark .navigation-top, .colors-dark .main-navigation ul,
.colors-custom .site-content-contain, .colors-custom .navigation-top, .colors-custom .main-navigation ul,
.site-content-contain, .navigation-top {
background-color: transparent;
}
<?php

            }

            oftsCommon::generate_css('body, body.colors-dark, body.colors-custom', 'background-color', 'page_background_color');

            if (self::ofts_has_no_header_image_or_nivo_slider()) {

                oftsCommon::generate_css('#page', 'border-width', 'page_border_width', '', 'px');
                oftsCommon::generate_css('#page', 'border-color', 'page_border_color');
                oftsCommon::generate_css('#page', 'border-style', 'page_border_style');
                oftsCommon::generate_css('#page', 'border-width', 'page_border_location');

                $mod = get_theme_mod('page_border_location');

                if ($mod) {
?>
#page {
    <?php echo $mod; ?>
}
<?php
                }

            }

            $mod = get_theme_mod('remove_link_underlines');

            if ('unordered' === $mod) {

?>
.widget ul li a {
    box-shadow: inset 0 -1px 0 rgba(15, 15, 15, 1);
}
<?php

            } elseif ($mod) {

?>
.screen-reader-text:focus {
	box-shadow: none;
}
.entry-content a,
.entry-summary a,
.comment-content a,
.widget a,
.site-footer .widget-area a,
.posts-navigation a,
.widget_authors a strong {
	box-shadow: none;
}
.entry-title a,
.entry-meta a,
.page-links a,
.page-links a .page-number,
.entry-footer a,
.entry-footer .cat-links a,
.entry-footer .tags-links a,
.edit-link a,
.post-navigation a,
.logged-in-as a,
.comment-navigation a,
.comment-metadata a,
.comment-metadata a.comment-edit-link,
.comment-reply-link,
a .nav-title,
.pagination a,
.comments-pagination a,
.site-info a,
.widget .widget-title a,
.widget ul li a,
.site-footer .widget-area ul li a,
.site-footer .widget-area ul li a {
	box-shadow: none;
}
.entry-content a:focus,
.entry-content a:hover,
.entry-summary a:focus,
.entry-summary a:hover,
.comment-content a:focus,
.comment-content a:hover,
.widget a:focus,
.widget a:hover,
.site-footer .widget-area a:focus,
.site-footer .widget-area a:hover,
.posts-navigation a:focus,
.posts-navigation a:hover,
.comment-metadata a:focus,
.comment-metadata a:hover,
.comment-metadata a.comment-edit-link:focus,
.comment-metadata a.comment-edit-link:hover,
.comment-reply-link:focus,
.comment-reply-link:hover,
.widget_authors a:focus strong,
.widget_authors a:hover strong,
.entry-title a:focus,
.entry-title a:hover,
.entry-meta a:focus,
.entry-meta a:hover,
.page-links a:focus .page-number,
.page-links a:hover .page-number,
.entry-footer a:focus,
.entry-footer a:hover,
.entry-footer .cat-links a:focus,
.entry-footer .cat-links a:hover,
.entry-footer .tags-links a:focus,
.entry-footer .tags-links a:hover,
.post-navigation a:focus,
.post-navigation a:hover,
.pagination a:not(.prev):not(.next):focus,
.pagination a:not(.prev):not(.next):hover,
.comments-pagination a:not(.prev):not(.next):focus,
.comments-pagination a:not(.prev):not(.next):hover,
.logged-in-as a:focus,
.logged-in-as a:hover,
a:focus .nav-title,
a:hover .nav-title,
.edit-link a:focus,
.edit-link a:hover,
.site-info a:focus,
.site-info a:hover,
.widget .widget-title a:focus,
.widget .widget-title a:hover,
.widget ul li a:focus,
.widget ul li a:hover {
	box-shadow: none;
}
.entry-content a img,
.comment-content a img,
.widget a img {
	box-shadow: none;
}
.colors-dark .entry-content a,
.colors-dark .entry-summary a,
.colors-dark .comment-content a,
.colors-dark .widget a,
.colors-dark .site-footer .widget-area a,
.colors-dark .posts-navigation a,
.colors-dark .widget_authors a strong {
	box-shadow: none;
}
.colors-custom .entry-content a:hover,
.colors-custom .entry-content a:focus,
.colors-custom .entry-summary a:hover,
.colors-custom .entry-summary a:focus,
.colors-custom .comment-content a:focus,
.colors-custom .comment-content a:hover,
.colors-custom .widget a:hover,
.colors-custom .widget a:focus,
.colors-custom .site-footer .widget-area a:hover,
.colors-custom .site-footer .widget-area a:focus,
.colors-custom .posts-navigation a:hover,
.colors-custom .posts-navigation a:focus,
.colors-custom .widget_authors a:hover strong,
.colors-custom .widget_authors a:focus strong {
    box-shadow: none;
}
<?php
            }

            if (get_theme_mod('search_images')) {

                add_action('get_template_part_template-parts/post/content', array($this, 'ofts_search_images'), 10, 3);

            }

            oftsCommon::generate_css('.custom-header .wrap, .header-sidebar-wrap', 'max-width', 'header_width');
            oftsCommon::generate_css('.wp-custom-header-video-button', 'display', 'remove_header_video_button', '', '', 'none');
            oftsCommon::generate_css('.site-branding', 'background-color', 'site_identity_background_color');
            oftsCommon::generate_css('.site-branding a:hover, .site-branding a:focus', 'opacity', 'remove_link_hover_opacity');

            if (get_theme_mod('full_cover_image') === true) {

                set_theme_mod('full_cover_image', '1');

            }

            if (self::ofts_has_header_image_or_nivo_slider()) {

                if (get_theme_mod('full_cover_image') === '1' && !get_theme_mod('external_header_video')) {

?>
.twentyseventeen-front-page.has-header-image .custom-header-media, .admin-bar.home.blog.has-header-image .custom-header-media, .admin-bar.twentyseventeen-front-page.has-header-image .custom-header-media, .has-header-image .custom-header-media img, .has-header-image.home.blog .custom-header, .has-header-image.twentyseventeen-front-page .custom-header, .has-header-image .custom-header-media img, .has-header-image .custom-header-media, .has-header-video .custom-header-media iframe, .has-header-video .custom-header-media video, .has-header-video.home.blog .custom-header, .has-header-video.twentyseventeen-front-page .custom-header, .has-header-video .custom-header-media .has-header-image.twentyseventeen-front-page .custom-header {
	position: static;
	height: auto;
}
.has-header-image.twentyseventeen-front-page .site-branding, .has-header-video.twentyseventeen-front-page .site-branding, .has-header-image.home.blog .site-branding, .has-header-video.home.blog .site-branding {
	position: static;
	padding: 3em 0;
	display: block;
}
body.has-header-image .site-title, body.has-header-video .site-title, body.has-header-image .site-title a, body.has-header-video .site-title a, .site-title a, .colors-dark .site-title a, .colors-custom .site-title a, body.has-header-image .site-title a, body.has-header-video .site-title a, body.has-header-image.colors-dark .site-title a, body.has-header-video.colors-dark .site-title a, body.has-header-image.colors-custom .site-title a, body.has-header-video.colors-custom .site-title a, .colors-dark .site-title, .colors-dark .site-title a {
	color: #222;
}
.site-description, .colors-dark .site-description, body.has-header-image .site-description, body.has-header-video .site-description, .site-description, .colors-dark .site-description, .colors-custom .site-description, body.has-header-image .site-description, body.has-header-video .site-description, body.has-header-image.colors-dark .site-description, body.has-header-video.colors-dark .site-description, body.has-header-image.colors-custom .site-description, body.has-header-video.colors-custom .site-description {
	color: #666;
}
.navigation-top {
    z-index: 7;
}
<?php

                    if (get_theme_mod('header_textcolor') == 'blank') {

?>
.has-header-image.twentyseventeen-front-page .site-branding, .has-header-video.twentyseventeen-front-page .site-branding, .has-header-image.home.blog .site-branding, .has-header-video.home.blog .site-branding {
    padding: 0;
}
<?php

                    }

                } elseif (get_theme_mod('full_cover_image') === '2' && !get_theme_mod('external_header_video')) {

?>
@media screen and (min-width: 48em) {
    .has-header-image .custom-header-media img {
        position: static;
        display: block
        position: relative !important;
    }
    .twentyseventeen-front-page.has-header-image .custom-header-media, .home.blog.has-header-image .custom-header-media, .admin-bar.twentyseventeen-front-page.has-header-image .custom-header-media, .admin-bar.home.blog.has-header-image .custom-header-media {
        height: auto;
    }
}
<?php

                } elseif (get_theme_mod('full_cover_image') === '3' && !get_theme_mod('external_header_video')) {

                    add_filter('body_class', array($this, 'ofts_remove_home_class'), 11, 2 );

                }

            }

            oftsCommon::generate_css('.site-branding', 'text-align', 'site_branding_text_align');

            if ('center' === get_theme_mod('site_branding_text_align')) {

?>
.custom-logo-link {
    padding-right: 0;
}
<?php

            }

            oftsCommon::generate_css('.site-branding-text', 'text-align', 'site_branding_text_align', '', '', 'left');
            oftsCommon::generate_css('.site-title', 'text-align', 'site_title_text_align');
            oftsCommon::generate_css('body:not(.title-tagline-hidden) .site-branding-text', 'display', 'site_title_text_align', '', '', 'block');
            oftsCommon::generate_css('.site-title', 'text-transform', 'site_title_text_transform');
            oftsCommon::generate_css('.site-title', 'letter-spacing', 'remove_site_title_letter_spacing', '', '', 'normal');

            $mod = get_theme_mod('site_title_font_size');

            if ($mod) {
?>
.site-title {
    font-size: <?php echo $mod / 3000 * 2; ?>rem;
}
@media screen and (min-width: 48em) {
    .site-title {
        font-size: <?php echo $mod / 1000; ?>rem;
    }
}
<?php
            }

            oftsCommon::generate_css('.site-title', 'font-size', 'site_title_font_size', '', 'rem', get_theme_mod('site_title_font_size') / 1000);
            oftsCommon::generate_css('.site-title', 'font-weight', 'site_title_font_weight');
            oftsCommon::generate_css('body.has-header-image .site-title, body.has-header-video .site-title, body.has-header-image .site-title a, body.has-header-video .site-title a, .site-title a, .colors-dark .site-title a, .colors-custom .site-title a, body.has-header-image .site-title a, body.has-header-video .site-title a, body.has-header-image.colors-dark .site-title a, body.has-header-video.colors-dark .site-title a, body.has-header-image.colors-custom .site-title a, body.has-header-video.colors-custom .site-title a, .colors-dark .site-title, .colors-dark .site-title a', 'color', 'site_title_color');
            oftsCommon::generate_css('.site-description', 'text-align', 'site_description_text_align');
            oftsCommon::generate_css('.site-description', 'text-transform', 'site_description_text_transform');

            $mod = get_theme_mod('site_description_font_size');

            if ($mod) {
?>
.site-description {
    font-size: <?php echo $mod * 0.0008125; ?>rem;
}
@media screen and (min-width: 48em) {
    .site-description {
        font-size: <?php echo $mod / 1000; ?>rem;
    }
}
<?php
            }

            oftsCommon::generate_css('.site-description', 'font-weight', 'site_description_font_weight');
            oftsCommon::generate_css('.site-description, .colors-dark .site-description, body.has-header-image .site-description, body.has-header-video .site-description, .site-description, .colors-dark .site-description, .colors-custom .site-description, body.has-header-image .site-description, body.has-header-video .site-description, body.has-header-image.colors-dark .site-description, body.has-header-video.colors-dark .site-description, body.has-header-image.colors-custom .site-description, body.has-header-video.colors-custom .site-description', 'color', 'site_description_color');
            oftsCommon::generate_css('.custom-header-media:before', 'display', 'remove_header_gradient', '', '', 'none');

            $mod = absint(get_theme_mod('header_gradient_height'));

            if ($mod) {

?>
@media screen and (min-width: 48em) {
.twentyseventeen-front-page.has-header-image .custom-header-media:before, .twentyseventeen-front-page.has-header-video .custom-header-media:before, .home.blog.has-header-image .custom-header-media:before, .home.blog.has-header-video .custom-header-media:before {
height: <?php echo $mod; ?>%;
}
}
<?php

            }

            $gradient_color = get_theme_mod('header_gradient_color');
            $gradient_opacity = absint(get_theme_mod('header_gradient_opacity'));

            if ($gradient_color || $gradient_opacity) {

                if (!$gradient_color) { $gradient_color = '#000000'; }

                if (!$gradient_opacity) { $gradient_opacity = 30; }

                $gradient_opacity = $gradient_opacity / 100;
                list($r, $g, $b) = array_map('hexdec', str_split(ltrim($gradient_color, '#'), 2));

?>
.custom-header-media:before {
background: linear-gradient(to bottom, rgba(<?php echo $r; ?>, <?php echo $g; ?>, <?php echo $b; ?>, 0) 0%, rgba(<?php echo $r; ?>, <?php echo $g; ?>, <?php echo $b; ?>, <?php echo $gradient_opacity; ?>) 75%, rgba(<?php echo $r; ?>, <?php echo $g; ?>, <?php echo $b; ?>, <?php echo $gradient_opacity; ?>) 100%);
}
<?php

            }

            oftsCommon::generate_css('.site-header', 'background', 'remove_header_background', '', '', 'none');

            if (get_theme_mod('animate_nav_logo') == '1') {

                set_theme_mod('animate_nav_logo', 'all');

            }

            if (get_theme_mod('full_width_nav_bar')) {

                set_theme_mod('nav_bar_width', '100%');
                remove_theme_mod('full_width_nav_bar');

            }

            if (get_theme_mod('prevent_nav_transition')) {

?>
.site-branding {
    transition: none;
}
<?php

            }

            oftsCommon::generate_css('.navigation-top .wrap', 'max-width', 'nav_bar_width');

            if (get_theme_mod('nav_bar_width') && get_theme_mod('sticky_nav_bar_width')) {

?>
@media screen and (min-width: 48em) {
    .site-navigation-fixed.navigation-top {
        width: <?php echo get_theme_mod('nav_bar_width'); ?>;
        left: 50%;
        transform: translate(-50%, 0);
    }
}
<?php

            }

            $mod = get_theme_mod('nav_background_image');

            if ($mod) {

?>
.navigation-top>div>nav>div>ul, .colors-dark .navigation-top>div>nav>div>ul, .colors-custom .navigation-top>div>nav>div>ul {
background-color: transparent;
}
.navigation-top, .colors-dark .navigation-top, .colors-custom .navigation-top {
background-image: url("<?php echo $mod; ?>");
}
.navigation-top {
    border: none;
}
<?php

            }

            if (get_theme_mod('nav_background_image_style')) {

?>
.navigation-top, .main-navigation ul, .colors-dark .navigation-top, .colors-dark .main-navigation ul, .colors-custom .navigation-top, .colors-custom .main-navigation ul {
background-repeat: repeat;
}
<?php

            } else {

?>
.navigation-top, .main-navigation ul, .colors-dark .navigation-top, .colors-dark .main-navigation ul, .colors-custom .navigation-top, .colors-custom .main-navigation ul {
background-size: cover;
background-position: center center;
background-repeat: no-repeat;
}
<?php

            }

            if (get_theme_mod('nav_remove_padding_vertical')) {

?>
@media screen and (min-width: 48em) {
	.navigation-top .wrap {
		padding-top: 0;
		padding-bottom: 0;
	}
}
<?php

            }

            oftsCommon::generate_css('.navigation-top a', 'text-transform', 'navigation_text_transform');

            $mod = get_theme_mod('navigation_font_size');

            if ($mod) {

?>
.navigation-top {
    font-size: <?php echo ($mod + 125) / 1000; ?>rem;
}
@media screen and (min-width: 48em) {
    .navigation-top {
        font-size: <?php echo $mod / 1000; ?>rem;
    }
}
<?php

            }

            oftsCommon::generate_css('.navigation-top a', 'font-weight', 'navigation_font_weight');

            $mod = absint(get_theme_mod('nav_link_padding_vertical'));

            if ($mod) {

?>
@media screen and (min-width: 48em) {
	.main-navigation a {
		padding-top: <?php echo $mod - 1; ?>px;
		padding-bottom: <?php echo $mod - 1; ?>px;
	}
}
<?php

            }

            oftsCommon::generate_css('.navigation-top a, .colors-dark .navigation-top a, .colors-custom .navigation-top a', 'color', 'nav_link_color', '', '');
            oftsCommon::generate_css('.dropdown-toggle, .menu-toggle, .colors-dark .menu-toggle, .colors-custom .menu-toggle', 'color', 'nav_link_color', '', '');
            oftsCommon::generate_css('.navigation-top .current-menu-item > a, .navigation-top .current_page_item > a, .colors-dark .navigation-top .current-menu-item > a, .colors-dark .navigation-top .current_page_item > a, .colors-custom .navigation-top .current-menu-item > a, .colors-custom .navigation-top .current_page_item > a', 'color', 'nav_current_link_color', '', '');
            oftsCommon::generate_css('.dropdown-toggle:hover, .navigation-top a:hover, .main-navigation li li.focus > a, .main-navigation li li:focus > a, .main-navigation li li:hover > a, .main-navigation li li a:hover, .main-navigation li li a:focus, .main-navigation li li.current_page_item a:hover, .main-navigation li li.current-menu-item a:hover, .main-navigation li li.current_page_item a:focus, .main-navigation li li.current-menu-item a:focus, .colors-dark .navigation-top a:hover, .colors-dark .main-navigation li li.focus > a, .colors-dark .main-navigation li li:focus > a, .colors-dark .main-navigation li li:hover > a, .colors-dark .main-navigation li li a:hover, .colors-dark .main-navigation li li a:focus, .colors-dark .main-navigation li li.current_page_item a:hover, .colors-dark .main-navigation li li.current-menu-item a:hover, .colors-dark .main-navigation li li.current_page_item a:focus, .colors-dark .main-navigation li li.current-menu-item a:focus, .colors-custom .navigation-top a:hover, .colors-custom .main-navigation li li.focus > a, .colors-custom .main-navigation li li:focus > a, .colors-custom .main-navigation li li:hover > a, .colors-custom .main-navigation li li a:hover, .colors-custom .main-navigation li li a:focus, .colors-custom .main-navigation li li.current_page_item a:hover, .colors-custom .main-navigation li li.current-menu-item a:hover, .colors-custom .main-navigation li li.current_page_item a:focus, .colors-custom .main-navigation li li.current-menu-item a:focus', 'color', 'nav_link_hover_color', '', '');

            $mod = get_theme_mod('nav_link_hover_background_color');

            if ($mod) {

?>
@media screen and (min-width: 48em) {
	.main-navigation li, .colors-dark .main-navigation li, .colors-custom .main-navigation li {
		-webkit-transition: background-color 0.2s ease-in-out;
		transition: background-color 0.2s ease-in-out;
	}
    .main-navigation li:hover, .main-navigation li.focus, .main-navigation li li:hover, .main-navigation li li.focus,
    .colors-dark .main-navigation li:hover, .colors-dark .main-navigation li.focus, .colors-dark .main-navigation li li:hover, .colors-dark .main-navigation li li.focus,
    .colors-custom .main-navigation li:hover, .colors-custom .main-navigation li.focus, .colors-custom .main-navigation li li:hover, .colors-custom .main-navigation li li.focus {
        background-color: <?php echo $mod; ?>;
    }
}
<?php

            }

            $background_color = get_theme_mod('nav_background_color');

            if ($background_color) {

                oftsCommon::generate_css('.navigation-top, .main-navigation ul, .colors-dark .navigation-top, .colors-dark .main-navigation ul, .colors-custom .navigation-top, .colors-custom .main-navigation ul', 'background-color', 'nav_background_color', '', '');

?>
@media screen and (min-width: 48em) {
    .main-navigation ul ul, .colors-dark .main-navigation ul ul, .colors-custom .main-navigation ul ul {
        background-color: <?php echo $background_color; ?>;
		border: none;
    }
}
.navigation-top {
    border: none;
}
<?php

                if (!get_theme_mod('nav_hamburger_align')) {

?>
.menu-toggle {
	margin: 0 auto 0;
}
<?php

                }

            }

            $background_opacity = get_theme_mod('nav_background_opacity');

            if ($background_opacity) {

?>
.main-navigation ul, .colors-dark .main-navigation ul, .colors-custom .main-navigation ul {
	background: none;
}
<?php

                $background_opacity = (100 - absint($background_opacity)) / 100;

                if ($background_color) {

                    list($r, $g, $b) = sscanf($background_color, "#%02x%02x%02x");
                    oftsCommon::generate_css('.navigation-top, .colors-dark .navigation-top, .colors-custom .navigation-top', 'background-color', 'nav_background_color', '', '', "rgba($r, $g, $b, $background_opacity)");

                } else {

?>
.navigation-top {
	background-color: rgba(255, 255, 255, <?php echo $background_opacity; ?>);
	border-top-color: rgba(238, 238, 238, <?php echo $background_opacity; ?>);
	border-bottom-color: rgba(238, 238, 238, <?php echo $background_opacity; ?>);
}
.colors-dark .navigation-top, .colors-custom .navigation-top {
	background-color: rgba(34, 34, 34, <?php echo $background_opacity; ?>);
	border-top-color: rgba(51, 51, 51, <?php echo $background_opacity; ?>);
	border-bottom-color: rgba(51, 51, 51, <?php echo $background_opacity; ?>);
}
<?php

                    if (get_theme_mod('colorscheme') !== 'custom') {

	                    $hue = absint(get_theme_mod( 'colorscheme_hue', 250));
	                    $saturation = absint(apply_filters( 'twentyseventeen_custom_colors_saturation', 50));
	                    $reduced_saturation = (.8 * $saturation) . '%';
                    	$saturation = $saturation . '%';

?>
.colors-custom .navigation-top {
	background-color: background: hsla(' . $hue . ', ' . $saturation . ', 100%, <?php echo $background_opacity; ?>);
	border-top-color: hsla(' . $hue . ', ' . $saturation . ', 93%, <?php echo $background_opacity; ?>);
	border-bottom-color: hsla(' . $hue . ', ' . $saturation . ', 93%, <?php echo $background_opacity; ?>);
}
<?php

                    }

                }

            }

            $mod = get_theme_mod('sub_menu_background_color');

            if ($mod) {
?>
@media screen and (min-width: 48em) {
	.main-navigation ul ul, .colors-dark .main-navigation ul ul, .colors-custom .main-navigation ul ul {
		background-color: <?php echo $mod; ?>;
		border: none;
	}
	.main-navigation ul li:hover > ul,
	.main-navigation ul li.focus > ul {
		left: 0;
	}
}
<?php

            }

            if (get_theme_mod('rotate_sub_menu_arrow')) {

?>
@media screen and (min-width: 48em) {
    .main-navigation ul li.menu-item-has-children:before,
	.main-navigation ul li.page_item_has_children:before,
	.main-navigation ul li.menu-item-has-children:after,
	.main-navigation ul li.page_item_has_children:after {
        transform: rotate(180deg);
		bottom: -7px;
	}
}
<?php

            }

            if (get_theme_mod('remove_nav_scroll_arrow')) {

?>
@media screen and (min-width: 48em) {
	.site-header .menu-scroll-down {
		display: none;
	}
}
<?php

            }

            if (get_theme_mod('full_width_content')) {

                set_theme_mod('header_width', '100%');
                set_theme_mod('content_width', '100%');
                set_theme_mod('footer_width', '100%');
                remove_theme_mod('full_width_content');

            }

            oftsCommon::generate_css('#content .wrap', 'max-width', 'content_width');

            oftsCommon::generate_css('#content', 'background-color', 'content_background_color');

            $mod = get_theme_mod('content_width');

            if ($mod) {

?>
@media screen and (min-width: 30em) {
    .page-one-column .panel-content .wrap {
        max-width: <?php echo $mod; ?>;
    }
}
<?php
            }

            if (get_theme_mod('page_sidebar') && is_active_sidebar('sidebar-1')) {

if (isset($post->ID) && !get_post_meta($post->ID, 'ofts_hide_page_sidebar', true) == '1') {

                add_action('get_template_part_template-parts/page/content', array($this, 'ofts_get_blog_sidebar'), 10, 2);
                add_filter('body_class', array($this, 'ofts_sidebar_body_class'));
?>
.page-template-default #secondary {
    display: none;
}
.twentyseventeen-front-page .site-content  {
    padding: 2.5em 0 0;
}
@media screen and (min-width: 48em) {
    .twentyseventeen-front-page .site-content  {
        padding: 5.5em 0 0;
	}
}
.twentyseventeen-front-page article:not(.has-post-thumbnail):not(:first-child) {
    border: none;
}
.panel-content {
    padding-top: 6em;
}
#main > [id^=post] .panel-content {
    padding-top: 0;
}
<?php

}

            }

            $mod = get_theme_mod('content_padding_top');

            if ($mod) {

?>
.site-content, .panel-content .wrap {
    padding-top: <?php echo number_format(((($mod / 2) - 0.5) / 5.5 * 2.5), 3); ?>em;
}
@media screen and (min-width: 48em) {
    .site-content, .panel-content .wrap {
        padding-top: <?php echo (($mod / 2) - 0.5); ?>em;
    }
}
<?php

            }

            oftsCommon::generate_css('.single-featured-image-header', 'border-bottom-width', 'featured_image_border_width', '', 'px', (get_theme_mod('featured_image_border_width') ? (absint(get_theme_mod('featured_image_border_width')) - 1) : 1));

            oftsCommon::generate_css('.archive .page-header .page-title, .home .page-header .page-title', 'text-align', 'page_header_title_text_align');
            oftsCommon::generate_css('.archive .page-header .page-title, .home .page-header .page-title', 'text-transform', 'page_header_title_text_transform');

            if (get_theme_mod('page_header_title_letter_spacing')) {

                set_theme_mod('remove_page_header_title_letter_spacing', get_theme_mod('page_header_title_letter_spacing'));
                remove_theme_mod('page_header_title_letter_spacing');

            }

            oftsCommon::generate_css('.archive .page-header .page-title, .home .page-header .page-title, .blog .page-header .page-title', 'letter-spacing', 'remove_page_header_title_letter_spacing', '', '', 'normal');
            oftsCommon::generate_css('.archive .page-header .page-title, .home .page-header .page-title, .blog .page-header .page-title', 'font-size', 'page_header_title_font_size', '', 'rem', get_theme_mod('page_header_title_font_size') / 1000);
            oftsCommon::generate_css('.archive .page-header .page-title, .home .page-header .page-title, .blog .page-header .page-title', 'font-weight', 'page_header_title_font_weight');
            oftsCommon::generate_css('.archive .page-header .page-title, .home .page-header .page-title, .blog .page-header .page-title', 'color', 'page_header_title_color');
            oftsCommon::generate_css('.entry-meta, .entry-footer .cat-links, .entry-footer .tags-links', 'text-align', 'post_meta_text_align');
            oftsCommon::generate_css('.entry-meta, .entry-footer .cat-links, .entry-footer .tags-links', 'text-transform', 'post_meta_text_transform');
            oftsCommon::generate_css('.entry-meta, .entry-footer .cat-links, .entry-footer .tags-links', 'font-size', 'post_meta_font_size', '', 'rem', get_theme_mod('post_meta_font_size') / 10000);
            oftsCommon::generate_css('.entry-meta, .entry-footer .cat-links, .entry-footer .tags-links', 'font-weight', 'post_meta_font_weight');
            oftsCommon::generate_css('.entry-meta, .entry-footer .cat-links, .entry-footer .tags-links, .entry-meta a, .entry-footer .cat-links a, .entry-footer .tags-links a, .entry-footer .cat-links .icon, .entry-footer .tags-links .icon', 'color', 'post_meta_color');
            oftsCommon::generate_css('.post .entry-header .entry-title', 'text-align', 'post_entry_header_title_text_align');
            oftsCommon::generate_css('.post .entry-header .entry-title', 'text-transform', 'post_entry_header_title_text_transform');
            oftsCommon::generate_css('.post .entry-header .entry-title', 'font-size', 'post_entry_header_title_font_size', '', 'rem', get_theme_mod('post_entry_header_title_font_size') / 1000);
            oftsCommon::generate_css('.post .entry-header .entry-title', 'font-weight', 'post_entry_header_title_font_weight');
            oftsCommon::generate_css('.post .entry-header .entry-title, .archive .entry-header .entry-title a, .blog .entry-header .entry-title a', 'color', 'post_entry_header_title_color');
            oftsCommon::generate_css('body.page .entry-header .entry-title', 'text-align', 'page_entry_header_title_text_align');
            oftsCommon::generate_css('body.page .entry-header .entry-title', 'text-transform', 'page_entry_header_title_text_transform');

            if (get_theme_mod('page_entry_header_title_letter_spacing')) {

                set_theme_mod('remove_page_entry_header_title_letter_spacing', get_theme_mod('page_entry_header_title_letter_spacing'));
                remove_theme_mod('page_entry_header_title_letter_spacing');

            }

            oftsCommon::generate_css('body.page .entry-header .entry-title', 'letter-spacing', 'remove_page_entry_header_title_letter_spacing', '', '', 'normal');
            oftsCommon::generate_css('body.page .entry-header .entry-title', 'font-size', 'page_entry_header_title_font_size', '', 'rem', get_theme_mod('page_entry_header_title_font_size') / 1000);
            oftsCommon::generate_css('body.page .entry-header .entry-title', 'font-weight', 'page_entry_header_title_font_weight');
            oftsCommon::generate_css('body.page .entry-header .entry-title, .colors-dark .page .panel-content .entry-title, .colors-dark.page:not(.twentyseventeen-front-page) .entry-title, .colors-custom .page .panel-content .entry-title, .colors-custom.page:not(.twentyseventeen-front-page) .entry-title', 'color', 'page_entry_header_title_color');

            $mod = get_theme_mod('page_entry_header_title_margin_bottom');

            if ($mod) {

?>
@media screen and (min-width: 48em) {
    .page.page-one-column .entry-header, .twentyseventeen-front-page.page-one-column .entry-header, .archive.page-one-column:not(.has-sidebar) .page-header {
        margin-bottom: <?php echo (($mod / 2) - 0.5); ?>em;
    }
}
<?php

            }

            oftsCommon::generate_css('.entry-content a', 'color', 'content_link_color');
            oftsCommon::generate_css('.entry-content a:hover', 'color', 'content_hover_color');
            oftsCommon::generate_css('.entry-footer .cat-links', 'display', 'hide_categories', '', '', 'none');
            oftsCommon::generate_css('.entry-footer .tags-links', 'display', 'hide_tags', '', '', 'none');
            oftsCommon::generate_css('.post-navigation', 'display', 'hide_post_navigation', '', '', 'none');

            oftsCommon::generate_css('footer .wrap', 'max-width', 'footer_width');
            oftsCommon::generate_css('.site-footer', 'border-top-width', 'footer_border_width', '', 'px', (get_theme_mod('footer_border_width') ? (absint(get_theme_mod('footer_border_width')) - 1) : 1));

            oftsCommon::generate_css('.site-footer', 'background-color', 'footer_background_color');

            oftsCommon::generate_css('.site-footer h2', 'color', 'footer_title_color');
            oftsCommon::generate_css('.site-footer', 'color', 'footer_text_color');
            oftsCommon::generate_css('.site-info a, .site-footer .widget-area a', 'color', 'footer_link_color');
            oftsCommon::generate_css('.site-info a:hover, .site-footer .widget-area a:hover, .colors-dark .site-info a:hover, .colors-dark .site-footer .widget-area a:hover, .colors-custom .site-info a:hover, .colors-custom .site-footer .widget-area a:hover', 'color', 'footer_link_hover_color');

            if ('1' !== get_theme_mod('remove_link_underlines') && true !== get_theme_mod('remove_link_underlines')) {

                oftsCommon::generate_css('.site-info a, .site-footer .widget-area a', '-webkit-box-shadow', 'footer_link_color', 'inset 0 -1px 0 ');
                oftsCommon::generate_css('.site-info a, .site-footer .widget-area a', 'box-shadow', 'footer_link_color', 'inset 0 -1px 0 ');
                oftsCommon::generate_css('.site-info a:hover, .site-footer .widget-area a:hover', '-webkit-box-shadow', 'footer_link_hover_color', '', '', 'inset 0 0 0 ' . get_theme_mod('footer_link_hover_color') . ', 0 3px 0 ' . get_theme_mod('footer_link_hover_color'));
                oftsCommon::generate_css('.site-info a:hover, .site-footer .widget-area a:hover', 'box-shadow', 'footer_link_hover_color', '', '', 'inset 0 0 0 ' . get_theme_mod('footer_link_hover_color') . ', 0 3px 0 ' . get_theme_mod('footer_link_hover_color'));

            }

            $mod = get_theme_mod('remove_powered_by_wordpress');

            if ($mod) {

                add_action('get_template_part_template-parts/footer/site', array($this, 'ofts_get_site_info_sidebar'));

?>
.site-info:last-child a:last-child {
    display: none;
}
.site-info:last-child span {
    display: none;
}
.site-info p {
    margin: 0;
}
<?php

            }

?>
</style> 
<!--/Customizer CSS-->
<?php

        }

        function ofts_wp_footer() {

            if (get_theme_mod('prevent_nav_transition')) {

?>
<script type="text/javascript">
(function() {
    var navigationTop = document.getElementsByClassName('navigation-top')[0],
        menuToggle = navigationTop.getElementsByClassName('menu-toggle'),
        customHeader = document.getElementsByClassName('custom-header')[0],
        siteBranding = customHeader.getElementsByClassName('site-branding')[0];
    if (menuToggle.length && 'none' === window.getComputedStyle(menuToggle[0]).display) {
        if (document.body.classList.contains('twentyseventeen-front-page') || (document.body.classList.contains('home') && document.body.classList.contains('blog'))) {
            siteBranding.style.marginBottom = Number(navigationTop.getBoundingClientRect().height) + 'px';
        } else {
            customHeader.style.marginBottom = Number(navigationTop.getBoundingClientRect().height) + 'px';
        }
    } else {
        customHeader.style.marginBottom = '0px';
        siteBranding.style.marginBottom = '0px';
    }
})();
</script>
<?php

            }

            if (is_front_page()) {

?>
<script type="text/javascript">
    if (navigator.userAgent.match(/Trident\/7\./)) {
        jQuery('body').on("mousewheel", function () {
            event.preventDefault();
            var wheelDelta = event.wheelDelta;
            var currentScrollPosition = window.pageYOffset;
            window.scrollTo(0, currentScrollPosition - wheelDelta);
        });
    }
</script>
<?php

            }

            if (is_search() && get_theme_mod('search_images')) {

?>
<script type="text/javascript">
    (function($) {
        $('#primary>#main>.post-thumbnail').each(function() {
            $(this).insertAfter($(this).next('article').find('.entry-header'));
        });
    })(jQuery);
</script>
<?php

            }

        }

        function ofts_disable_youtube_on_ie11() {

             if (is_front_page()) {

?>
<script type="text/javascript">
	var isIE11 = !!window.MSInputMethodContext && !!document.documentMode;
	if (isIE11 && jQuery('.twentyseventeen-front-page').length > 0 && jQuery(window).width() >= 900) {
    	var wp_custom_header_html = jQuery('#wp-custom-header').html();
    	jQuery('#wp-custom-header-video').hide();
    	var ie_remove_youtube_timer = setTimeout(function ie_remove_youtube() {
    		if (jQuery("#wp-custom-header img").length == 0) {
    			jQuery('#wp-custom-header-video').remove();
    			jQuery('#wp-custom-header').append(jQuery(wp_custom_header_html));
    		} else {
    			ie_remove_youtube_timer = setTimeout(ie_remove_youtube, 50);
    		}
    	}, 50);
    }
</script>
<?php

             }

        }

        function ofts_header_sidebar_init() {
        	register_sidebar( array(
        		'name'          => __('Header', 'options-for-twenty-seventeen'),
        		'id'            => 'header',
        		'description'   => __('Add widgets here to appear in your header.', 'options-for-twenty-seventeen'),
		        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        		'after_widget'  => '</section>',
        		'before_title'  => '<h2 class="widget-title">',
        		'after_title'   => '</h2>',
        	) );
        }

        function ofts_remove_home_class($classes, $class) {

            if (($key = array_search('home', $classes)) !== false) {

                unset($classes[$key]);

            }

            if (($key = array_search('twentyseventeen-front-page', $classes)) !== false) {

                unset($classes[$key]);

            }

            return $classes;

        }

        function ofts_get_blog_sidebar($slug, $name) {

            if ($name != 'front-page-panels') {

                get_sidebar();

?>
<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#secondary").appendTo(jQuery("#primary").parent());
        if (jQuery("body").hasClass("twentyseventeen-front-page")) {
            jQuery("#primary, #secondary").wrapAll("<div class='wrap'></div>");
            jQuery(".panel-content > .wrap > *").unwrap();
        }
        jQuery("#secondary").show();
    });
</script>
<?php

            }

        }

        function ofts_site_info_sidebar_init() {
        	register_sidebar( array(
        		'name'          => __('Site Info', 'options-for-twenty-seventeen'),
        		'id'            => 'site-info',
        		'description'   => __('Add widgets here to appear in your footer site info.', 'options-for-twenty-seventeen'),
		        'before_widget' => '',
        		'after_widget'  => '',
        		'before_title'  => '<h2 class="widget-title">',
        		'after_title'   => '</h2>',
        	) );
        }

        function ofts_get_site_info_sidebar() {

            if (is_active_sidebar('site-info')) {

                echo('<div class="site-info">');
                dynamic_sidebar('site-info');
                echo('</div>');

            }

        }

        function ofts_sidebar_body_class($classes) {

            if (is_page()) {

    		    $classes[] = 'has-sidebar';

            }

            return $classes;

        }

        function ofts_enqueue_customize_preview_js() {

            wp_enqueue_script('ofts-customize-preview', plugin_dir_url(__FILE__) . 'js/customize-preview.js', array('jquery', 'customize-preview'), oftsCommon::plugin_version(), true);

        }

        function ofts_enqueue_customizer_css() {

            wp_enqueue_style('ofts-customizer-css', plugin_dir_url(__FILE__) . 'css/theme-customizer.css', oftsCommon::plugin_version());

            if (class_exists('AdvancedTwentySeventeen') && !get_theme_mod('allow_ats_js')) {

                wp_deregister_script('wp-color-picker-alpha');

            }

        }

        function ofts_twentyseventeen_default_image_setup() {

        	register_default_headers(
        		array(
        			'default-image' => array(
        				'url'           => plugin_dir_url(__FILE__) . 'images/header.jpg',
        				'thumbnail_url' => plugin_dir_url(__FILE__) . 'images/header.jpg',
        				'description'   => __( 'Default Header Image', 'twentyseventeen' ),
        			),
        		)
        	);

        }

        function ofts_change_custom_header_image() {

            if (function_exists('twentyseventeen_header_style')) {

            	add_theme_support('custom-header', apply_filters(
                    'twentyseventeen_custom_header_args', array(
                        'default-image'    => plugin_dir_url( __FILE__ ) . 'images/header.jpg',
                        'width'            => 2000,
                        'height'           => 1200,
                        'flex-height'      => true,
                        'video'            => true,
                        'wp-head-callback' => 'twentyseventeen_header_style',
                    )
            	));

            }

        }

        function ofts_fix_custom_logo_crop_bug() {

            if (function_exists('twentyseventeen_setup')) {

                add_theme_support('custom-logo', array(
                    'width' => 250,
                    'height' => 250,
                    'flex-width' => true,
                    'flex-height' => true
                ));

            }

        }

        function ofts_inject_smooth_scrolling() {

?>
<script type="text/javascript">
jQuery(document).ready(function() {
    var scrollnow = function(e) {
        if (e) {
            e.preventDefault();
            var target = this.hash;
            jQuery(e.target).blur();
            jQuery(e.target).parents('.menu-item, .page_item').removeClass('focus');
        } else {
            var target = location.hash;
        }
        target = target.length ? target : jQuery('[name=' + this.hash.slice(1) + ']');
        if (target.length) {
            setTimeout(function() {
                var menuTop = 0, navigationOuterHeight;
			    if (jQuery('body').hasClass('admin-bar')) {
		            menuTop -= 32;
	            }
			    if (!jQuery('body').find('.navigation-top').length) {
                    navigationOuterHeight = 0;
	            } else {
                    navigationOuterHeight = jQuery('body').find('.navigation-top').outerHeight();
	            }
	            setTimeout(function() {
			        jQuery(window).scrollTo(target, {
				        duration: 600,
				        offset: { 
				            top: menuTop - navigationOuterHeight
				        }
			        });
                }, 100);
            }, 100);
        }
    };
    setTimeout(function() {
        if (location.hash) {
            jQuery('html, body').scrollTop(0).show();
            scrollnow();
        }
        if (jQuery('a[href*="/"][href*=\\#]').length) {
            jQuery('a[href*="/"][href*=\\#]').each(function(){
                if (this.pathname.replace(/^\//,'') == location.pathname.replace(/^\//,'') && this.hostname == location.hostname) {
                    if (this.search == location.search) {
                        jQuery(this).attr("href", this.hash);
                    }
                }
            });
            jQuery('a[href^=\\#]:not([href=\\#])').click(scrollnow);
        }
    }, 1);
});
</script>
<?php

        }

        function ofts_social_links_shortcode($atts, $content = null, $tag = '') {

            $atts = array_change_key_case((array)$atts, CASE_LOWER);

			if (has_nav_menu('social')) {

                $social_links = '<nav class="social-navigation' . (isset($atts['class']) ? ' ' . $atts['class'] : '') . '" role="navigation" aria-label="' . esc_attr__('Social Links Menu', 'options-for-twenty-seventeen') . '">';
                $social_links .= wp_nav_menu(array(
                    'theme_location' => 'social',
                    'menu_class'     => 'social-links-menu',
                    'depth'          => 1,
                    'link_before'    => '<span class="screen-reader-text">',
                    'link_after'     => '</span>' . twentyseventeen_get_svg(array('icon' => 'chain')),
                    'echo'          => false
                ));
                $social_links .= '</nav>';
                $social_links .= "
<style>
#masthead .social-navigation, #content .social-navigation {
	width: 100%;
}
.social-navigation a, .widget .social-navigation a {
	box-shadow: none;
}
.social-navigation a:hover, .widget .social-navigation a:hover {
	box-shadow: none;
	color: white;
}
</style>
                ";
                return $social_links;

		    }

        }

        public function ofts_search_images($slug, $name, $args) {

            if (is_search() && 'excerpt' === $name) {

                if ( '' !== get_the_post_thumbnail() && ! is_single() ) {

?>
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'twentyseventeen-featured-image' ); ?>
			</a>
		</div><!-- .post-thumbnail -->
<?php

                }

            }

        }

	}

    if (!class_exists('oftsCommon')) {

        require_once(dirname(__FILE__) . '/includes/class-ofts-common.php');

    }

    if (oftsCommon::is_theme_being_used('twentyseventeen')) {

	    $options_for_twenty_seventeen_object = new options_for_twenty_seventeen_class();

    } else {

        if (is_admin()) {

            $themes = wp_get_themes();

            if (!isset($themes['twentyseventeen'])) {

                add_action('admin_notices', 'ofts_wrong_theme_notice');

            }

        }

    }

    function ofts_wrong_theme_notice() {

?>

<div class="notice notice-error">

<p><strong><?php _e('Options for Twenty Seventeen Plugin Error', 'options-for-twenty-seventeen'); ?></strong><br />
<?php
        printf(
            __('This plugin requires the default Wordpress theme Twenty Seventeen to be active or live previewed in order to function. Your theme "%s" is not compatible.', 'options-for-twenty-seventeen'),
            get_template()
        );
?>

<a href="<?php echo add_query_arg('search', 'twentyseventeen', admin_url('theme-install.php')); ?>" title="<?php _e('Twenty Seventeen', 'options-for-twenty-seventeen'); ?>"><?php
        _e('Please install and activate or live preview the Twenty Seventeen theme (or a child theme thereof)', 'options-for-twenty-seventeen');
?></a>.</p>

</div>

<?php

    }

}

?>

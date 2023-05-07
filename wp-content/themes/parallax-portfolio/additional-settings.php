<?php
add_action( 'customize_register', 'parallax_portfolio_customize_register' );
function parallax_portfolio_customize_register( $wp_customize ) {

    $default = parallax_portfolio_customizer_defaults();

    $wp_customize->add_setting('cww_header_toggle_color', 
			array(
		        'default'           => $default['cww_header_toggle_color'],
		        'sanitize_callback' => 'cww_portfolio_sanitize_color',
		    )
		);

	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'cww_header_toggle_color', 
		array(
	        'label'           	=> esc_html__( 'Menu Toggle', 'parallax-portfolio' ),
	        'section'         	=> 'cww_header_section',
	        
	    ) ) );


    $wp_customize->add_setting('cww_menu_toggle_title', 
            array(
                'sanitize_callback' => 'sanitize_text_field',
            )
        );

    $wp_customize->add_control( 'cww_menu_toggle_title', array(
                'label'         	=> esc_html__( 'Title', 'parallax-portfolio' ),
                'description'       => esc_html__( 'Display text inside full screen menu','parallax-portfolio'),
                'section'       	=> 'cww_header_section',
                'type'      		=> 'text',
                
            ) );

    $wp_customize->add_setting('cww_menu_toggle_desc', 
            array(
                'sanitize_callback' => 'wp_kses_post',
            )
        );

    $wp_customize->add_control( 'cww_menu_toggle_desc', array(
                'label'         	=> esc_html__( 'Description', 'parallax-portfolio' ),
                'description'       => esc_html__( 'Display text inside full screen menu','parallax-portfolio'),
                'section'       	=> 'cww_header_section',
                'type'      		=> 'textarea',
                
            ) );


}
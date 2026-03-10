<?php
if (!defined('ABSPATH')) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class Widget_Map_Custom extends Widget_Base {

    public function get_name() { return 'leaflet_points'; }
    public function get_title() { return __('Mappa con lista', 'custom-widget'); }
    public function get_icon() { return 'eicon-google-maps'; }
    public function get_categories() { return ['custom']; }

    public function get_script_depends() {
        return [ 'custom-map-js' ];
    }

    public function get_style_depends() {
        return [ 'custom-map-css' ];
    }

    protected function register_controls() {

        $this->start_controls_section('section_content', [
            'label' => __('Contenuto', 'custom-widget'),
        ]);

        $this->add_control(
			'widget_title',
			[
				'label' => esc_html__( 'Title', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type your title here', 'textdomain' ),
			]
		);

        $this->add_control('map_height', [
            'label' => __('Altezza mappa (px)', 'custom-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 520,
            'min' => 200,
            'max' => 1200,
        ]);

        $this->add_control('zoom', [
            'label' => __('Zoom iniziale', 'custom-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 12,
            'min' => 1,
            'max' => 20,
        ]);

        $this->add_control('active_zoom', [
            'label' => __('Zoom su click/hover', 'custom-widget'),
            'type' => Controls_Manager::NUMBER,
            'default' => 14,
            'min' => 1,
            'max' => 20,
        ]);

        $this->add_control('interaction', [
            'label' => __('Interazione lista', 'custom-widget'),
            'type' => Controls_Manager::SELECT,
            'default' => 'hover',
            'options' => [
                'hover' => __('Hover + Click (fallback mobile)', 'custom-widget'),
                'click' => __('Solo Click', 'custom-widget'),
            ],
        ]);

        $this->add_control('show_popup', [
            'label' => __('Apri popup al focus', 'custom-widget'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $this->add_control('pulse_marker', [
            'label' => __('Marker “pulse” attivo', 'custom-widget'),
            'type' => Controls_Manager::SWITCHER,
            'return_value' => 'yes',
            'default' => 'yes',
        ]);

        $repeater = new Repeater();

        $repeater->add_control('title', [
            'label' => __('Titolo', 'custom-widget'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Punto', 'custom-widget'),
        ]);

        $repeater->add_control('subtitle', [
            'label' => __('Sottotitolo', 'custom-widget'),
            'type' => Controls_Manager::TEXT,
            'default' => __('Descrizione breve', 'custom-widget'),
        ]);

        $repeater->add_control('badge', [
            'label' => __('Badge (opzionale)', 'custom-widget'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
        ]);

        $repeater->add_control('lat', [
            'label' => __('Latitudine', 'custom-widget'),
            'type' => Controls_Manager::TEXT,
            'default' => '45.92',
        ]);

        $repeater->add_control('lng', [
            'label' => __('Longitudine', 'custom-widget'),
            'type' => Controls_Manager::TEXT,
            'default' => '11.17',
        ]);

        $repeater->add_control('link', [
            'label' => __('Link (opzionale)', 'custom-widget'),
            'type' => Controls_Manager::URL,
            'label_block' => true,
            'options' => ['url', 'is_external', 'nofollow'],
        ]);

        $repeater->add_control('color_border', [
			'label'  => __('Colore bordo', 'custom-widget'),
			'type'   => \Elementor\Controls_Manager::COLOR,
            'global' => [
                'active' => false,
            ],
            'default' => '#000000',
		]);

        $this->add_control('points', [
            'label' => __('Punti', 'custom-widget'),
            'type' => Controls_Manager::REPEATER,
            'fields' => $repeater->get_controls(),
            'title_field' => '{{{ title }}}',
            'default' => [
                ['title' => 'Roma', 'subtitle' => 'La capitale d\'Italia', 'lat' => '41.89', 'lng' => '12.47'],
                ['title' => 'Milano', 'subtitle' => 'La capitale della moda', 'lat' => '45.47', 'lng' => '9.18'],
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section(
            'style_map',
            [
                'label' => esc_html__( 'Mappa', 'textdomain' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'gap_item',
			[
				'label' => esc_html__( 'Distanza colonne', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .custom-leaflet-widget' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'border_radius_map',
			[
				'label' => esc_html__( 'Border radius', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'unit' => 'px',
					'isLinked' => true,
				],
				'selectors' => [
					'{{WRAPPER}} .custom-leaflet-map' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_map',
				'selector' => '{{WRAPPER}} .custom-leaflet-map',
			]
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'style_content_text',
            [
                'label' => esc_html__( 'Contenuto', 'textdomain' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'padding_content',
            [
                'label' => esc_html__( 'Padding', 'textdomain' ),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
                'default' => [
                    'unit' => 'px',
                    'isLinked' => true,
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-leaflet-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]   
		);

        $this->end_controls_section();

        $this->start_controls_section(
            'style_map_text',
            [
                'label' => esc_html__( 'Testi', 'textdomain' ),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
			'distanza_item',
			[
				'label' => esc_html__( 'Distanza', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .custom-leaflet-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
			'title_widget_options',
			[
				'label' => esc_html__( 'Titolo lista', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_lista_typography',
				'selector' => '{{WRAPPER}} .custom-leaflet-title-list',
			]
		);
        $this->add_control(
			'title_lista_color',
			[
				'label' => esc_html__( 'Titolo lista Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-leaflet-title-list' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'title_options',
			[
				'label' => esc_html__( 'Titolo', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .custom-leaflet-title h3',
			]
		);
        $this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-leaflet-title h3' => 'color: {{VALUE}}',
				],
			]
		);

        $this->add_control(
			'subtitle_options',
			[
				'label' => esc_html__( 'Sottotitolo', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

        $this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'subtitle_typography',
				'selector' => '{{WRAPPER}} .custom-leaflet-subtitle p',
			]
		);

        $this->add_control(
			'subtitle_color',
			[
				'label' => esc_html__( 'Subtitle Color', 'textdomain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .custom-leaflet-subtitle p' => 'color: {{VALUE}}',
				],
			]
		);

        $this->end_controls_section();

    }

    protected function render() {
		
        $s = $this->get_settings_for_display();
        if (empty($s['points'])) return;

        $uid = 'leaflet_map_' . $this->get_id();
        $points = [];

        foreach ($s['points'] as $i => $p) {
            // echo '<pre>';
            // var_dump(array_column($s['points'], 'color_border'));
            // echo '</pre>';
            $points[] = [
                'id' => 'p' . $i,
                'title' => $p['title'] ?? '',
                'subtitle' => $p['subtitle'] ?? '',
                'badge' => $p['badge'] ?? '',
                'lat' => (float) ($p['lat'] ?? 0),
                'lng' => (float) ($p['lng'] ?? 0),
                'color_border' => $p['color_border'] ?? '',
                'link' => [
                    'url' => $p['link']['url'] ?? '',
                    'is_external' => !empty($p['link']['is_external']),
                    'nofollow' => !empty($p['link']['nofollow']),
                ],
            ];
        }

        $config = [
            'mapId' => $uid,
            'zoom' => (int) $s['zoom'],
            'activeZoom' => (int) $s['active_zoom'],
            'interaction' => $s['interaction'],
            'showPopup' => ($s['show_popup'] === 'yes'),
            'pulseMarker' => ($s['pulse_marker'] === 'yes'),
            'points' => $points,
        ];
        ?>
        <div class="custom-leaflet-widget" data-leaflet-config="<?php echo esc_attr(wp_json_encode($config)); ?>">
            <div class="custom-leaflet-list">
                <h3 class="custom-leaflet-title-list"><?php echo esc_html($s['widget_title']); ?></h3>
                <?php foreach ($points as $idx => $p):
                    
                    $link = $p['link']['url'];
                    $color = $s['points'][$idx]['color_border'] ?? '';
                    $border_style = $color ? 'border-left:3px solid ' . $color : '';
                    ?>
                    <div class="custom-leaflet-item" data-point-id="<?php echo esc_attr($p['id']); ?>" style="<?php echo esc_attr($border_style); ?>">
                        <div class="custom-leaflet-texts">
                            <div class="custom-leaflet-title">
                                <?php if ($link): ?>
                                    <a href="<?php echo esc_url($link); ?>"
                                        <?php echo $p['link']['is_external'] ? ' target="_blank"' : ''; ?>
                                        <?php echo $p['link']['nofollow'] ? ' rel="nofollow"' : ''; ?>>
                                        <?php echo esc_html($p['title']); ?>
                                    </a>
                                <?php else: ?>
                                    <h3><?php echo esc_html($p['title']); ?></h3>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($p['subtitle'])): ?>
                                <div class="custom-leaflet-subtitle"><p><?php echo esc_html($p['subtitle']); ?></p></div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="custom-leaflet-mapwrap" style="height: <?php echo (int)$s['map_height']; ?>px;">
                <div id="<?php echo esc_attr($uid); ?>" class="custom-leaflet-map"></div>
            </div>
        </div>
        <?php
    }
}

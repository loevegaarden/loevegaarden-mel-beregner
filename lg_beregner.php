<?php
/**
 * Plugin Name:     Løvegården Mængde–Vægt Beregner
 * Plugin URI:      https://www.loevegaarden.dk
 * Description:     Omregn mellem volumen- og vægtenheder for mel, flager, stivelse m.v.
 * Version:         1.0
 * Author:          Løvegården
 * Author URI:      https://www.loevegaarden.dk
 * License:         GPL2
 * Text Domain:     loevegaarden
 */

if (!defined('ABSPATH')) {
    exit; // Forhindre direkte adgang
}

// 1) Registrér CSS og JS
add_action('wp_enqueue_scripts','loevegaarden_beregner_assets');
function loevegaarden_beregner_assets(){
    // Indlejret CSS
    wp_add_inline_style('wp-block-library','
      .loevegaarden-beregner{border:1px solid #234a1f;padding:15px;width:100%;box-sizing:border-box;margin-bottom:30px;font-family:sans-serif;}
      .loevegaarden-beregner .header-bar{display:block;background:#234a1f;color:#fff;padding:8px 15px;font-weight:bold;margin:-15px -15px 15px;text-align:center;white-space:nowrap;}
      .loevegaarden-beregner label{display:block;margin-bottom:5px;color:#234a1f;font-weight:bold;}
      .loevegaarden-beregner input,.loevegaarden-beregner select{width:100%;padding:6px;margin-bottom:10px;border:1px solid #ccc;border-radius:4px;box-sizing:border-box;}
      .loevegaarden-beregner .result{padding:8px;background:#f9f9f9;border-radius:4px;text-align:center;font-weight:bold;}
    ');
    // Frontend-logik
    wp_enqueue_script(
      'loevegaarden-beregner',
      plugin_dir_url(__FILE__).'js/loevegaarden-beregner.js',
      array('jquery'),
      '1.0',
      true
    );
    // Fjern utilsigtede <br> tags
    wp_add_inline_script('loevegaarden-beregner','jQuery(function($){$(".loevegaarden-beregner br").remove();});');
}

// 2) Shortcode
add_shortcode('loevegaarden_beregner','loevegaarden_beregner_shortcode');
function loevegaarden_beregner_shortcode($atts){
    $raw = $atts;
    $atts = shortcode_atts(array(
      'name'  => 'Boghvedemel',
      'value' => 1,
      'from'  => 'dl',
      'to'    => 'g'
    ), $atts,'loevegaarden_beregner');

    $groups = array(
      'Mel'                   => array('Boghvedemel','Fuldkornsrismel','Havremel','Hirsemel','Kikærtemel','Majsmel','Mandelmel','Quinoamel','Rismel','Sorghummel','Teffmel'),
      'Flager'                => array('Boghvedeflager','Fintvalsede havregryn','Grovvalsede havregryn','Hirseflager','Quinoaflager'),
      'Stivelse'              => array('Kartoffelmel','Majsstivelse','Tapiokastivelse'),
      'Gryn, kerner og frø'    => array('Boghvedekerner','Chiafrø','Græskarkerner','Hel hirse','Hørfrø','Knækket boghvede','Majsgryn/polenta','Quinoafrø','Sesamfrø','Solsikkekerner'),
      'Andet bagetilbehør'     => array('FiberHUSK','Loppefrøskaller/psyllium','Guargum','Xanthangum'),
    );

    $single = array_key_exists('from',$raw) || array_key_exists('to',$raw);
    if ($single) {
        // Én-til-én converter
        ob_start();
        echo '<div class="loevegaarden-beregner single" data-name="'.esc_attr($atts['name']).'">'
           . '<div class="header-bar">Omregning for '.esc_html(strtolower($atts['name'])).'</div>'
           . '<input type="number" step="any" class="from-value" value="'.esc_attr($atts['value']).'">'
           . '<select class="from-unit">'
             . '<optgroup label="Volumen">'
               . '<option value="l"'.selected('l',$atts['from'],false).'>l</option>'
               . '<option value="dl"'.selected('dl',$atts['from'],false).'>dl</option>'
               . '<option value="cl"'.selected('cl',$atts['from'],false).'>cl</option>'
               . '<option value="ml"'.selected('ml',$atts['from'],false).'>ml</option>'
               . '<option value="spsk"'.selected('spsk',$atts['from'],false).'>spsk</option>'
               . '<option value="tsk"'.selected('tsk',$atts['from'],false).'>tsk</option>'
             . '</optgroup>'
             . '<optgroup label="Vægt">'
               . '<option value="kg"'.selected('kg',$atts['from'],false).'>kg</option>'
               . '<option value="g"'.selected('g',$atts['from'],false).'>g</option>'
             . '</optgroup>'
           . '</select>'
           . '<select class="to-unit">'
             . '<optgroup label="Volumen">'
               . '<option value="l"'.selected('l',$atts['to'],false).'>l</option>'
               . '<option value="dl"'.selected('dl',$atts['to'],false).'>dl</option>'
               . '<option value="cl"'.selected('cl',$atts['to'],false).'>cl</option>'
               . '<option value="ml"'.selected('ml',$atts['to'],false).'>ml</option>'
               . '<option value="spsk"'.selected('spsk',$atts['to'],false).'>spsk</option>'
               . '<option value="tsk"'.selected('tsk',$atts['to'],false).'>tsk</option>'
             . '</optgroup>'
             . '<optgroup label="Vægt">'
               . '<option value="kg"'.selected('kg',$atts['to'],false).'>kg</option>'
               . '<option value="g"'.selected('g',$atts['to'],false).'>g</option>'
             . '</optgroup>'
           . '</select>'
           . '<div class="result">'.esc_html(str_replace('.',',',number_format((float)$atts['value'],0,',',''))).' '.esc_html($atts['to']).'</div>'
           . '</div>';
        return ob_get_clean();
    }

    // Generel converter
    ob_start();
    echo '<div class="loevegaarden-beregner">'
       . '<div class="header-bar">Hurtig omregning for mel og andet bagetilbehør</div>'
       . '<select class="ingredient">';
    foreach ($groups as $label => $varer) {
        echo '<optgroup label="'.esc_attr($label).'">';
        foreach ($varer as $vare) {
            echo '<option value="'.esc_attr($vare).'"'.selected($vare,$atts['name'],false).'>'.esc_html($vare).'</option>';
        }
        echo '</optgroup>';
    }
    echo    '</select>'
          . '<input type="number" step="any" class="from-value" value="'.esc_attr($atts['value']).'">'
          . '<select class="from-unit">'
            . '<optgroup label="Volumen">'
              . '<option value="l"'.selected('l',$atts['from'],false).'>l</option>'
              . '<option value="dl"'.selected('dl',$atts['from'],false).'>dl</option>'
              . '<option value="cl"'.selected('cl',$atts['from'],false).'>cl</option>'
              . '<option value="ml"'.selected('ml',$atts['from'],false).'>ml</option>'
              . '<option value="spsk"'.selected('spsk',$atts['from'],false).'>spsk</option>'
              . '<option value="tsk"'.selected('tsk',$atts['from'],false).'>tsk</option>'
            . '</optgroup>'
            . '<optgroup label="Vægt">'
              . '<option value="kg"'.selected('kg',$atts['from'],false).'>kg</option>'
              . '<option value="g"'.selected('g',$atts['from'],false).'>g</option>'
            . '</optgroup>'
          . '</select>'
          . '<select class="to-unit">'
            . '<optgroup label="Volumen">'
              . '<option value="l"'.selected('l',$atts['to'],false).'>l</option>'
              . '<option value="dl"'.selected('dl',$atts['to'],false).'>dl</option>'
              . '<option value="cl"'.selected('cl',$atts['to'],false).'>cl</option>'
              . '<option value="ml"'.selected('ml',$atts['to'],false).'>ml</option>'
              . '<option value="spsk"'.selected('spsk',$atts['to'],false).'>spsk</option>'
              . '<option value="tsk"'.selected('tsk',$atts['to'],false).'>tsk</option>'
            . '</optgroup>'
            . '<optgroup label="Vægt">'
              . '<option value="kg"'.selected('kg',$atts['to'],false).'>kg</option>'
              . '<option value="g"'.selected('g',$atts['to'],false).'>g</option>'
            . '</optgroup>'
          . '</select>'
          . '<div class="result">'.esc_html(str_replace('.',',',number_format((float)$atts['value'],0,',',''))).' '.esc_html($atts['to']).'</div>'
       . '</div>';
    return ob_get_clean();
}

// 3) Admin-menu for dokumentation
add_action('admin_menu','loevegaarden_admin_menu');
function loevegaarden_admin_menu(){
  add_menu_page('Løvegården','Løvegården','manage_options','loevegaarden','loevegaarden_admin_side','dashicons-editor-spreadsheet',60);
  add_submenu_page('loevegaarden','Beregnere','Beregnere','manage_options','loevegaarden-beregner','loevegaarden_beregner_admin_side');
}
function loevegaarden_admin_side(){
  echo '<div class="wrap"><h1>Løvegården</h1><p>Vælg “Beregnere” i menuen.</p></div>';
}
function loevegaarden_beregner_admin_side(){
  echo '<div class="wrap"><h1>Beregnere</h1>'
     . '<h2>Overordnet</h2><pre>[loevegaarden_beregner]</pre><p>Starter på Boghvedemel, dl → g, værdi=1. Brugeren kan ændre alt i frontenden.</p>'
     . '<h2>Specifik</h2><pre>[loevegaarden_beregner name="Boghvedemel" from="l" to="g"]</pre><p>Viser kun enheds-vælgere for den ønskede ingrediens.</p>'
     . '</div>';
}

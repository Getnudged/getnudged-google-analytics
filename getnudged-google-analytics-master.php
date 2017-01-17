<?php
/**
 * Plugin Name: Getnudged Google Analytics
 * Plugin URI:  https://getnudged.de
 * Description: Google Analytics Datenschutzkonform einbinden.
 * Version:     1.1.3
 * Author:      Getnudged
 * Author URI:  https://getnudged.de
 * License:     MIT License
 * License URI: http://opensource.org/licenses/MIT
 */

/**
 * Plugin Update Checker von YahnisElsts
 * https://github.com/YahnisElsts
 */
require 'plugin-update-checker/plugin-update-checker.php';
$className = PucFactory::getLatestClassVersion('PucGitHubChecker');
$myUpdateChecker = new $className(
    'https://github.com/Getnudged/getnudged-google-analytics/',
    __FILE__,
    'master'
);

/**
 * Customizer registrieren
 */
add_action('customize_register', 'gnga_register_customizer' );
function gnga_register_customizer($wp_customize) {
  
  $wp_customize->add_section('gnga-options', array(
  'title' => __('Google Analytics'),
  'priority' => 30,
  ));	
      
  # Feld hinzufügen
  $wp_customize->add_setting('gaproperty', array(
    'default' => '',
    'transport' => 'refresh',
  ));	
      
  # Feld definieren
  $wp_customize->add_control(new \WP_Customize_Control(
    $wp_customize,
    'gaproperty',
    array(
    'label' => __('Google Analytics Property ID'),
    'section' => 'gnga-options',
    'settings' => 'gaproperty',
    'capability' => 'edit_theme_options',
    'description' => __('Hier tragen Sie Ihre Google Analytics Property ID ein. (UA-XXXXXXXX-X)'),
    'type' => 'text'
    )
  ));	
      
  # Feld hinzufügen
  $wp_customize->add_setting('gaoptout', array(
    'default' => '',
    'transport' => 'refresh',
  ));	
      
  # Feld definieren
  $wp_customize->add_control(new \WP_Customize_Control(
    $wp_customize,
    'gaoptout',
    array(
    'label' => __('Opt-Out Linktext'),
    'section' => 'gnga-options',
    'settings' => 'gaoptout',
    'capability' => 'edit_theme_options',
    'description' => __('Hier tragen Sie einen beliebigen Text für den Opt Out Link ein. (Standard: Google Analytics deaktivieren)'),
    'type' => 'text'
    )
  ));	
  
}	
	
/**
 * Customizer Live Preview Script registrieren
 */
add_action('customize_preview_init', 'gnga_customizer' );
function gnga_customizer() {
  
  $version = date('ymd-Gis', filemtime( plugin_dir_path( __FILE__ ) . 'customizer.js' ));
  
  wp_enqueue_script( 
    'gnga-customizer', 
    plugins_url( 'customizer.js', __FILE__ ), 
    array( 
      'jquery', 
      'customize-preview' 
    ), 
    $version 
  );
  
}

/**
 * Variable in den Headbereich setzen
 */
add_action('wp_head', 'gnga_head');
function gnga_head() {
  
  if( get_theme_mod("gaproperty") ) {
    ?>

<!-- Getnudged Google Analytics -->
<script>
var gaProperty = '<?php echo get_theme_mod("gaproperty"); ?>';
</script> 
<!-- END Getnudged Google Analytics -->
   
<?php
  }    
  
}
/**
 * Script in in den Footer setzen
 */
add_action('wp_footer', 'gnga_footer');
function gnga_footer() {
  
  if( get_theme_mod("gaproperty") ) {
    ?>

<!-- Getnudged Google Analytics -->
<script>
// GOOGLE ANALYTICS OPT-OUT
var disableStr = 'ga-disable-' + gaProperty;
if (document.cookie.indexOf(disableStr + '=true') > -1) {
  window[disableStr] = true;
}
function gaOptout() {
  document.cookie = disableStr + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
  window[disableStr] = true;
  alert(\'Google Analytics wurde deaktiviert\');
}
// GOOGLE ANALYTICS
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
ga('create', gaProperty, 'auto');
ga('send', 'pageview');
ga('set', 'anonymizeIp', true);
</script> 
<!-- END Getnudged Google Analytics -->
   
<?php
  }    
  
}

/**
 * Opt-Out Shortcode [optout]
 */
function gnga_optout( $atts ){
    
  if( get_theme_mod("gaoptout") ) {
    $text = get_theme_mod("gaoptout");
  } else {
    $text = __('Google Analytics deaktivieren');
  }
  
  return '<p><a onclick="gaOptout();" href="javascript:gaOptout()">' . $text . '</a></p>';  
}
add_shortcode( 'optout', 'gnga_optout' );

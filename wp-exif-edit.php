<?php
/*
Plugin Name: WP Exif Edit
Plugin URI: https://kevin-benabdelhak.fr/plugins/wp-exif-edit/
Description: WP Exif Edit permet de visualiser, modifier et enregistrer les métadonnées EXIF de vos images directement depuis la médiathèque WordPress, sans quitter l’interface d’administration.
Version: 1.0
Author: Kevin BENABDELHAK
Author URI: https://kevin-benabdelhak.fr
Contributors: kevinbenabdelhak
*/

if (!defined('ABSPATH')) exit;


if ( !class_exists( 'YahnisElsts\\PluginUpdateChecker\\v5\\PucFactory' ) ) {
    require_once __DIR__ . '/plugin-update-checker/plugin-update-checker.php';
}
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$monUpdateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/kevinbenabdelhak/WP-Exif-Edit/', 
    __FILE__,
    'wp-exif-edit' 
);
$monUpdateChecker->setBranch('main');

// ========== callback principal =============

add_filter('attachment_fields_to_edit', function($form_fields, $post) {
    $file = get_attached_file($post->ID);
    if (!preg_match('/\.jpe?g$/i', $file)) {
        return $form_fields;
    }
    $url = wp_get_attachment_url($post->ID);

    $exif = [
        'ImageDescription'      => '',
        'DateTimeOriginal'      => '',

        'Make'                  => '',
        'Model'                 => '',
        'FocalLength'           => '',
        'ExposureTime'          => '',
        'ISOSpeedRatings'       => '',
        'ExposureBiasValue'     => '',
        'FocalLengthIn35mmFilm' => '',
        'MaxApertureValue'      => '',
        'SubjectDistance'       => '',
        'Flash'                 => '',

        'Contrast'              => '',
        'BrightnessValue'       => '',
        'LightSource'           => '',
        'ExposureProgram'       => '',
        'Saturation'            => '',
        'Sharpness'             => '',
        'WhiteBalance'          => '',
        'PhotometricInterpretation' => '',
        'DigitalZoomRatio'      => '',
        'ExifVersion'           => '',
        'Artist'                => '',
        'Copyright'             => '',
    ];

    if (function_exists('exif_read_data')) {
        $data = @exif_read_data($file, 0, true);
        if ($data) {
            if (isset($data['IFD0']['ImageDescription'])) $exif['ImageDescription'] = $data['IFD0']['ImageDescription'];
            if (isset($data['EXIF']['DateTimeOriginal'])) $exif['DateTimeOriginal'] = $data['EXIF']['DateTimeOriginal'];

            if (isset($data['IFD0']['Make']))         $exif['Make'] = $data['IFD0']['Make'];
            if (isset($data['IFD0']['Model']))        $exif['Model'] = $data['IFD0']['Model'];

            if (isset($data['EXIF']['FocalLength'])) {
                $foc = $data['EXIF']['FocalLength'];
                if (is_array($foc) && count($foc) === 2 && $foc[1] != 0) {
                    $exif['FocalLength'] = ($foc[0]/$foc[1]) . ' mm';
                } else {
                    $exif['FocalLength'] = $foc;
                }
            }

            if (isset($data['EXIF']['ExposureTime'])) $exif['ExposureTime'] = $data['EXIF']['ExposureTime'];
            if (isset($data['EXIF']['ISOSpeedRatings'])) $exif['ISOSpeedRatings'] = $data['EXIF']['ISOSpeedRatings'];
            if (isset($data['EXIF']['ExposureBiasValue'])) $exif['ExposureBiasValue'] = $data['EXIF']['ExposureBiasValue'];
            if (isset($data['EXIF']['FocalLengthIn35mmFilm'])) $exif['FocalLengthIn35mmFilm'] = $data['EXIF']['FocalLengthIn35mmFilm'];
            if (isset($data['EXIF']['MaxApertureValue'])) $exif['MaxApertureValue'] = $data['EXIF']['MaxApertureValue'];
            if (isset($data['EXIF']['SubjectDistance'])) $exif['SubjectDistance'] = $data['EXIF']['SubjectDistance'];
            if (isset($data['EXIF']['Flash']))        $exif['Flash'] = $data['EXIF']['Flash'];

            if (isset($data['EXIF']['Contrast']))     $exif['Contrast'] = $data['EXIF']['Contrast'];
            if (isset($data['EXIF']['BrightnessValue'])) $exif['BrightnessValue'] = $data['EXIF']['BrightnessValue'];
            if (isset($data['EXIF']['LightSource']))  $exif['LightSource'] = $data['EXIF']['LightSource'];
            if (isset($data['EXIF']['ExposureProgram']))  $exif['ExposureProgram'] = $data['EXIF']['ExposureProgram'];
            if (isset($data['EXIF']['Saturation']))   $exif['Saturation'] = $data['EXIF']['Saturation'];
            if (isset($data['EXIF']['Sharpness']))    $exif['Sharpness'] = $data['EXIF']['Sharpness'];
            if (isset($data['EXIF']['WhiteBalance'])) $exif['WhiteBalance'] = $data['EXIF']['WhiteBalance'];
            if (isset($data['IFD0']['PhotometricInterpretation'])) $exif['PhotometricInterpretation'] = $data['IFD0']['PhotometricInterpretation'];
            if (isset($data['EXIF']['DigitalZoomRatio'])) $exif['DigitalZoomRatio'] = $data['EXIF']['DigitalZoomRatio'];
            if (isset($data['EXIF']['ExifVersion']))  $exif['ExifVersion'] = $data['EXIF']['ExifVersion'];

            if (isset($data['IFD0']['Artist'])) $exif['Artist'] = $data['IFD0']['Artist'];
            if (isset($data['IFD0']['Copyright'])) $exif['Copyright'] = $data['IFD0']['Copyright'];
        }
    }

    // Inclusion des champs & du JS
    require_once plugin_dir_path(__FILE__) . 'options.php';
    require_once plugin_dir_path(__FILE__) . 'scripts.php';

    $htmlFields = wp_exif_edit_render_form_fields($post, $exif, $url);
    $htmlJS = wp_exif_edit_render_js($post, $url);

    $form_fields['custom_exif_replace'] = [
        'label' => 'Édition EXIF avancée',
        'input' => 'html',
        'html'  => $htmlFields . $htmlJS
    ];
    return $form_fields;
}, 10, 2);



// ====================== sauvegarde ajax  =============================

add_action('wp_ajax_custom_exif_replace', function() {
    check_ajax_referer('custom_exif_replace');
    $post_id = intval($_POST['post_id']);
    if (!current_user_can('edit_post', $post_id)){
        wp_send_json_error(['message'=>'Droits insuffisants']);
    }
    if (empty($_FILES['image']) || $_FILES['image']['error']) {
        wp_send_json_error(['message'=>'Aucune image reçue']);
    }
    $file = get_attached_file($post_id);
    if (!preg_match('/\.jpe?g$/i', $file)) {
        wp_send_json_error(['message'=>'Format non supporté']);
    }
    if (!move_uploaded_file($_FILES['image']['tmp_name'], $file)) {
        wp_send_json_error(['message'=>'Erreur lors de la sauvegarde']);
    }
    clean_attachment_cache($post_id);
    wp_send_json_success(['message'=>'Image bien remplacée !']);
});
<?php
if (!defined('ABSPATH')) exit;


function wp_exif_edit_render_form_fields($post, $exif, $url) {
    ob_start();
    ?>
    <p><label><strong>Titre (ImageDescription) :</strong>
        <input type="text" id="custom-exif-imagedescription-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['ImageDescription']); ?>" placeholder="Description de l'image">
    </label></p>
    <p><label><strong>Auteurs (Artist) :</strong>
        <input type="text" id="custom-exif-artist-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Artist']); ?>" placeholder="Nom de l'auteur">
    </label></p>
    <p><label><strong>Copyright :</strong>
        <input type="text" id="custom-exif-copyright-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Copyright']); ?>" placeholder="Copyright">
    </label></p>
    <p><label><strong>Date prise de vue (DateTimeOriginal) :</strong>
        <input type="text" id="custom-exif-datetime-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['DateTimeOriginal']); ?>" placeholder="2023:06:20 18:30:00">
    </label></p>
    <p><label><strong>Marque appareil photo (Make) :</strong>
        <input type="text" id="custom-exif-make-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Make']); ?>" placeholder="Nikon, Canon">
    </label></p>
    <p><label><strong>Modèle appareil photo (Model) :</strong>
        <input type="text" id="custom-exif-model-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Model']); ?>" placeholder="D3500, EOS R5...">
    </label></p>
    <p><label><strong>Focale 35mm (FocalLengthIn35mmFilm) :</strong>
        <input type="number" id="custom-exif-focal35-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['FocalLengthIn35mmFilm']); ?>" placeholder="35">
    </label></p>
    <p><label><strong>Temps d'exposition (ExposureTime) :</strong>
        <input type="text" id="custom-exif-exposuretime-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['ExposureTime']); ?>" placeholder="1/200">
    </label></p>
    <p><label><strong>Sensibilité ISO (ISOSpeedRatings) :</strong>
        <input type="number" id="custom-exif-iso-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['ISOSpeedRatings']); ?>" placeholder="100, 400, 3200">
    </label></p>
    <p><label><strong>Compensation (ExposureBiasValue) :</strong>
        <input type="text" id="custom-exif-exposurebias-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['ExposureBiasValue']); ?>" placeholder="0, -0.3, 1/3, -0.7">
    </label></p>
    <p><label><strong>Ouverture max (MaxApertureValue) :</strong>
        <input type="text" id="custom-exif-maxaperture-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['MaxApertureValue']); ?>" placeholder="2.8">
    </label></p>
    <p><label><strong>Distance sujet (SubjectDistance) :</strong>
        <input type="text" id="custom-exif-subjectdistance-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['SubjectDistance']); ?>" placeholder="4.5">
    </label></p>
    <p><label><strong>Flash (Flash) :</strong>
        <input type="number" id="custom-exif-flash-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Flash']); ?>" list="exif-flash-<?php echo $post->ID; ?>" placeholder="0 (aucun), 1 (flash)">
        <datalist id="exif-flash-<?php echo $post->ID; ?>">
            <option value="0" label="Aucun flash"></option>
            <option value="1" label="Flash"></option>
            <option value="9" label="Flash, mode auto"></option>
            <option value="16" label="Pas de flash, mode auto"></option>
            <option value="32" label="Flash ne s’est pas déclenché"></option>
            <option value="65" label="Flash automatique, déclenché"></option>
        </datalist>
    </label></p>
    <p><label><strong>Contraste (Contrast) :</strong>
        <input type="number" id="custom-exif-contrast-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Contrast']); ?>" list="exif-contrast-<?php echo $post->ID; ?>" placeholder="0 (Normal)">
        <datalist id="exif-contrast-<?php echo $post->ID; ?>">
            <option value="0" label="Normal">
            <option value="1" label="Soft">
            <option value="2" label="Hard">
        </datalist>
    </label></p>
    <p><label><strong>Luminosité (BrightnessValue) :</strong>
        <input type="text" id="custom-exif-brightness-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['BrightnessValue']); ?>" placeholder="ex : 6.3">
    </label></p>
    <p><label><strong>Source de lumière (LightSource) :</strong>
        <input type="number" id="custom-exif-lightsource-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['LightSource']); ?>" list="exif-lightsource-<?php echo $post->ID; ?>" placeholder="0 (Inconnue)">
        <datalist id="exif-lightsource-<?php echo $post->ID; ?>">
            <option value="0" label="Inconnue">
            <option value="1" label="Lumière du jour">
            <option value="2" label="Fluorescent">
            <option value="3" label="Tungstène">
            <option value="4" label="Flash">
            <option value="9" label="Beau temps">
            <option value="10" label="Couvert">
            <option value="17" label="Lumière standard D65">
        </datalist>
    </label></p>
    <p><label><strong>Programme d'exposition (ExposureProgram) :</strong>
        <input type="number" id="custom-exif-exposureprogram-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['ExposureProgram']); ?>" list="exif-exposureprogram-<?php echo $post->ID; ?>" placeholder="0 (Non défini)">
        <datalist id="exif-exposureprogram-<?php echo $post->ID; ?>">
            <option value="0" label="Non défini">
            <option value="1" label="Manuel">
            <option value="2" label="Normale">
            <option value="3" label="Priorité ouverture">
            <option value="4" label="Priorité vitesse">
            <option value="5" label="Créatif">
            <option value="6" label="Action">
            <option value="7" label="Portrait">
            <option value="8" label="Paysage">
        </datalist>
    </label></p>
    <p><label><strong>Saturation (Saturation) :</strong>
        <input type="number" id="custom-exif-saturation-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Saturation']); ?>" list="exif-saturation-<?php echo $post->ID; ?>" placeholder="0 (Normale)">
        <datalist id="exif-saturation-<?php echo $post->ID; ?>">
            <option value="0" label="Normale">
            <option value="1" label="Low">
            <option value="2" label="High">
        </datalist>
    </label></p>
    <p><label><strong>Netteté (Sharpness) :</strong>
        <input type="number" id="custom-exif-sharpness-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['Sharpness']); ?>" list="exif-sharpness-<?php echo $post->ID; ?>" placeholder="0 (Normale)">
        <datalist id="exif-sharpness-<?php echo $post->ID; ?>">
            <option value="0" label="Normale">
            <option value="1" label="Soft">
            <option value="2" label="Hard">
        </datalist>
    </label></p>
    <p><label><strong>Balance des blancs (WhiteBalance) :</strong>
        <input type="number" id="custom-exif-whitebalance-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['WhiteBalance']); ?>" list="exif-whitebalance-<?php echo $post->ID; ?>" placeholder="0 (Auto)">
        <datalist id="exif-whitebalance-<?php echo $post->ID; ?>">
            <option value="0" label="Auto">
            <option value="1" label="Manuelle">
        </datalist>
    </label></p>
    <p><label><strong>Interprétation photométrique (PhotometricInterpretation) :</strong>
        <input type="number" id="custom-exif-photometric-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['PhotometricInterpretation']); ?>" list="exif-photometric-<?php echo $post->ID; ?>" placeholder="2 (RGB)">
        <datalist id="exif-photometric-<?php echo $post->ID; ?>">
            <option value="2" label="RGB">
            <option value="6" label="YCbCr">
        </datalist>
    </label></p>
    <p><label><strong>Zoom numérique (DigitalZoomRatio) :</strong>
        <input type="text" id="custom-exif-digitalzoom-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['DigitalZoomRatio']); ?>" placeholder="1.0, 2.0...">
    </label></p>
    <p><label><strong>Version EXIF (ExifVersion) :</strong>
        <input type="text" id="custom-exif-exifversion-<?php echo $post->ID; ?>" value="<?php echo esc_attr($exif['ExifVersion']); ?>" placeholder="0230">
    </label></p>

    <button type="button" class="button"
        id="custom-exif-save-<?php echo $post->ID; ?>"
        data-img="<?php echo esc_url($url); ?>"
        data-id="<?php echo $post->ID; ?>">
        Sauvegarder EXIF (remplace image)
    </button>
    <div id="custom-exif-result-<?php echo $post->ID; ?>"></div>
    <?php
    return ob_get_clean();
}
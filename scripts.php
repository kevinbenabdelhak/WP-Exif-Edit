<?php
if (!defined('ABSPATH')) exit;


function wp_exif_edit_render_js($post, $url) {
    ob_start(); ?>
    <script src="https://cdn.jsdelivr.net/npm/piexifjs"></script>
    <script>
    function toFraction(value) {
        if (parseInt(value) === value) return [value, 1];
        var tolerance = 1.0E-6, h1=1, h2=0, k1=0, k2=1, b = Math.abs(value), sign = value < 0 ? -1 : 1;
        do {
            var a = Math.floor(b);
            var aux = h1; h1 = a*h1 + h2; h2 = aux;
            aux = k1; k1 = a*k1 + k2; k2 = aux;
            b = 1/(b-a);
        } while (Math.abs(Math.abs(value)-h1/k1) > Math.abs(value)*tolerance && k1 < 10000);
        return [h1*sign, k1];
    }
    function parseFractionOrNumber(v) {
        if (!v) return null;
        v = v.trim().replace(',', '.');
        if (v.indexOf('/') > -1) {
            var parts = v.split('/');
            var num = parseFloat(parts[0]);
            var den = parseInt(parts[1]);
            if (isNaN(num) || isNaN(den) || den === 0) return null;
            return [num, den];
        }
        var f = parseFloat(v);
        if (isNaN(f)) return null;
        return [f, 1];
    }
    (function($){
        $('#custom-exif-save-<?php echo $post->ID; ?>').on('click', function(e){
            var id = '<?php echo $post->ID; ?>';
            var fields = {
                imagedescription: $('#custom-exif-imagedescription-'+id).val(),
                datetime: $('#custom-exif-datetime-'+id).val(),

                make: $('#custom-exif-make-'+id).val(),
                model: $('#custom-exif-model-'+id).val(),
                focallength: $('#custom-exif-focallength-'+id).val(),
                exposuretime: $('#custom-exif-exposuretime-'+id).val(),
                iso: $('#custom-exif-iso-'+id).val(),
                exposurebias: $('#custom-exif-exposurebias-'+id).val(),
                focal35: $('#custom-exif-focal35-'+id).val(),
                maxaperture: $('#custom-exif-maxaperture-'+id).val(),
                subjectdist: $('#custom-exif-subjectdistance-'+id).val(),
                flash: $('#custom-exif-flash-'+id).val(),
                contrast: $('#custom-exif-contrast-'+id).val(),
                brightness: $('#custom-exif-brightness-'+id).val(),
                lightsource: $('#custom-exif-lightsource-'+id).val(),
                exposureprogram: $('#custom-exif-exposureprogram-'+id).val(),
                saturation: $('#custom-exif-saturation-'+id).val(),
                sharpness: $('#custom-exif-sharpness-'+id).val(),
                whitebalance: $('#custom-exif-whitebalance-'+id).val(),
                photometric: $('#custom-exif-photometric-'+id).val(),
                digitalzoom: $('#custom-exif-digitalzoom-'+id).val(),
                exifversion: $('#custom-exif-exifversion-'+id).val(),
                artist: $('#custom-exif-artist-'+id).val(),
                copyright: $('#custom-exif-copyright-'+id).val(),
            };
            var imgUrl = $(this).data('img');
            var postId = $(this).data('id');
            var $result = $('#custom-exif-result-<?php echo $post->ID; ?>');
            $result.text('Traitement en cours...');

            fetch(imgUrl)
                .then(r => r.blob())
                .then(blob => new Promise(resolve => {
                    var reader = new FileReader();
                    reader.onload = function() { resolve(reader.result); };
                    reader.readAsDataURL(blob);
                }))
                .then(function(dataURL){
                    try {
                        var exifObj = piexif.load(dataURL);
                        exifObj["0th"] = exifObj["0th"] || {};
                        exifObj["Exif"] = exifObj["Exif"] || {};
                        exifObj["1st"] = exifObj["1st"] || {};
                        exifObj["GPS"] = exifObj["GPS"] || {};

                        if(fields.imagedescription) 
                            exifObj["0th"][piexif.ImageIFD.ImageDescription] = fields.imagedescription;

                        if (fields.datetime && fields.datetime.match(/^\d{4}:\d{2}:\d{2} \d{2}:\d{2}:\d{2}$/))
                            exifObj["Exif"][piexif.ExifIFD.DateTimeOriginal] = fields.datetime;
                        else
                            delete exifObj["Exif"][piexif.ExifIFD.DateTimeOriginal];

                        exifObj["0th"][piexif.ImageIFD.Make] = fields.make || "";
                        exifObj["0th"][piexif.ImageIFD.Model] = fields.model || "";

                        var f = parseFractionOrNumber(fields.focallength);
                        if (f) exifObj["Exif"][piexif.ExifIFD.FocalLength] = f;

                        var e = parseFractionOrNumber(fields.exposuretime);
                        if (e) exifObj["Exif"][piexif.ExifIFD.ExposureTime] = e;

                        var isoval = parseInt(fields.iso, 10);
                        if (!isNaN(isoval) && isoval > 0) exifObj["Exif"][piexif.ExifIFD.ISOSpeedRatings] = isoval;

                        var expBias = fields.exposurebias.trim().replace(',', '.');
                        if(expBias) {
                            if (expBias.indexOf('/') > -1) {
                                var parts = expBias.split('/');
                                var num = parseFloat(parts[0]);
                                var den = parseInt(parts[1]);
                                if (!isNaN(num) && !isNaN(den) && den !== 0) {
                                    exifObj["Exif"][piexif.ExifIFD.ExposureBiasValue] = [num, den];
                                }
                            } else {
                                var ebValue = parseFloat(expBias);
                                if(!isNaN(ebValue)) {
                                    var frac = toFraction(ebValue);
                                    exifObj["Exif"][piexif.ExifIFD.ExposureBiasValue] = frac;
                                }
                            }
                        } else {
                            delete exifObj["Exif"][piexif.ExifIFD.ExposureBiasValue];
                        }

                        var focal35val = parseInt(fields.focal35, 10);
                        if (!isNaN(focal35val) && focal35val > 0) exifObj["Exif"][piexif.ExifIFD.FocalLengthIn35mmFilm] = focal35val;
                        else delete exifObj["Exif"][piexif.ExifIFD.FocalLengthIn35mmFilm];

                        var maxAp = parseFractionOrNumber(fields.maxaperture);
                        if (maxAp) exifObj["Exif"][piexif.ExifIFD.MaxApertureValue] = maxAp;
                        else delete exifObj["Exif"][piexif.ExifIFD.MaxApertureValue];

                        var subjDist = parseFractionOrNumber(fields.subjectdist);
                        if (subjDist) exifObj["Exif"][piexif.ExifIFD.SubjectDistance] = subjDist;
                        else delete exifObj["Exif"][piexif.ExifIFD.SubjectDistance];

                        var flashInt = parseInt(fields.flash, 10);
                        if (!isNaN(flashInt)) exifObj["Exif"][piexif.ExifIFD.Flash] = flashInt;
                        else delete exifObj["Exif"][piexif.ExifIFD.Flash];

                        if(fields.contrast) exifObj["Exif"][piexif.ExifIFD.Contrast] = parseInt(fields.contrast, 10);
                        else delete exifObj["Exif"][piexif.ExifIFD.Contrast];

                        var brightnessV = parseFloat(fields.brightness.replace(',', '.'));
                        if(!isNaN(brightnessV)) exifObj["Exif"][piexif.ExifIFD.BrightnessValue] = toFraction(brightnessV);
                        else delete exifObj["Exif"][piexif.ExifIFD.BrightnessValue];

                        var lightsource = parseInt(fields.lightsource, 10);
                        if(!isNaN(lightsource)) exifObj["Exif"][piexif.ExifIFD.LightSource] = lightsource;
                        else delete exifObj["Exif"][piexif.ExifIFD.LightSource];

                        var exposureprogram = parseInt(fields.exposureprogram, 10);
                        if(!isNaN(exposureprogram)) exifObj["Exif"][piexif.ExifIFD.ExposureProgram] = exposureprogram;
                        else delete exifObj["Exif"][piexif.ExifIFD.ExposureProgram];

                        var saturation = parseInt(fields.saturation, 10);
                        if(!isNaN(saturation)) exifObj["Exif"][piexif.ExifIFD.Saturation] = saturation;
                        else delete exifObj["Exif"][piexif.ExifIFD.Saturation];

                        var sharpness = parseInt(fields.sharpness, 10);
                        if(!isNaN(sharpness)) exifObj["Exif"][piexif.ExifIFD.Sharpness] = sharpness;
                        else delete exifObj["Exif"][piexif.ExifIFD.Sharpness];

                        var whitebalance = parseInt(fields.whitebalance, 10);
                        if(!isNaN(whitebalance)) exifObj["Exif"][piexif.ExifIFD.WhiteBalance] = whitebalance;
                        else delete exifObj["Exif"][piexif.ExifIFD.WhiteBalance];

                        var photometric = parseInt(fields.photometric, 10);
                        if(!isNaN(photometric)) exifObj["0th"][piexif.ImageIFD.PhotometricInterpretation] = photometric;
                        else delete exifObj["0th"][piexif.ImageIFD.PhotometricInterpretation];

                        var digitalzoom = parseFloat(fields.digitalzoom.replace(',', '.'));
                        if(!isNaN(digitalzoom)) exifObj["Exif"][piexif.ExifIFD.DigitalZoomRatio] = [digitalzoom,1];
                        else delete exifObj["Exif"][piexif.ExifIFD.DigitalZoomRatio];

                        if(fields.exifversion) exifObj["Exif"][piexif.ExifIFD.ExifVersion] = fields.exifversion;
                        else delete exifObj["Exif"][piexif.ExifIFD.ExifVersion];

                        if(fields.artist) exifObj["0th"][piexif.ImageIFD.Artist] = fields.artist;
                        else delete exifObj["0th"][piexif.ImageIFD.Artist];

                        if(fields.copyright) exifObj["0th"][piexif.ImageIFD.Copyright] = fields.copyright;
                        else delete exifObj["0th"][piexif.ImageIFD.Copyright];

                        var exifBytes = piexif.dump(exifObj);
                        var newDataUrl = piexif.insert(exifBytes, dataURL);

                        var byteString = atob(newDataUrl.split(',')[1]);
                        var ab = new ArrayBuffer(byteString.length);
                        var ia = new Uint8Array(ab);
                        for (var i = 0; i < byteString.length; i++) {
                            ia[i] = byteString.charCodeAt(i);
                        }
                        var newblob = new Blob([ab], {type: 'image/jpeg'});
                        var fd = new FormData();
                        fd.append('action', 'custom_exif_replace');
                        fd.append('post_id', postId);
                        fd.append('image', newblob, 'image.jpg');
                        fd.append('_wpnonce', '<?php echo wp_create_nonce('custom_exif_replace'); ?>');
                        $result.text('Upload en cours...');
                        fetch(ajaxurl, {
                            method: 'POST',
                            body: fd
                        }).then(r=>r.json()).then(function(res){
                            if(res.success){
                                $result.text("Image remplacée et EXIF modifié !");
                            }else{
                                $result.text("Erreur: " + (res.data && res.data.message ? res.data.message : 'Inconnue'));
                            }
                        }).catch(function(e){
                            $result.text("Erreur AJAX: " + e);
                        });
                    } catch(e) {
                        $result.text("Erreur de conversion EXIF: " + e);
                    }
                });
        });
    })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
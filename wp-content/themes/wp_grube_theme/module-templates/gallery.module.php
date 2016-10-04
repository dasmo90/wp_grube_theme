<?php

add_action( 'wp_enqueue_scripts', 'scooch_library' );

?>

<?php

$ids = $parameters['ids'];
if (preg_match_all('/\d+/', $ids, $matches)) {
    for($i = 0; $i < count($matches[0]);++$i) {
        $id = $matches[0][$i];
        $imageSrc = wp_get_attachment_image_src($id)[0];
        $imageSizes = wp_get_attachment_image_sizes($id);
        $imageSrcset = wp_get_attachment_image_srcset($id);

        echo "<div style='width: 100%; height: 300px; background-color: black; text-align: center'>
                <img src='$imageSrc' style='height: 100%; width: auto;'
                srcset='$imageSrcset'
                sizes='$imageSizes' >
              </div>";
    }
}

?>
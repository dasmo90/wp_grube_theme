<?php
/**
 * Gallery module. (overwrites default theme behaviour)
 *
 * @global body (unused)
 * @global parameters
 */
?>

<?php
$ids = $parameters['ids'];
$maxSize = $parameters['max-size'] | 6;
?>

<?php if (preg_match_all('/\d+/', $ids, $matches)): ?>

<div class="c-gallery">
    <div class="c-gallery__outer m-scooch m-fluid">
        <div class="c-gallery__inner m-scooch-inner">
        <?php
        $size = count($matches[0]);
        for($i = 0; $i < $size;++$i) {
            $id = $matches[0][$i];
            $mActive = $i === 0 ? ' m-active' : '';
            $imageSrc = wp_get_attachment_image_src($id)[0];
            $imageSizes = wp_get_attachment_image_sizes($id);
            $imageSrcset = wp_get_attachment_image_srcset($id);
            $caption = wp_get_attachment_caption($id);
            if ($caption) {
                $caption = "<div class='c-gallery__item-caption'>$caption</div>";
            }
            echo "<div class='c-gallery__item m-item$mActive'>
                    <img src='$imageSrc' srcset='$imageSrcset' sizes='$imageSizes' >
                    $caption
                  </div>";
    }
        ?>
        </div>

        <?php if ($size < $maxSize): ?>

        <div class="c-gallery__controls m-scooch-controls">
            <span class="c-gallery__control c-gallery__control-arrow -accent c-gallery__control-arrow--prev"
                  data-m-slide="prev">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
            </span>
            <span class="c-gallery__control c-gallery__control-arrow -accent c-gallery__control-arrow--next"
                  data-m-slide="next">
                <i class="fa fa-chevron-right" aria-hidden="true"></i>
            </span>
            <span class="c-gallery__control c-gallery__control-boobles -accent">
                <?php
                for($i = 1; $i <= $size;++$i) {
                    $mActive = $i === 0 ? ' m-active' : '';
                    echo "<span data-m-slide='$i' class='c-gallery__control-booble$mActive'></span>";
                }
                ?>
        </div>

        <?php endif ?>
    </div>
    <!-- construct the carousel -->
    <script>
        jQuery('.m-scooch').scooch({
            autoHideArrows: true
        });
    </script>
</div>

<?php endif ?>
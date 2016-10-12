<?php
/**
 * Gallery module. (overwrites default theme behaviour)
 *
 * @global body (unused)
 * @global parameters
 */
?>

<?php
$selector = uniqid('c-gallery-');
$ids = $parameters['ids'];
$maxSize = $parameters['max-size'] | 6;
?>

<?php if (preg_match_all('/\d+/', $ids, $matches)): ?>

<div class="c-gallery <?php echo $selector?> -accent">
    <div class="c-gallery__outer m-scooch m-fluid">
        <div class="c-gallery__inner m-scooch-inner">
        <?php
        $size = count($matches[0]);
        for($i = 0; $i < $size;++$i) {
            $id = $matches[0][$i];
            $mActive = $i === 0 ? ' m-active' : '';
            $imageSrc = wp_get_attachment_image_src($id)[0];
            $imageSrcset = wp_get_attachment_image_srcset($id);

            echo "<div class='c-gallery__item m-item$mActive'>
                    <img src='$imageSrc' srcset='$imageSrcset' sizes='100vw' >
                  </div>";
        }
        ?>
        </div>

        <?php if ($size > 1 && $size < $maxSize): ?>

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
    <div class="c-gallery__captions">
    <?php
    for($i = 0; $i < $size;++$i) {
        $id = $matches[0][$i];
        $caption = wp_get_attachment_caption($id);
        $active = $i === 0 ? ' -active' : '';
        echo "<div class='c-gallery__caption$active'>$caption</div>";
    }
    ?>
    </div>
</div>
<!-- construct the carousel -->
<script>
    var scooch = jQuery('.<?php echo $selector?> .m-scooch').scooch({
        autoHideArrows: true
    });
    var captions = jQuery('.<?php echo $selector?> .c-gallery__caption');
    scooch.on('beforeSlide', function(event, start, end) {
        captions.each(function(index, caption) {
            console.log(arguments);
            if(index === end - 1) {
                jQuery(caption).addClass('-active');
            } else {
                jQuery(caption).removeClass('-active');
            }
        });
    });
</script>

<?php endif ?>
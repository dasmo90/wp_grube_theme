<?php
/**
 * Gallery module. (overwrites default theme behaviour)
 *
 * @global image
 * @global id
 */
?>

<div class="c-gallery <?php echo $selector?> -accent">
    <div class="c-gallery__outer">
        <div class="c-gallery__inner">
            <div class='c-gallery__item'>
<?php
    echo $image;
?>
            </div>
        </div>
    </div>
    <div class="c-gallery__captions">
        <?php
            $caption = wp_get_attachment_caption($id);
            echo "<div class='c-gallery__caption -active'>$caption</div>";
        ?>
    </div>
</div>

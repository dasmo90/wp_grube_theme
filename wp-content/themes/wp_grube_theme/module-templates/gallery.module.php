<?php
/**
 * Gallery module. (overwrites default theme behaviour)
 *
 * @global body (unused)
 * @global parameters
 */
?>

<div class="m-scooch m-fluid">
    <div class="m-scooch-inner">
<?php
$ids = $parameters['ids'];
if (preg_match_all('/\d+/', $ids, $matches)) {
    for($i = 0; $i < count($matches[0]);++$i) {
        $id = $matches[0][$i];
        $mActive = $i === 0 ? ' m-active' : '';
        $imageSrc = wp_get_attachment_image_src($id)[0];
        $imageSizes = wp_get_attachment_image_sizes($id);
        $imageSrcset = wp_get_attachment_image_srcset($id);

        echo "<div class='m-item$mActive'>
                <img src='$imageSrc' srcset='$imageSrcset' sizes='$imageSizes' >
              </div>";
    }
}
?>
    </div>
    <div class="m-scooch-controls">
        <span data-m-slide="prev">Previous</span>
        <span data-m-slide="1" class="m-active">1</span>
        <span data-m-slide="2">2</span>
        <span data-m-slide="3">3</span>
        <span data-m-slide="next">Next</span>
    </div>
</div>

<!-- construct the carousel -->
<script>jQuery('.m-scooch').scooch()</script>
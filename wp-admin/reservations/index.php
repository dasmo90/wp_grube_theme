<?php

require_once( dirname( __FILE__ ) . '/../admin.php' );

get_header();

$pageName = 'Reservierungen';
?>

<div class="container">
    <div class="row">
        <div id="primary" class="col-md-9 content-area">
            <main id="main" class="site-main" role="main">

                <article class = "post-content">

                    <header class="entry-header">
                        <span class="screen-reader-text"><?php echo $pageName ?></span>
                        <h1 class="entry-title"><?php echo $pageName ?></h1>

                        <div class="entry-meta"></div>
                    </header>


                    <div class="entry-content">
                        Test
                    </div>

                    <footer class="entry-footer"></footer>

                </article>
            </main>
        </div>
    </div>
</div>


<?php
get_footer();
?>
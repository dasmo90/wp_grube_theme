<?php

require_once( dirname( __FILE__ ) . '/../admin.php' );

get_header();

$pageName = 'Reservierungen';
?>

<div class="container">
    <div class="row">
        <div id="primary" class="col-md-12 content-area">
            <main id="main" class="site-main" role="main">

                <article class = "post-content">

                    <header class="entry-header">
                        <span class="screen-reader-text"><?php echo $pageName ?></span>
                        <h1 class="entry-title"><?php echo $pageName ?></h1>

                        <div class="entry-meta"></div>
                    </header>


                    <div class="entry-content">
                        <div class="c-grubeWidget c-reservations">
                            <a id="reservations"></a>
                            <h3>Reservierung hinzufügen</h3>
                            <form method="get" action="#reservations">
                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input class="form-control" type="text" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="arrival_day">Anreisetag:</label>
                                    <input class="form-control" type="date" id="arrival_day" name="arrival_day" required>
                                </div>
                                <div class="form-group">
                                    <label for="departure_day">Abreisetag:</label>
                                    <input class="form-control" type="date" id="departure_day" name="departure_day" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </form>
                        </div>

                        <?php echo htmlspecialchars($_GET['arrival_day']); ?>

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
<?php

require_once( dirname( __FILE__ ) . '/../admin.php' );

get_header();

global $wpdb;

$tableName = 'reservations';
$pageName = 'Reservierungen';
$format = 'd.m.Y';


function hasCollision($arrivalDate, $departureDate) {
    global $wpdb, $tableName;
    $query = "SELECT COUNT(*)
              FROM $tableName
              WHERE
                (end >= '$arrivalDate' AND end <= '$departureDate')
                OR
                (start <= '$arrivalDate' AND start >= '$departureDate')";
    $reservations = $wpdb->get_var($query);
    return intval($reservations) > 0;
}

function saveToDataBase($arrivalTime, $departureTime, $name) {
    global $wpdb;
    if(!($arrivalTime && $departureTime && $name)) {
        return -1;
    }
    $arrivalDate = date("Y-m-d", $arrivalTime);
    $departureDate = date("Y-m-d", $departureTime);
    if(hasCollision($arrivalDate, $departureDate)) {
        return -2;
    }
    $result = $wpdb->insert('reservations', array(
        'name' => $name,
        'start' => $arrivalDate,
        'end' => $departureDate
    ));
    if($result === 1) {
        return $wpdb->insert_id;
    }
    return -3;
}

function formInput($paramKey, $paramValue, $valid, $fieldName, $type = 'text') {
    $inputClass = $valid ? '' : ' invalid';
    $message = $valid ? '' : "<p class='form-error-message'>$fieldName ist ungültig.</p>";
    echo "<div class='form-group'>
            <label for='arrival_day'>$fieldName:</label>
              <input class='form-control$inputClass'
                     type='$type'
                     id='$paramKey'
                     name='$paramKey'
                     value='$paramValue'
                     required>
                     $message
          </div>";
}
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

                    <?php

                    $arrivalKey = 'arrival_day';
                    $arrivalValue = htmlspecialchars($_POST[$arrivalKey]);
                    $arrivalTime = strtotime($arrivalValue);
                    $departureKey = 'departure_day';
                    $departureValue = htmlspecialchars($_POST[$departureKey]);
                    $departureTime = strtotime($departureValue);
                    $nameKey = 'name';
                    $nameValue = htmlspecialchars($_POST[$nameKey]);
                    $nameValid = $nameValue != null && $nameValue != '';

                    $wrongOrderMessage = '';
                    $message = '';

                    if($_SERVER['REQUEST_METHOD'] === 'POST') {

                        if($arrivalTime && $departureTime) {

                            if($arrivalTime < $departureTime) {
                                $success = saveToDataBase($arrivalTime, $departureTime, $nameValue);

                                if($success > 0) {
                                    $arrivalDatePretty = date($format, $arrivalTime);
                                    $departureDatePretty = date($format, $departureTime);
                                    $message = "<p>Die Reservierung ($arrivalDatePretty bis $departureDatePretty) wurde erfolgreich eingetragen.</p>";
                                    $arrivalValue = '';
                                    $departureValue = '';
                                    $nameValue = '';
                                } else {
                                    $errorCode = -$success;
                                    $message = "<p>Beim Eintragen der Reservierung ist ein Fehler aufgetreten. (Code: 52$errorCode)";
                                }
                            } else {
                                $wrongOrderMessage =
                                    '<p class="form-error-message">Anreisetag muss <strong>vor</strong> dem Abreisetag liegen</p>';
                            }
                        }

                    } else {
                        $arrivalTime = true;
                        $departureTime = true;
                        $nameValid = true;
                    }

                    ?>

                    <div class="entry-content">

                        <div class="c-grubeWidget c-reservations">
                            <a id="reservations"></a>
                            <h3>Reservierung hinzufügen</h3>
                            <form method="post" action="#reservations">
                                <?php
                                    formInput($nameKey, $nameValue, $nameValid, 'Name');
                                    formInput($arrivalKey, $arrivalValue, $arrivalTime, 'Anreisetag', 'date');
                                    formInput($departureKey, $departureValue, $departureTime, 'Abreisetag', 'date');

                                    echo $wrongOrderMessage;
                                ?>

                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </form>
                        </div>
                        <p></p>
                        <?php
                        if($message) {
                            echo "<div class='alert alert-success text-left'>$message</div>";
                        }
                        ?>
                        <ul class="list-group">
                            <?php
                                $now = date("Y-M-d");
                                $query = "SELECT * FROM reservations WHERE '$now' <= end ORDER BY start";
                                $reservations = $wpdb->get_results($query, OBJECT);

                                foreach($reservations as $reservation) {
                                    $start = date($format, strtotime($reservation->start));
                                    $end = date($format, strtotime($reservation->end));
                                    $name = $reservation->name;

                                    echo "<li class='list-group-item'><div>$name</div><div>$start - $end</div></li>";
                                }
                            ?>
                        </ul>

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
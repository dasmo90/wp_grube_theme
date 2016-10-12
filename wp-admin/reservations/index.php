<?php

require_once( dirname( __FILE__ ) . '/../admin.php' );

get_header();

global $wpdb;

define('RESERVATIONS_TABLE', 'reservations');

define('ARRIVAL_KEY', 'arrival_day');
define('DEPARTURE_KEY', 'departure_day');
define('NAME_KEY', 'name');
define('METHOD_KEY', 'method');
define('ID_KEY', 'id');

define('MSG_WRONG_ORDER',
    '<p class="form-error-message">Anreisetag muss <strong>vor</strong> dem Abreisetag liegen</p>');

$pageName = 'Reservierungen';
$format = 'd.m.Y';


function hasCollision($arrivalDate, $departureDate) {
    global $wpdb;
    $tableName = RESERVATIONS_TABLE;
    $query = "SELECT COUNT(*)
              FROM $tableName
              WHERE NOT (start > '$departureDate' OR '$arrivalDate' > end)";
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
    $result = $wpdb->insert(RESERVATIONS_TABLE, array(
        'name' => $name,
        'start' => $arrivalDate,
        'end' => $departureDate
    ));
    if($result === 1) {
        return $wpdb->insert_id;
    }
    return -3;
}

function deleteFromDataBase($id) {
    global $wpdb;
    $result = $wpdb->delete(RESERVATIONS_TABLE, array('id' => $id));
    if($result === false) {
        return -1;
    }
    elseif ($result === 0) {
        return -2;
    }
    return $result;
}

function uiMessage($template) {
    if($template === 'add:success') {
        $arg1 = func_get_arg(1);
        $arg2 = func_get_arg(2);
        return "<div class='alert alert-success text-left'>
                    <p>Die Reservierung ($arg1 bis $arg2) wurde erfolgreich eingetragen</p>
                </div>";
    }
    elseif($template === 'add:error') {
        $arg1 = func_get_arg(1);
        return "<div class='alert alert-danger text-left'>
                    <p>Beim Eintragen der Reservierung ist ein Fehler aufgetreten. (Code: 52$arg1)</p>
                </div>";
    }
    elseif($template === 'delete:success') {
        return '<div class="alert alert-success text-left"><p>Die Reservierung wurde erfolgreich gelöscht.</p></div>';
    }
    elseif($template === 'delete:error') {
        $arg1 = func_get_arg(1);
        return "<div class='alert alert-danger text-left'>
                    <p>Beim Löschen ist ein Fehler aufgetreten (Code: 52$arg1)</p>
                </div>";
    }
    return null;
}

function uiDeleteLink($id, $label, $style = 'primary') {
    $methodKey = METHOD_KEY;
    $idKey = ID_KEY;
    return "<form method='post' action='#reservations'>
        <input type='hidden' name='$methodKey' value='delete'>
        <input type='hidden' name='$idKey' value='$id'>
        <button class='btn btn-$style grube--right-centered' type='submit'>$label</button>
    </form>";
}

function uiFormInput($paramKey, $paramValue, $valid, $fieldName, $type = 'text') {
    $inputClass = $valid ? '' : ' invalid';
    $message = $valid ? '' : "<p class='form-error-message'>$fieldName ist ungültig.</p>";
    return "<div class='form-group'>
            <label for='$paramKey'>$fieldName:</label>
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

                    // defaults
                    $arrivalTime = true;
                    $departureTime = true;
                    $nameValid = true;

                    $arrivalValue = '';
                    $departureValue = '';
                    $nameValue = '';

                    $wrongOrderMessage = '';
                    $message = '';

                    $addedId = -1;

                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                        $method = htmlspecialchars($_POST[METHOD_KEY]);

                        if ($method === 'add') {
                            $arrivalValue = htmlspecialchars($_POST[ARRIVAL_KEY]);
                            $departureValue = htmlspecialchars($_POST[DEPARTURE_KEY]);
                            $nameValue = htmlspecialchars($_POST[NAME_KEY]);

                            $arrivalTime = strtotime($arrivalValue);
                            $departureTime = strtotime($departureValue);
                            $nameValid = $nameValue != null && $nameValue != '';

                            // handle data of add
                            if($arrivalTime && $departureTime) {
                                if($arrivalTime < $departureTime) {
                                    $result = saveToDataBase($arrivalTime, $departureTime, $nameValue);

                                    if($result > 0) {
                                        $addedId = $result;
                                        $arrivalDatePretty = date($format, $arrivalTime);
                                        $departureDatePretty = date($format, $departureTime);
                                        $deleteLink = uiDeleteLink($result, 'Rückgängig');
                                        $message = uiMessage('add:success', $arrivalDatePretty, $departureDatePretty);
                                        $arrivalValue = '';
                                        $departureValue = '';
                                        $nameValue = '';
                                    } else {
                                        $message = uiMessage('add:error', -$result);
                                    }
                                } else {
                                    $wrongOrderMessage = MSG_WRONG_ORDER;
                                }
                            }
                        }
                        elseif ($method === 'delete') {
                            $id = htmlspecialchars($_POST[ID_KEY]);
                            $result = deleteFromDataBase($id);
                            if($result > 0) {
                                $message = uiMessage('delete:success');
                            } else {
                                $message = uiMessage('delete:error', -$result);
                            }
                        }
                    }
                    ?>

                    <div class="entry-content">

                        <div class="c-grubeWidget c-reservations">
                            <a id="reservations"></a>
                            <h3>Reservierung hinzufügen</h3>
                            <form method="post" action="#reservations">
                                <input type='hidden' name='method' value='add'>
                                <?php
                                    echo uiFormInput(NAME_KEY, $nameValue, $nameValid, 'Name');
                                    echo uiFormInput(ARRIVAL_KEY, $arrivalValue, $arrivalTime, 'Anreisetag', 'date');
                                    echo uiFormInput(DEPARTURE_KEY, $departureValue, $departureTime, 'Abreisetag',
                                        'date');

                                    echo $wrongOrderMessage;
                                ?>

                                <button type="submit" class="btn btn-primary">Speichern</button>
                            </form>
                        </div>
                        <p></p>
                        <?php
                        if($message) {
                            echo $message;
                        }
                        ?>
                        <ul class="list-group">
                            <?php
                                $now = date("Y-M-d");
                                $reservationsTable = RESERVATIONS_TABLE;
                                $query = "SELECT * FROM $reservationsTable WHERE '$now' <= end ORDER BY start";
                                $reservations = $wpdb->get_results($query, OBJECT);

                                foreach($reservations as $reservation) {
                                    $start = date($format, strtotime($reservation->start));
                                    $end = date($format, strtotime($reservation->end));
                                    $name = $reservation->name;
                                    $isNew = $addedId == $reservation->id;
                                    $deleteLink =
                                        uiDeleteLink($reservation->id, 'Löschen', $isNew ? 'default' : 'primary');
                                    $active = $isNew ? ' active' : '';

                                    echo "<li class='list-group-item$active'>
                                            <div>$name</div>
                                            <div>$start - $end</div>
                                            <div>$deleteLink</div>
                                          </li>";
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
<?php
/**
 * Created by IntelliJ IDEA.
 * User: mbuerger
 * Date: 09.09.2016
 * Time: 17:28
 */

if (!function_exists('obtainReservationsBetween')) {
    $reservedData = array(
    array('empty', 'frei'),
    array('almostEmpty', 'noch frei'),
    array('almostFull', 'wenig frei'),
    array('full', 'ausgebucht'),
);

function obtainReservationsBetween($startDateString, $endDateString) {
    global $wpdb;
    $query = "SELECT * FROM reservations WHERE '$startDateString' <= end AND '$endDateString' >= start";
return $wpdb->get_results($query, OBJECT);
}

function lastMonday($date) {
if (date('w', $date) == 1) {
return $date;
} else {
return strtotime('last monday', $date);
}
}

function nextDay($date) {
return strtotime('+1 day', $date);
}

function weeksInMonth($firstWeekday, $daysInMonth) {
return ceil(($firstWeekday + $daysInMonth - 1) / 7);
}

function isReservedDateClass($date, $reservations) {
foreach($reservations as $reservation) {
$start = strtotime($reservation->start);
$end = strtotime($reservation->end);
if ($date > $start && $date < $end) {
return ' reserved';
} else  if($date == $start || $date == $end) {
return ' halfReserved';
}
}
return '';
}

function isReservedMonth($monthTime, $daysInMonth,  $reservations) {
$reservedDays = 0;
foreach($reservations as $reservation) {
$start = strtotime($reservation->start);
$end = strtotime($reservation->end);
$firstDay = toDay($monthTime);
$reservedDays += reservedDays(toDay($start), toDay($end), $firstDay, $firstDay + $daysInMonth - 1);
}
return intval(4 * $reservedDays / ($daysInMonth + 1));
}

function toDay($date) {
return intval($date / (60 * 60 * 24));
}

function reservedDays($startDay, $endDay, $firstDay, $lastDay) {
if($lastDay < $startDay || $firstDay > $endDay) {
return 0;
}
return $lastDay - $firstDay + 1 + min($firstDay - $startDay, 0) + min($endDay - $lastDay, 0);
}

function reservationMonth($monthOffset, $reservations) {
global $reservedData;

$monthTime = strtotime("first day of $monthOffset months");
$monthName = date('F Y', $monthTime);
$monthNumber = date('n', $monthTime);
$currentDate = lastMonday($monthTime);
$firstWeekday = date('N', $monthTime);
$daysInMonth = date('t', $monthTime);
$weeksInMonth = weeksInMonth($firstWeekday, $daysInMonth);
$isReservedMonth = isReservedMonth($monthTime, $daysInMonth, $reservations);

$reservedClass = $reservedData[$isReservedMonth][0];
$reservedText = $reservedData[$isReservedMonth][1];

$month = '<div class="c-calendar__month">';
    $month.= "  <div class='c-calendar__monthHeader' onclick='toggleMonth(\"month-$monthOffset\");'>";
        $month.= "    <span class='c-calendar__monthName'>$monthName</span>";
        $month.= "    <span class='c-calendar__monthReservation $reservedClass'>$reservedText</span>";
        $month.= '  </div>';
    $month.= "  <div id='month-$monthOffset' class='c-calendar__monthView'>";
        $month.= "    <div class='c-calendar__monthTable'>";
            $month.= '      <table>
                <tr>
                    <th>Mo</th>
                    <th>Di</th>
                    <th>Mi</th>
                    <th>Do</th>
                    <th>Fr</th>
                    <th>Sa</th>
                    <th>So</th>
                </tr>';
                for($weeks = 0;$weeks < 6;$weeks++) {
                $emptyWeek = $weeks < $weeksInMonth ? '' : 'emptyWeek';
                $month .= "<tr class='$emptyWeek'>";
                for ($day = 0; $day < 7; $day++) {
                $thisDay = date("j", $currentDate);
                $inMonthClass = $monthNumber === date('n', $currentDate) ? ' inMonth' : '';
                $reservedClass = isReservedDateClass($currentDate, $reservations);
                $currentDate = nextDay($currentDate);
                $month .= "<td class='c-calendar__day$inMonthClass$reservedClass'>$thisDay</td>";
                }
                $month .= '</tr>';
                }
                $month.= '      </table>
        </div>
    </div>
</div>';
return $month;
}

function reservationCalendar($months = 12, $monthOffset = 0) {
$monthMax = $months - 1;
$start = date("Y-m-01", strtotime("$monthOffset months"));
$end = date("Y-m-t", strtotime("$monthMax months"));

$reservations = obtainReservationsBetween($start, $end);

$calendar = '<div class="c-grubeWidget c-calendar">';
    for($i = $monthOffset; $i < $monthOffset + $months; $i++) {
    $calendar.= reservationMonth($i, $reservations);
    }
    $calendar.= '</div>';
$calendar.= '
<script>
            function toggleMonth(id) {
                var monthView = document.getElementById(id);
                monthView.classList.toggle("visible");
            }
        </script>
';
return $calendar;
}
}

echo reservationCalendar();
?>
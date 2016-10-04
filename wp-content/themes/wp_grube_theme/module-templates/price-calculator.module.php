<div class="c-grubeWidget c-priceCalc">
    <a id="price_calc" class="anchor"></a>
    <h3 >Preisrechner</h3>
    <div class="c-priceCalc__label"><label for="arrival_month">Wann wollen Sie anreisen?</label></div>
    <div class="c-priceCalc__select">
        <select id="arrival_month" name="arrival_month" onchange="price_calc_changed()">
            <option selected value=''>--Monat auswählen--</option>
            <option value='0'>Januar</option>
            <option value='1'>Februar</option>
            <option value='2'>März</option>
            <option value='3'>April</option>
            <option value='4'>Mai</option>
            <option value='5'>Juni</option>
            <option value='6'>Juli</option>
            <option value='7'>August</option>
            <option value='8'>September</option>
            <option value='9'>Oktober</option>
            <option value='10'>November</option>
            <option value='11'>Dezember</option>
        </select>
    </div>
    <div class="c-priceCalc__label"><label for="guests">Mit wie vielen Personen reisen Sie an?</label></div>
    <div class="c-priceCalc__select">
        <select id="guests" name="guests" onchange="price_calc_changed()">
            <option selected value=''>--Anzahl auswählen--</option>
            <option value='0'>1</option>
            <option value='1'>2</option>
            <option value='2'>3</option>
            <option value='3'>4</option>
            <option value='4'>5</option>
            <option value='5'>6</option>
        </select>
    </div>
    <div class="c-priceCalc__label">
        <strong>Preis pro Übernachtung: <span id="price_per_night" style="margin-left: 10pt;">-</span></strong>
    </div>
    <p>
        <?php echo $body; ?>
    </p>
    <script>
        var monthTable = [<?php echo $parameters['january']; ?>,
                          <?php echo $parameters['february']; ?>,
                          <?php echo $parameters['march']; ?>,
                          <?php echo $parameters['april']; ?>,
                          <?php echo $parameters['may']; ?>,
                          <?php echo $parameters['june']; ?>,
                          <?php echo $parameters['july']; ?>,
                          <?php echo $parameters['august']; ?>,
                          <?php echo $parameters['september']; ?>,
                          <?php echo $parameters['october']; ?>,
                          <?php echo $parameters['november']; ?>,
                          <?php echo $parameters['december']; ?>];

        function adjust_price(value) {
            return value.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+,)/g, '$1.') + ' €';
        }

        function price_calc_changed() {
            var arrivalMonth = document.getElementById("arrival_month").selectedIndex;
            var guests = document.getElementById("guests").selectedIndex;

            if(arrivalMonth > 0 && guests > 0) {
                document.getElementById("price_per_night").innerHTML =
                        adjust_price(monthTable[arrivalMonth - 1] + Math.max(guests - 2, 0) * <?php echo $parameters['person-price']; ?>);
            } else {
                document.getElementById("price_per_night").innerHTML = '-';
            }
        }
    </script>
</div>
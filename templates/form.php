<div class="taxes">
  <form class="taxes__form">
    <div class="taxes__row">
      <label class="taxes__label"><?php _e('Nazwa produktu', 'ak-taxes') ?><br>
        <input class="taxes__field taxes__field--product" name="product" type="text">
      </label>
      </div>
    <div class="taxes__row">
      <label class="taxes__label"><?php _e('Kwota netto', 'ak-taxes') ?><br>
        <input class="taxes__field taxes__field--price" name="price" type="number">
      </label>
      </div>
    <div class="taxes__row">
      <label class="taxes__label"><?php _e('Waluta', 'ak-taxes') ?><br>
        <input class="taxes__field taxes__field--currency" name="currency" type="text" value="PLN" disabled>
      </label>
    </div>
    <div class="taxes__row">
      <label class="taxes__label"><?php _e('Stawka VAT', 'ak-taxes') ?><br>
        <select class="taxes__field taxes__field--tax-rate" name="tax">
          <option value="23"><?php _e('23%', 'ak-taxes') ?></option>
          <option value="22"><?php _e('22%', 'ak-taxes') ?></option>
          <option value="8"><?php _e('8%', 'ak-taxes') ?></option>
          <option value="7"><?php _e('7%', 'ak-taxes') ?></option>
          <option value="5"><?php _e('5%', 'ak-taxes') ?></option>
          <option value="3"><?php _e('3%', 'ak-taxes') ?></option>
          <option value="0"><?php _e('0%', 'ak-taxes') ?></option>
          <option value="zw"><?php _e('zw.', 'ak-taxes') ?></option>
          <option value="np"><?php _e('np.', 'ak-taxes') ?></option>
          <option value="oo"><?php _e('o.o.', 'ak-taxes') ?></option>
        </select>
      </label>
    </div>
    <div class="taxes__row">
      <button class="taxes__btn"><?php _e('Oblicz', 'ak-taxes') ?></button>
    </div>
    <div class="taxes__result"></div>
    <input class="taxes__security" name="security" type="hidden" value="<?php echo $nonce ?>">
  </form>
</div>
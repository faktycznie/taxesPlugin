const taxShortcode = () => {
  const btn = document.querySelectorAll('.taxes__btn');
  if( btn ) {
    btn.forEach((el) => {
      el.addEventListener('click', (e) => {
        e.preventDefault();
        const form = e.target.parentNode.parentNode; //parent form
        makeCalculations(form);
      });
    });
  }
}

const makeCalculations = (form) => {
  const product = form.querySelector('.taxes__field--product').value.trim();
  const price = Math.abs(form.querySelector('.taxes__field--price').value);
  const currency = form.querySelector('.taxes__field--currency').value.trim();
  const security = form.querySelector('.taxes__security').value;
  let taxrate = form.querySelector('.taxes__field--tax-rate').value;

  showMessage(form, ''); //clear response

  const err = [];

  if( ! product ) {
    err.push(ak_taxes.labels.no_product);
  }
  if( ! price ) {
    err.push(ak_taxes.labels.no_price);
  }
  if( ! currency ) {
    err.push(ak_taxes.labels.no_currency);
  }
  if( ! taxrate ) {
    err.push(ak_taxes.labels.taxrate);
  }

  if( err.length ) {
    const message = err.join('<br>');
    showMessage(form, message, 'error');
  } else {
    const orgTaxrate = taxrate;
    if(taxrate === 'zw' || taxrate === 'np' || taxrate === 'oo') taxrate = 0;

    const tax = Number(price) * (Number(taxrate)/100);
    const finalPrice = Number(price) + tax;

    let msg = ak_taxes.labels.result;

    msg = msg.replace('{product}', product);
    msg = msg.replace('{finalPrice}', finalPrice.toFixed());
    msg = msg.replace('{tax}', tax.toFixed());

    showMessage(form, msg);

    const data = { product, price, finalPrice, currency, taxrate, orgTaxrate, tax, msg, security }
    saveData(form, data);
  }
}

const showMessage = (form, message, type = 'success') => {
  const container = form.querySelector('.taxes__result');
  if( container ) {
    container.innerHTML = '';

    if( message ) {
      let msg = document.createElement('div');
      msg.innerHTML = message;
      msg.classList.add('taxes__message');
      msg.classList.add('taxes__message--' + type);

      container.appendChild(msg);
    }
  }
}

const clearForm = (form) => {
  form.reset();
}

const saveData = (form, data) => {

  const params = new URLSearchParams({
    action: 'ak_taxes_save_data',
    security: data.security,
    product: data.product,
    price: data.price,
    finalPrice: data.finalPrice,
    currency: data.currency,
    orgTaxrate: data.orgTaxrate,
    taxrate: data.taxrate,
    tax: data.tax,
    msg: data.msg
  });

  fetch(ak_taxes.url, {
    method: 'POST',
    body: params
  }).then(function (response) {
    return response.json();
  }).then(response => {
    console.log(response); //just for example
    clearForm(form);
  }).catch(err => { 
    console.log(err);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  taxShortcode();
});
$(function() {
    let $form = $('.js-checkout-form');
    //we need to download the jquery.payement.js librairy to do this
    $form.find('.js-cc-number').payment('formatCardNumber');
    $form.find('.js-cc-exp').payment('formatCardExpiry');
    $form.find('.js-cc-cvc').payment('formatCardCVC');
    $form.submit(function(event) {
        event.preventDefault();

        // Disable the submit button to prevent repeated clicks:
        $form.find('.js-submit-button').prop('disabled', true);
        // Request a token from Stripe:
        Stripe.card.createToken($form, stripeResponseHandler);
        return false;
    });
});

function stripeResponseHandler(status, response) {
    // Grab the form:
    let $form = $('.js-checkout-form');
    if (response.error) { // Problem!
        // Show the errors on the form:
        $form.find('.js-checkout-error')
            .text(response.error.message)
            .removeClass('hidden');
        $form.find('.js-submit-button').prop('disabled', false); // Re-enable submission
    } else { // Token was created!
        $form.find('.js-checkout-error')
            .addClass('hidden');
        // Get the token ID:
        var token = response.id;
        // Insert the token ID into the form so it gets submitted to the server:
        $form.append($('<input type="hidden" name="stripeToken">').val(token));
        // Submit the form:
        $form.get(0).submit();
    }
}
$(document).ready(function ()
{
    bluesnap.embeddedCheckoutSetup(json, function (eCheckoutResult) {
        callbackFunction(eCheckoutResult);
    });
    if (openCheckout) {
        bluesnap.embeddedCheckoutOpen();
    }
});
<?php
// PayHere Sandbox Credentials
// Switch these to your live credentials + set $payhere_sandbox = false when going live.
define('PAYHERE_MERCHANT_ID', '1238113');
define('PAYHERE_MERCHANT_SECRET', 'ODczODg2MTUyMTY1NzA2NTY4NTIxMDE4NDQ4NjEyMzc5MjgzNTI4');
define('PAYHERE_SANDBOX', true); // true = sandbox testing, false = live

// The checkout URL PayHere uses (sandbox vs live)
define('PAYHERE_CHECKOUT_URL', PAYHERE_SANDBOX
    ? 'https://sandbox.payhere.lk/pay/checkout'
    : 'https://www.payhere.lk/pay/checkout'
);
?>
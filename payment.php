require_once('stripe-php/init.php');

// Set your API keys
\Stripe\Stripe::setApiKey('your_secret_key');

// Get the payment information from the form
$amount = $_POST['amount'] * 100; // Stripe requires the amount in cents
$name = $_POST['name'];
$email = $_POST['email'];
$card_number = $_POST['card_number'];
$card_expiry = explode('/', $_POST['card_expiry']);
$card_exp_month = trim($card_expiry[0]);
$card_exp_year = trim($card_expiry[1]);
$card_cvc = $_POST['card_cvc'];

// Create a Stripe customer
$customer = \Stripe\Customer::create(array(
  'email' => $email,
  'source'  => $token
));

// Charge the customer's card
$charge = \Stripe\Charge::create(array(
  'customer' => $customer->id,
  'amount'   => $amount,
  'currency' => 'usd'
));

// Generate an invoice
$invoice = \Stripe\Invoice::create(array(
  'customer' => $customer->id,
  'amount'   => $amount,
  'currency' => 'usd',
  'description' => 'Donation',
  'statement_descriptor' => 'Donation'
));

// Send an email to the customer with the invoice details
$to = $email;
$subject = 'Donation Invoice';
$message = 'Thank you for your donation of $' . ($amount / 100) . '. An invoice has been generated and is attached to this email.';
$headers = 'From: your_email@example.com' . "\r\n" .
    'Reply-To: your_email@example.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$attachment = $invoice->invoice_pdf;

mail($to, $subject, $message, $headers, $attachment);

<?php
$publishableKey = getenv('STRIPE_PUBLISHABLE_KEY') ?: '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout personalizado Stripe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/modern-normalize/2.0.0/modern-normalize.min.css" integrity="sha512-4O3hxbWrayKDh+qUfzkThRYDxU/sdjDU2mDYLk4SZn6BRNuxq2chERL7TXm1XYsJShrZqP6rL/BLr3hfsw4P1g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            background: #f6f9fc;
            color: #0a2540;
            margin: 0;
            padding: 0 16px;
        }
        .container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            padding: 32px;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        }
        h1 {
            margin-bottom: 16px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        .StripeElement {
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
        }
        select, button, input[type="number"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            margin-top: 8px;
            font-size: 1rem;
        }
        button {
            background: #635bff;
            color: #fff;
            border: none;
            cursor: pointer;
            margin-top: 24px;
        }
        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            background: #fde68a;
            color: #78350f;
            margin-bottom: 16px;
        }
        .status {
            margin-top: 16px;
            min-height: 24px;
        }
        .success {
            color: #047857;
        }
        .error {
            color: #b91c1c;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Produto único - $ 1.000,00</h1>
    <p>Preencha os dados do cartão de crédito ou débito para finalizar a compra.</p>
    <?php if ($publishableKey === ''): ?>
        <div class="alert">
            Defina a variável de ambiente <code>STRIPE_PUBLISHABLE_KEY</code> antes de usar o checkout.
        </div>
    <?php endif; ?>
    <form id="payment-form">
        <label for="card-element">Dados do cartão</label>
        <div id="card-element" class="StripeElement"></div>

        <label for="installments">Parcelas (opcional)</label>
        <select id="installments" name="installments">
            <option value="1">Pagamento à vista</option>
            <option value="3">3x sem juros</option>
            <option value="6">6x sem juros</option>
            <option value="12">12x sem juros</option>
        </select>

        <button id="submit-button" type="submit">Pagar $ 1.000,00</button>
        <div id="status-message" class="status"></div>
    </form>
</div>
<script src="https://js.stripe.com/v3/"></script>
<script>
    window.STRIPE_PUBLISHABLE_KEY = "<?php echo htmlspecialchars($publishableKey, ENT_QUOTES); ?>";
    window.CHECKOUT_CONFIG = {
        productName: 'Produto Exemplo',
        amount: 1000,
        currency: 'usd'
    };
</script>
<script src="/js/checkout.js" defer></script>
</body>
</html>

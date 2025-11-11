<?php
declare(strict_types=1);

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');

$autoloadPath = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        'error' => 'Dependências ausentes. Execute "composer install" antes de tentar novamente.',
    ]);
    exit;
}

require $autoloadPath;

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido.']);
    exit;
}

$secretKey = getenv('STRIPE_SECRET_KEY');

if (!$secretKey) {
    http_response_code(500);
    echo json_encode(['error' => 'Configure a variável STRIPE_SECRET_KEY.']);
    exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if ($payload === null && json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido.']);
    exit;
}

$installments = isset($payload['installments']) ? (int) $payload['installments'] : 1;
$installments = max(1, $installments);

$stripe = new StripeClient($secretKey);

try {
    $params = [
        'amount' => 100000,
        'currency' => 'usd',
        'payment_method_types' => ['card'],
        'description' => 'Produto único de $ 1.000,00',
        'metadata' => [
            'product_name' => 'Produto único',
            'installments_requested' => (string) $installments,
        ],
        'payment_method_options' => [
            'card' => [
                'installments' => [
                    'enabled' => true,
                ],
            ],
        ],
    ];

    if ($installments > 1) {
        $params['payment_method_options']['card']['installments']['plan'] = [
            'count' => $installments,
            'interval' => 'month',
            'type' => 'fixed_count',
        ];
    }

    $paymentIntent = $stripe->paymentIntents->create($params);

    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret,
    ]);
} catch (ApiErrorException $exception) {
    http_response_code(400);
    echo json_encode([
        'error' => $exception->getMessage(),
    ]);
} catch (\Throwable $exception) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erro interno: ' . $exception->getMessage(),
    ]);
}

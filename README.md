# Checkout personalizado com Stripe e PHP

Este projeto demonstra uma integração completa entre PHP e Stripe utilizando um checkout próprio que aceita cartões de crédito ou débito e permite selecionar parcelamento para um produto único no valor fixo de **US$ 1.000,00**.

## Requisitos

- PHP 8.1 ou superior com a extensão `curl` habilitada
- [Composer](https://getcomposer.org/) para instalar dependências
- Conta Stripe com chaves de API ativas (publicável e secreta)

## Configuração

1. Instale as dependências do projeto:

   ```bash
   composer install
   ```

2. Defina as variáveis de ambiente com as suas chaves Stripe:

   ```bash
   export STRIPE_PUBLISHABLE_KEY="pk_test_..."
   export STRIPE_SECRET_KEY="sk_test_..."
   ```

   > Utilize chaves de teste enquanto estiver desenvolvendo.

3. Inicie o servidor embutido do PHP apontando para o diretório `public/`:

   ```bash
   php -S localhost:4242 -t public/
   ```

4. Acesse `http://localhost:4242` no navegador e finalize um pagamento de teste utilizando os cartões disponíveis na [documentação do Stripe](https://stripe.com/docs/testing).

## Como funciona

- O frontend (`public/index.php` e `public/js/checkout.js`) exibe um formulário personalizado utilizando o Stripe.js. O usuário escolhe o número de parcelas e informa os dados do cartão.
- Ao submeter o formulário, o frontend solicita ao backend (`public/create-intent.php`) a criação de um `PaymentIntent` de US$ 1.000,00 com suporte a parcelas.
- Em seguida, o Stripe.js confirma o pagamento com o número de parcelas selecionado via `stripe.confirmCardPayment`.
- Mensagens de feedback são apresentadas ao usuário com o resultado da tentativa de pagamento.

### Parcelamento

O backend habilita o recurso de parcelamento (`payment_method_options[card][installments]`) sempre que o país/bandeira do cartão suportar a funcionalidade. Durante a confirmação do pagamento, o frontend envia o plano de parcelas escolhido. Caso o parcelamento não esteja disponível para o cartão utilizado, o Stripe retornará um erro informado diretamente ao comprador.

## Personalizações

- **Valor do produto**: altere o valor fixo no arquivo `public/create-intent.php` (propriedade `amount`, em centavos) e ajuste os textos visíveis no `public/index.php`.
- **Opções de parcelamento**: modifique as opções do `<select>` em `public/index.php` conforme sua regra de negócio.
- **Moeda**: atualize a propriedade `currency` em `public/create-intent.php` e, se necessário, os textos exibidos para o usuário.

## Segurança

- Nunca exponha a chave secreta da sua conta Stripe no frontend.
- Utilize HTTPS em produção.
- Armazene logs sensíveis e erros de forma adequada. Neste exemplo, as mensagens de erro são retornadas em texto simples apenas para facilitar o desenvolvimento.

## Recursos adicionais

- [Documentação oficial do Stripe para Payment Intents](https://stripe.com/docs/payments/payment-intents)
- [Parcelamento com Stripe](https://stripe.com/docs/payments/installments)


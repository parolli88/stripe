(function () {
    const publishableKey = window.STRIPE_PUBLISHABLE_KEY;
    const statusMessage = document.getElementById('status-message');
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const installmentsSelect = document.getElementById('installments');

    if (!form || !statusMessage || !submitButton || !installmentsSelect) {
        return;
    }

    function setStatus(message, type) {
        statusMessage.textContent = message || '';
        statusMessage.classList.remove('success', 'error');
        if (type) {
            statusMessage.classList.add(type);
        }
    }

    if (!publishableKey) {
        setStatus('Configure STRIPE_PUBLISHABLE_KEY para inicializar o Stripe.js.', 'error');
        if (submitButton) {
            submitButton.disabled = true;
        }
        return;
    }

    const stripe = Stripe(publishableKey);
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        hidePostalCode: false,
    });
    cardElement.mount('#card-element');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setStatus('Processando pagamento, aguarde...', '');
        submitButton.disabled = true;

        try {
            const clientSecret = await createPaymentIntent();
            await confirmPayment(clientSecret);
            setStatus('Pagamento aprovado! Obrigado pela compra.', 'success');
        } catch (error) {
            console.error(error);
            setStatus(error.message || 'Não foi possível concluir o pagamento.', 'error');
        } finally {
            submitButton.disabled = false;
        }
    });

    async function createPaymentIntent() {
        const installments = parseInt(installmentsSelect.value, 10) || 1;
        const response = await fetch('/create-intent.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ installments })
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.error || 'Falha ao criar Payment Intent');
        }

        return data.clientSecret;
    }

    async function confirmPayment(clientSecret) {
        const installments = parseInt(installmentsSelect.value, 10) || 1;

        const options = {
            payment_method: {
                card: cardElement,
            },
        };

        if (installments > 1) {
            options.payment_method_options = {
                card: {
                    installments: {
                        plan: {
                            count: installments,
                            interval: 'month',
                            type: 'fixed_count',
                        },
                    },
                },
            };
        }

        const result = await stripe.confirmCardPayment(clientSecret, options);
        if (result.error) {
            throw new Error(result.error.message);
        }

        if (result.paymentIntent && result.paymentIntent.status !== 'succeeded' && result.paymentIntent.status !== 'requires_capture') {
            throw new Error('Status inesperado do pagamento: ' + result.paymentIntent.status);
        }
    }
})();

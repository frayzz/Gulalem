// resources/js/custom.js

document.addEventListener('DOMContentLoaded', function() {
    // Форматирование номера телефона
    const phoneInputs = document.querySelectorAll('.phone-input');
    if (phoneInputs.length > 0) {
        phoneInputs.forEach(function(phoneInput) {
            phoneInput.addEventListener('input', function() {
                let value = phoneInput.value.replace(/\D/g, '');
                if (value.length > 10) value = value.slice(0, 10);

                let formattedValue = '';
                if (value.length > 0) formattedValue += '(' + value.slice(0, 3);
                if (value.length >= 4) formattedValue += ') ' + value.slice(3, 6);
                if (value.length >= 7) formattedValue += '-' + value.slice(6, 8);
                if (value.length >= 9) formattedValue += '-' + value.slice(8, 10);

                phoneInput.value = formattedValue;
            });
        });
    }

    // Выборка времени доставки
    const deliveryTimesContainer = document.getElementById('delivery-times');
    if (deliveryTimesContainer) {
        const currentTime = new Date();
        const interval = 120; // Интервал в минутах (2 часа)
        const numberOfSlots = 3; // Количество временных интервалов

        // Добавляем опцию "Как можно быстрее" и делаем её выбранной по умолчанию
        const asapDiv = document.createElement('div');
        asapDiv.className = 'delivery-option selected';
        asapDiv.setAttribute('data-time', 'fast');
        asapDiv.textContent = 'Как можно быстрее';
        deliveryTimesContainer.appendChild(asapDiv);
        document.getElementById('selected-time').value = 'ASAP';

        for (let i = 1; i <= numberOfSlots; i++) {
            const startTime = new Date(currentTime.getTime() + i * interval * 60000);
            const endTime = new Date(startTime.getTime() + interval * 60000);
            const startTimeStr = startTime.toTimeString().substring(0, 5);
            const endTimeStr = endTime.toTimeString().substring(0, 5);

            const timeSlotDiv = document.createElement('div');
            timeSlotDiv.className = 'delivery-option';
            timeSlotDiv.setAttribute('data-time', `${startTimeStr}-${endTimeStr}`);
            timeSlotDiv.textContent = `${startTimeStr} - ${endTimeStr}`;
            deliveryTimesContainer.appendChild(timeSlotDiv);
        }

        // Добавляем опцию "Выбрать свое время"
        const customTimeDiv = document.createElement('div');
        customTimeDiv.className = 'delivery-option';
        customTimeDiv.setAttribute('data-time', 'CUSTOM');
        customTimeDiv.textContent = 'Выбрать свое время';
        deliveryTimesContainer.appendChild(customTimeDiv);
    }

    // Убедимся, что все элементы существуют перед добавлением обработчиков событий
    const deliveryOptions = document.querySelectorAll('.delivery-option');
    if (deliveryOptions.length > 0) {
        deliveryOptions.forEach(option => {
            option.addEventListener('click', function() {
                deliveryOptions.forEach(opt => opt.classList.remove('selected'));
                option.classList.add('selected');
                const selectedTime = option.getAttribute('data-time');
                document.getElementById('selected-time').value = selectedTime;
                if (selectedTime === 'CUSTOM') {
                    document.getElementById('custom-time-input').style.display = 'block';
                    setCustomTimeConstraints();
                } else {
                    document.getElementById('custom-time-input').style.display = 'none';
                }
            });
        });
    }

    // Показать поле ввода даты и времени, если была выбрана опция "CUSTOM"
    if (document.getElementById('selected-time') && document.getElementById('selected-time').value === 'CUSTOM') {
        document.getElementById('custom-time-input').style.display = 'block';
    }

    function setCustomTimeConstraints() {
        const customTimeInput = document.getElementById('custom-time');
        if (customTimeInput) {
            const currentTime = new Date();
            const minTime = new Date(currentTime.getTime() + 2 * 60 * 60 * 1000); // Через 2 часа

            // Форматируем дату и время для атрибута min
            const year = minTime.getFullYear();
            const month = String(minTime.getMonth() + 1).padStart(2, '0');
            const day = String(minTime.getDate()).padStart(2, '0');
            const hours = String(minTime.getHours()).padStart(2, '0');
            const minutes = String(minTime.getMinutes()).padStart(2, '0');

            const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
            customTimeInput.min = minDateTime;
        }
    }

    // Обработка отправки и проверки кода
    const sendCodeButton = document.getElementById('send-code-button');
    const resendCodeButton = document.getElementById('resend-code-button');
    const verificationForm = document.getElementById('verification-form');
    const orderForm = document.getElementById('order-form');
    const verifyCodeButton = document.getElementById('verify-code-button');

    if (orderForm) {
        // Предотвращаем автоматическую отправку формы
        orderForm.addEventListener('submit', function(event) {
            event.preventDefault();
        });
    }

    if (verificationForm) {
        verificationForm.addEventListener('submit', function(event) {
            event.preventDefault();
        });
    }

    if (sendCodeButton) {
        sendCodeButton.addEventListener('click', function() {
            sendVerificationCode();
        });
    }

    if (resendCodeButton) {
        resendCodeButton.addEventListener('click', function() {
            sendVerificationCode();
        });
    }

    function sendVerificationCode() {
        const formData = new FormData(orderForm);
        fetch('/send-verification-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    orderForm.style.display = 'none';
                    verificationForm.style.display = 'block';
                    sendCodeButton.style.display = 'none';
                    resendCodeButton.style.display = 'block';
                } else if (data.errors) {
                    alert('Ошибка: ' + data.errors.join(', '));
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }

    if (verifyCodeButton) {
        verifyCodeButton.addEventListener('click', function() {
            verifyCode();
        });
    }

    function verifyCode() {
        const formData = new FormData(verificationForm);
        formData.append('user_phone', document.querySelector('[name="user_phone"]').value);
        formData.append('recipient_phone', document.querySelector('[name="recipient_phone"]').value);
        formData.append('city', document.querySelector('[name="city"]').value);
        formData.append('region', document.querySelector('[name="region"]').value);
        formData.append('address', document.querySelector('[name="address"]').value);
        formData.append('selected_time', document.querySelector('[name="selected_time"]').value);
        formData.append('custom_time', document.querySelector('[name="custom_time"]').value);
        formData.append('card_message', document.querySelector('[name="card_message"]').value);
        formData.append('payment_method', document.querySelector('[name="payment_method"]').value);
        formData.append('product_id', document.querySelector('[name="product_id"]').value);
        fetch('/verify-code', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.message && data.message.includes('успешно')) {
                    document.getElementById('success-text').innerText = data.message;
                    document.getElementById('success-message').style.display = 'flex';
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 5000); // Перенаправление через 5 секунд
                } else if (data.errors) {
                    alert('Ошибка: ' + data.errors.join(', '));
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }
});


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Расчеты и изображение</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            width: 100%;
            height: auto;
        }

        #container {
            position: relative;
        }

        #coin-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2; /* Монеты будут выше свиньи */
        }

        .coin {
            width: 70px; /* Замените на ширину вашей монеты */
            height: 70px; /* Замените на высоту вашей монеты */
            position: absolute;
        }

        #piggyBankImage {
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translate(-50%, 80%);
            z-index: 1; /* Свинья будет ниже монет */
        }
    </style>

</head>
<body>

<div id="container">
    <img id="piggyBankImage" src="piggy_bank_image.png" alt="Piggy Bank Image" />
    <div id="coin-container"></div>
</div>
    
    <script>
        // Функция для создания случайного числа в заданном диапазоне
        function getRandomInt(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        // Анимация монет, которые "сыпятся" до половины экрана
        function animateCoins() {
            var coinContainer = document.getElementById('coin-container');

            for (var i = 0; i < 10; i++) {
                var coin = document.createElement('img');
                coin.src = 'coin.png';
                coin.className = 'coin';

                // Установка начальной позиции в центр экрана
                coin.style.left = '50%';
                coin.style.top = '0';

                coinContainer.appendChild(coin);
                     // Определите, на какую высоту вы хотите, чтобы монеты падали (например, половина высоты экрана)
                    var targetHeight = window.innerHeight / 2.5;

                anime({
                    targets: coin,
                    translateY: targetHeight + getRandomInt(50, 100), // куда монета двигается (высота экрана + 100)
                    duration: 5000, // длительность анимации в миллисекундах
                    easing: 'easeInOutQuad', // функция анимации
                    delay: i * 500, // задержка для создания эффекта поочередного падения
                    complete: function(anim) {
                        // Удаляем монету после завершения анимации
                        coinContainer.removeChild(anim.animatables[0].target);
                    }
                });
            }
        }

        function startCoinAnimation() {
    // Вызывать animateCoins сразу и устанавливать интервал
    animateCoins();

    // Запускать animateCoins каждые 5 секунд
    setInterval(animateCoins, 5000);
}

        // Вызываем функцию для анимации монет при загрузке страницы
        document.addEventListener('DOMContentLoaded', animateCoins);

        function reloadPage() {
         location.reload(true);
}
        // Обновление страницы каждые 10 секунд
        setInterval(reloadPage, 10000);

        function fetchData() {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var data = new Uint8Array(xhr.response);
                    var workbook = XLSX.read(data, { type: 'array' });

                    // Используем библиотеку xlsx для обработки данных на стороне клиента
                    var sheet_name_list = workbook.SheetNames;
                    var sheet = workbook.Sheets[sheet_name_list[0]];
                    var data_json = XLSX.utils.sheet_to_json(sheet, { header: 1 });

                    // Обработка данных
                    var depositSum = 0;
                    var withdrawalSum = 0;

                    for (var i = 1; i < data_json.length; i++) {
                        if (data_json[i][4] === 'Deposit' && data_json[i][13] === 'Crypto') {
                            depositSum += parseFloat(data_json[i][19]);
                        } else if (data_json[i][4] === 'Withdrawal' && data_json[i][13] === 'Crypto') {
                            withdrawalSum += parseFloat(data_json[i][19]);
                        }
                    }

                    // Вычисление разницы между суммой Deposit и Withdrawal
                    var result = depositSum - withdrawalSum;

                    // Округление суммы до целого числа и добавление знака доллара
                    var roundedResult = Math.round(result);
                    var formattedResult = '$' + roundedResult.toLocaleString();

                    // Отображение суммы на изображении
                    var piggyBankImage = document.getElementById('piggyBankImage');
                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');

                    canvas.width = piggyBankImage.width;
                    canvas.height = piggyBankImage.height;

                    context.drawImage(piggyBankImage, 0, 0);

                    // Отображение цели (например, 900,000$)
                    var goalText = 'TARGET: 900,000$';
                    context.font = '20px Arial';
                    context.fillStyle = 'grey';
                    context.textAlign = 'center';
                    context.fillText(goalText, canvas.width / 2, canvas.height / 2 + 40);
                    
                    context.font = '80px Arial';
                    context.fillStyle = 'green';
                    context.textAlign = 'center';
                    context.fillText(formattedResult, canvas.width / 2, canvas.height / 2);

                    piggyBankImage.src = canvas.toDataURL();
                }
            };
            
            // Здесь "netCrypto.xlsx" - имя вашего файла
            xhr.open('GET', 'netCrypto.xlsx', true);
            xhr.responseType = 'arraybuffer';
            xhr.send();
        }

        // Вызывайте функцию для получения данных при загрузке страницы или по необходимости
        fetchData();
    </script>

</body>
</html>

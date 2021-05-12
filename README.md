# shifr-php
<p>ENG</p>
<p>
Symmetric stream encryption with 'salt'.<br>
The 'salt' is constantly being produced, which gives good encryption strength.<br>
Data size doubles in binary mode.<br>
Will triple in text mode.<br>
There is no diagnosis of the wrong password.<br>
The encryption key size is 296 bits ( 45 - 90 letters ).<br>
Or light key with 45 bits ( 6 - 14 lettters ).<br>
The PHP <i>(server's side)</i> code compatible with C.<br>
The JavaScript <i>(user's side)</i> code is also compatible.<br>
<br>
Password encrypted × password = can serve as a hash function.<br>
Hash ÷ decrypt (password) == password <br>
If the decrypted hash with the password gives this password, then the password is correct.<br>

Double encryption of known data with a password can serve as a signature. <br>
For example Sha1Sum(data) × password × password = Signature <br>
If the signature decrypted twice gives the checksum, then this gives reason to trust the signed data.<br>

Double data decryption can also serve as a signature.<br>
Data ÷ decryption ÷ decryption = signature<br>
The data is decrypted twice and verified with the signature.<br>
</p>
<p>RUS</p>
<p>
Симметричное потоковое шифрование с помощью 'соли'.<br>
'Соль' постоянно вырабатывается, что даёт хорошую стойкость шифрования.<br>
Размер данных удваивается в двоичном режиме.<br>
Утроится в текстовом режиме.<br>
Нет никакого диагноза неправильного пароля.<br>
Размер ключа шифрования составляет 296 бит ( 45-90 букв ).<br>
Или лёгкий ключ с 45 битами ( 6 - 14 букв ).<br>
PHP <i>(серверная часть)</i> кода, совместимый с C.<br>
Код JavaScript <i>(на стороне пользователя)</i> также совместима.<br>
<br>
Пароль зашированный × паролем = может служить как хеш функция.<br>
Хеш ÷ расшифровать (паролем) == пароль<br>
Если расшифрованный хеш с паролем даёт тот-же пароль, то пароль правильный.<br>

Двойное шифрование известных данных паролем может служить подписью.<br>
Например Sha1Sum(данные) × пароль × пароль = Подпись<br>
Если подпись расшифрованная два раза даёт контрольную сумму, то это даёт <br>
повод доверять подписаным данным.<br>

Двойная расшифровка данных тоже может служить подписью.<br>
Данные ÷ расшифр ÷ расшифр = подпись<br>
Данные два раза расшифровываются и сверяются с подписью.<br>
</p>
<img src="github.jpg" alt="PrtScr">

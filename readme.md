Тестовое Laravel + Laraadmin:

1. создаем модули для хранения курсов валют, прогружаем сразу историю за 14 прошлых дней в БД ( вот апи: https://bank.gov.ua/control/uk/publish/article?art_id=38441973#exchange ) берем за базовую USD. так же должен быть модуль для добавления валют, для которых будут показаны курсы(например EUR, GBP), нужно завести 3-4, но предусмотреть чтобы вся система работала при добавлении дополнительных

2. главная страница должна содержать график, по умолчанию конвертация 1й из модуля валют. вот пример страницы: http://prntscr.com/la98q1 
слева где перечислены валюты 
график по умолчанию за 3дня последних. на примере есть попап окно с возможными вариантами выбора, делаем похожее, с вариантами за 5 дней, 7, 10, за все время

3. данные должны добавляться в БД по текущему дню при первом заходе на страницу, если за текущую дату еще не вносили , то вносим.  

# Тестовое задание на основе Docker и CodeIgniter для Fullstack разработчика

## Первоначальная настройка

-   Устанавливаем Docker c [официального сайта](https://www.docker.com/products/docker-desktop) и [Docker Compose](https://docs.docker.com/compose/install/);
-   Для пользователей Windows дополнительно необходимо установить виртуальное ядро Linux, следуя данной [инструкции](https://docs.docker.com/desktop/install/windows-install/);
-   Собираем контейнер командой в папке проекта `docker-compose up -d`;
-   Инициализируем сервер:
    -   при запущенном контейнере в папке проекта запускаем команду `docker-compose exec web bash`;
    -   запускаем сборку `composer install`.

## Ошибка докера

В случае ошибки такого вида:

    Error response from daemon: pull access denied for nginx, repository does not exist or may require 'docker login': denied:
    <html>
    <body>
    <h1>403 Forbidden</h1>
    Since Docker is a US company, we must comply with US export control regulations.
    In an effort to comply with these, we now block all IP addresses that are located in Cuba, Iran, North Korea, Republic of Crimea, Sudan, and Syria.
    If you are not in one of these cities, countries, or regions and are blocked, please reach out to https://hub.docker.com/support/contact/
    </body>
    </html>

Нужно использовать прокси сервер

### Как его подключить

1. через конфиг докера (как зеркало docker.io)

    конфиг расположен в

    | операционная система     |          путь к файлу конфигурации          |
    | ------------------------ | :-----------------------------------------: |
    | Linux, обычная установка |           /etc/docker/daemon.json           |
    | Linux, режим rootless    |        ~/.config/docker/daemon.json         |
    | Windows                  |  C:\ProgramData\docker\config\daemon.json   |
    | Windows с Docker Desktop | C:\Users\<Пользователь>\.docker\daemon.json |

    конфиг:
    { "registry-mirrors" : [ "https://dockerhub.timeweb.cloud" ] }

    чтобы конфиг применился потребуется перезапустить конфигурацию:
    systemctl reload docker


    теперь при попытке загрузки образа, Docker будет сначала пытаться использовать прокси.

1. явное указание адреса

    ```
    docker pull dockerhub.timeweb.cloud/library/alpine:latest

    docker pull dockerhub.timeweb.cloud/openresty/openresty:latest
    ```

## Описание записи

-   name -  почта создателя;
-   text - Текст комментария;
-   date - Дата создания комментария в строковом формате(выбирается создателем).

## Стек

- PHP 7.4;
- MYSQL 8;
- CodeIgniter 4;
- jQuery 3;
- Bootstrap 4.

## Задание

Создать сайт со списком комментариев.
Форма с добавлением комментариевдолжна располагаться под уже добавленными комментариями.

Требования к разработке:

-   добавление и удаление комментариев (желательно, без перезагрузки страницы);
-   постраничный просмотр комментариев (3 комментария на страницу c возможностью выбора конкретной);
-   сортировка по:
    -   id;
    -   дате добавления;
-   направления сортировки:
    -   по возрастанию;
    -   по убыванию;
-   использование валидации почты при вводе для пользователя (с отображением ошибки), а также на сервере;
-   использование адаптивной верстки;
-   использование jQuery.
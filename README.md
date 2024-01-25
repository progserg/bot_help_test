## Реализация асинхронной обработки сообщений очереди.
### Задача
Есть веб-api, непрерывно принимающее события (ограничимся 10000 событий) для группы аккаунтов (1000 аккаунтов) и складывающее их в очередь.
Каждое событие связано с определенным аккаунтом и важно, чтобы события аккаунта обрабатывались в том же порядке, в котором поступили в очередь. Обработка события занимает 1 секунду (эмулировать с помощью sleep).
Сделать обработку очереди событий максимально быстрой на данной конкретной машине.
Код писать на PHP. Можно использовать фреймворки и инструменты такие как RabbitMQ, Redis, MySQL и т. д.

### Решение
#### Инструменты
PHP 8.2
Symfony 6
Apache Kafka
Apache ZooKeeper
Mysql 8
Docker
Docker-Compose

#### Подход
Эмулируем входящие сообщения с помощью команды LoadGeneratorCommand. Каждый пользователь может получить случайное количество сообщений.
В данном примере само сообщение является "тегом" для понимания порядка обработки сообщения.
Во время генерирования сообщений, они записываются в таблицу user_message, для возможности в дальнейшем проверить, сколько каким пользователям приходило сообщений, когда они пришли(поле generated_at) и когда были обработаны(поле processed_at).
А также сообщение(src/Messenger/Message/UserMessage) отправляется в очередь.
Обработчик очереди UserMessageHandler пытается получить блокировку по id пользователя, если ему это удается, пытается найти в таблице user_message запись с processed_at = NULL, причем только одну и в порядке добавления в таблицу самую первую.
Если такая запись есть, и номер сообщения в поле message не меньше пришедшего в сообщении на обработку - записывает дату обработки в таблицу.
Если описанные выше условия не выполнились, то берется следующее сообщение из очереди.
Проверить результат можно, например, запросом: select * from user_message order by user_id, message limit 50;

## Важно!
Это не решение для прода. Многие спорные моменты, например, падение консьюмеров, очереди и всякое прочее, скорее всего не обработаны.

## Требования
* git
* docker
* docker-compose
* make

## kafka-ui http://localhost:8080/
## посмотреть результаты работы http://localhost:8000/index/result

## Действия
### Склонируйте проект
```bash
git clone https://github.com/progserg/bot_help_test.git
```
### Установка
Эта команда создаст все службы и создаст топик kafka
```bash
make install-local
```
Если скрипт падает не выполнив миграции и не создав топика, необходимо выполнить следующие команды вручную:
```bash
make console doctrine:migrations:migrate
make topic-create user_message_topic
```

### Запуск генерирования сообщений
#### Producer
```shell
make console app:messenger:load_generator
```
Эта команда сгенерирует сообщения в топик кафки и запишет в таблицу user_message<br>
Топик объявлен в `config/packages/messenger.yaml` `producer_topic`
```yaml
framework:
    messenger:
        transports:
          user_message_transport:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    producer_topic: 'user_message_topic'
```

#### Consumer
```shell
make console messenger:consume user_message_transport
```
`user_message_transport` это транспорт объявленный в `config/packages/messenger.yaml`<br>
Для запуска нескольких обработчиков, необходимо передать команде console messenger:consume соответствующие названия транспорта(user_message_transport_1, user_message_transport_2...)
Эта команда будет извлекать сообщения из топика kafka, определенной в `config/packages/messenger.yaml` `consumer_topics`<br>
```yaml
framework:
    messenger:
        transports:
          user_message_transport:
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%'
                options:
                    consumer_topics:
                        - 'user_message_topic'
```
Сообщения должны обрабатываться `App\Messenger\Handler\UserMessageHandler`<br>
В данном примере, обработчик пытается убедиться должен ли именно он обработать данное сообщение, и если да, то записывает время обработки в user_message.

## References
* https://github.com/symfony-examples/messenger-kafka

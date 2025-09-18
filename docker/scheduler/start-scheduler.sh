#!/bin/sh

# Запуск cron daemon
crond -f -d 8

# Tail лог файла, чтобы контейнер не завершился
tail -f /var/log/cron.log

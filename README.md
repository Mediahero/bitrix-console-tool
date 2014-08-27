bitrix-console-tool
===================

Bitrix CMF Console Tool

Поддерживает следующие команды:

* `components:list` - получать список всех компонентов текущего сайта (в каталоге которого запускается команда)
* `generate:include` - генерирует код включения компонента (`$APPLICATION->IncludeComponent(...)`) 
* `templates:copy` - копирует шаблон указанного в параметрах компонента в папку указанного шаблона сайта
* `templates:list` - выводит список шаблонов указанного компонента
* `iblock:types` - выводит список типов инфоблоков
* `iblock:lists` - выводит список инфоблоков указанного типа
* `web-root` - выводит путь к корю публичной части сайта

Список всех доступных команд можно посмотреть, запустив скрипт `bxc` без указания аргументов.

Чтобы узнать, какие опции поддерживает та или иная команда скрипта, нужно выполнить эту команду с опцией `--help`:

````
$ bxc components:list --help
Usage:
 components:list [-b|--bitrix] [-l|--local]

Options:
 --bitrix (-b)         Show only core components (from bitrix folder)
 --local (-l)          Show only local components (from local folder)
 --help (-h)           Display this help message.
 --quiet (-q)          Do not output any message.
 --verbose (-v|vv|vvv) Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
 --version (-V)        Display this application version.
 --ansi                Force ANSI output.
 --no-ansi             Disable ANSI output.
 --no-interaction (-n) Do not ask any interactive question.
 ````

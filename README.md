# Генератор объявлений для Yandex.Direct

[![Build Status](https://travis-ci.org/Doka-NT/direct-generator.svg?branch=master)](https://travis-ci.org/Doka-NT/direct-generator)
[![Coveralls](https://coveralls.io/repos/github/Doka-NT/direct-generator/badge.svg?branch=master)](https://coveralls.io/github/Doka-NT/direct-generator?branch=master)
[![CodeCov](https://codecov.io/gh/Doka-NT/direct-generator/branch/master/graph/badge.svg)](https://codecov.io/gh/Doka-NT/direct-generator)

## Общая информация

DirectGenerator - это утилита командной строки, которая позволяет сгенерировать сотни и тысячи объявлений
для Яндекс.Директ. 

Программа распознает специальный файл с раширением `.dg` и формирует все возможные комбинации заголовков
и текстов объявлений. Все что Вам нужно - иметь набор ключевых слов, основные варианты заголовков и текстов.

## Установка

### Системные требования

- php7.1
- php7.1-mbstring
- php7.1-curl

### Процесс установки

1. Установить composer следуя [инструкции на сайте](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)
2. Выполнить команду
```bash
composer create-project skobka/direct-generator
# или следующую (в зависимости от того как установлен composer)
php composer.phar create-project skobka/direct-generator
```
3. Перейти в папку с проектом:

```bash
cd direct-generator
```

## Пример

Для генерации объявлений необходимо сформировать исходные данные:
- ключевые слова
- заголовки
- тексты

Данные формируются в виде `.dg` файла.
Пример такого файла можно найти [здесь](https://github.com/Doka-NT/direct-generator/blob/master/var/example/example.dg)

После того как файл сформирован, пора запускать генерацию:

```bash
./direct-generator var/example/example.dg example.csv
```

После выполнения сформируется файл `example.csv`, в котором будут содержаться ключевые слова и тексты объявлений

### Опции

| Опция                | Описание                                                                                       |
| -------------------- | ---------------------------------------------------------------------------------------------- |
| `-s`, `--skip-long`  | Пропускать длинные заголовки. Если указана, то длинные заголовки и тексты будут игнорироваться |

## Обратная связь

Все вопросы и пожелания по проекту направляйте в ввиде [нового issue](https://github.com/Doka-NT/direct-generator/issues/new) в проекте.
 
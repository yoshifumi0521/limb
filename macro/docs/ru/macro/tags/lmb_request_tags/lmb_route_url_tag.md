# Тег {{route_url}}
## Описание
Тег {{route_url}} формирует и выводит url, определенный при помощи класса [lmbRoutes](../../../../../../web_app/docs/ru/web_app/lmb_routes.md). Строка формируется из так называемого «маршрута», который можно указать при помощи атрибута route (опционально).

## Синтаксис

    {{route_url [route="route_name"] params='param1:value1,param2:value2...' [skip_controller='true']}}

## Область применения
В любом месте MACRO-шаблона.

## Атрибуты
* **route** (опционально) — указывает название марштура (route), который будет использоваться классом lmbRoutes для формирования строки.
* **params** — дополнительные параметры, которые необходимы, чтобы сформировать правильный url. Несколько параметров разделяются запятыми, между названием и значением параметра ставится двоеточине: id:{$id},action:edit
* **skip_controller** — позволяет отменить автоматическое добавление имени текущего контроллера в список параметров маршрута.

## Содержимое
Нет.

## Пример использования

    {{list:list using='$sections'}}
    <ul>
      {{list:item}}
        <li><a href='{{route_url route="catalog" params="locale_id:{$#request.locale_id},id:{$id},action:companies" skip_controller="true"/}}'>{$title}</a></li>
      {{/list:item}}
    </ul>
    {{/list:list}}

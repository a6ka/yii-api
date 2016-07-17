Реализация RESTfull API
============================

Задачи:
-------------------
1. Листинг новостей
2. Авторизация пользователя

Описание реализации
-------------------
#Листинг

Воспользовался стандартными инструментами Yii2 - классом yii\rest\ActiveController.

Так как задача только вычитывать новости, то отключены возможность Создания, Обновления, удаления.

Данная настройка делается в конфиге (web.pbp) при настройке urlManager:
```php
['class' => 'yii\rest\UrlRule', 'controller' => 'news', 'except' => ['delete', 'create', 'update','options'],],
```

Какие файлы изменялись:
-------------------

      config/web.php            настройка urlManager 
      controllers/              
       - AuthController.php     Контроллер авторизации
       - NewsController.php     Контроллер новостей
      models/             
       - User.php               Модель пользователей (вычитка с БД, авторизация)
       - News.php               Модель новостей
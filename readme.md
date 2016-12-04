# RAGNAROKZ III

RagnarokZ is a web application that build on top of Laravel, it scrap manga and store image locally.

For now, managing manga is either via API or console.

## Requirements

* PHP 7.0 (minimum >= 5.6.4)
* Nginx
* MySql Server
* Memcached (optional)
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension

More server requirements you can look at Laravel installation documentation here [Laravel Server Requirements](https://laravel.com/docs/5.3#server-requirements).

## Getting Started

* Create a new .env file from example .env.example
* set ENABLE_REGISTER to true
* Go to home page (path "/") and you'll be prompt to login or register
* Register, login and set ENABLE_REGISTER to false back

## Manage Manga

All manga managing is done via console. (API documentation coming soon)

### Add Manga

```python
# manga:add <manga_url>
php artisan manga:add http://mangafox.me/manga/onepunch_man/
```

### Scrap Manga

Just run this command, and you will be prompt to choose manga that already added.

```python
php artisan manga:scrap

+----+--------------------------------------+
| ID | Manga                                |
+----+--------------------------------------+
| 1  | Onepunch-Man                         |
| 4  | History's Strongest Disciple Kenichi |
| 6  | Tales of Demons and Gods             |
| 7  | The Gamer                            |
| 8  | Boku no Hero Academia                |
+----+--------------------------------------+

 Choose your manga? [1, 4, 6, 7, 8]:
 >
```

## Upcoming Feature

Please create an issue and insert what feature you want or interested in the future!

* Inf8 Mode - Automatically switch to next chapter when reach the bottom of the page (Beta Testing)
* Manage manga via web (GUIFTW!)
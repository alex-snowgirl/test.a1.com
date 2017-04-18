Test REST (almost) API project for a1 Company
A lot of TODOs present, due to Easter, sorry.
Fully written by me (except jquery.js, fonts, Loading img and PHPUnit).

-PHP-
@todo PHP7.1
@todo tests
@todo async requests
@todo transactions
@todo cache
@todo docker file
@todo Config file
Main Classes:
1) CORE\Api\App - core API Application class
2) APP\Api\App - extended version of core API App class, - custom security layer added
@todo check updated values, such as level increment
3) CORE\Api\Entity - core Entity class, represent request entity (part of Command design pattern)
(child classes: APP\Api\Entity\User, APP\Api\Entity\Hero, APP\Api\Entity\Game)
4) CORE\Api\Action - core API Action class, represent request method (part of Command design pattern)
(child classes: CORE\Api\Action\Get, CORE\Api\Action\Post, CORE\Api\Action\Put, CORE\Api\Action\Patch, CORE\Api\Action\Delete)
5) CORE\Storage\Session - session storage

-JavaScript-
Main File:
1) pub/app.js - fully responsible for the Client application
@todo pre-processors, minify
@todo split into separate classes (entity, storage, fight strategy, skill strategy, view etc.)
@todo add error handlers
@todo remove code duplicates if exists
@todo implement promises
@todo cache heroes
@todo mounted Fight strategies

-CSS-
1) pub/app.css - fully responsible for the Client look and feel
@todo pre-processors, minify
@todo split into separate files page and app styles

-Game-
1) This is Fights simulator
2) Enter your name
3) Pick your hero
4) Choose your opponent
5) Fight is running... (5 rounds; Lose, Dead Heat or Won)
6) Save game
7) Resume game

Dependencies:
1) PHP 5.6
2) php json ext
3) composer

Usage:
1) cd /path/to/project
2) composer install
3) cd pub
4) php -S 0.0.0.0:8080
5) open in your fav browser - http://0.0.0.0:8080/web.php
6) enjoy

Total time spent in general: 10 hours.



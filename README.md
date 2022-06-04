# Symfony video app

Based on Udemy course [Symfony Web Development Complete Guide: Beginner To Advanced](https://www.udemy.com/course/symfony-4-web-development-from-beginner-to-advanced)

## Requirements

- PHP 8 (or later)
- Composer
- Web server environment
  - Xampp, Lampp or similar (or use Docker)

## Installation (docker)

- `git clone https://github.com/chsjr1996/symfony-video-app.git`
- `cd symfony-video-app`
- `composer install`
- `docker-compose up -d`
- `./docker-helpers.sh --rebuild-db --re-migrate`
- Now access the [http://localhost](http://localhost) url on the browser

## Notes

On this course the Symfony 4 was used, but here I'm using the Symfony 6 and PHP 8.

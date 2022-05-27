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
- Now access the [http://localhost](http://localhost) url on the browser

## Notes

On this course the Symfony 4 was used, but here I'm using the Symfony 6 (and PHP 8 to use the awesome attributes syntax). I will made some changes too, like improve the code and frontend design.

## Under development

### TODO

- [ ] Try to apply more concepts of S.O.L.I.D.
- [ ] Replace all DI final class by Interfaces
- [ ] Make code more clean (DRY)
- [ ] Add CI on this repository
- [ ] Verify all twig template files and apply [Coding Standards](https://twig.symfony.com/doc/3.x/coding_standards.html)
- [ ] Be careful with twig template files names (more accurate)
- [ ] Verify all tests, add more if needed, try to improve the coverage.
- [ ] ...
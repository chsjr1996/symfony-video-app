# !/bin/sh

function clear_database() {
  read -p "Are you sure? [y/n]: " -n 1 -r

  if [ "$REPLY" != "y" ]; then
    echo
    echo "Canceled!"
    exit
  fi

  echo
  echo "Cleaning database"
  docker-compose exec php ./bin/console doctrine:schema:drop -n -q --force --full-database

  echo "Cleaning migrations"
  rm -rf migrations/*.php

  echo "Creating migrations"
  docker-compose exec php ./bin/console make:migration -n -q

  echo "Running migrations"
  docker-compose exec php ./bin/console doctrine:migrations:migrate -n -q

  echo "Running fixtures"
  docker-compose exec php ./bin/console doctrine:fixtures:load -n -q
}


case $1 in
    --rebuild-db)
        clear_database
        ;;
    --console)
        docker-compose exec php ./bin/console $2
        ;;
    --tests)
        docker-compose exec php ./bin/phpunit $2
        ;;
    --exec)
        docker-compose exec php $2
        ;;
    *)
        echo "Options are: '--rebuild-db', '--console', '--tests', '--exec'"
        ;;
esac


# !/bin/sh

function clear_database() {
  read -p "Are you sure? [y/n]: " -n 1 -r

  if [ "$REPLY" != "y" ]; then
    echo
    echo "Canceled!"
    exit
  fi

  echo

  consoleEnv=""
  if [[ "$2" = "tests" || "$3" = "tests" ]]; then
    consoleEnv="--env=test"
    echo "Test DB!!!"
    echo
  fi

  echo "Cleaning database"
  docker-compose exec php ./bin/console $consoleEnv doctrine:schema:drop --force --full-database

  if [[ "$2" = "--re-migrate" || "$3" = "--re-migrate" ]]; then
    echo "Cleaning migrations"
    rm -rf migrations/*.php

    echo "Creating migrations"
    docker-compose exec php ./bin/console $consoleEnv make:migration
  fi

  echo "Running migrations"
  docker-compose exec php ./bin/console $consoleEnv doctrine:migrations:migrate

  echo "Running fixtures"
  docker-compose exec php ./bin/console $consoleEnv doctrine:fixtures:load
}


case $1 in
    --rebuild-db)
        clear_database $@
        ;;
    --console)
        docker-compose exec php ./bin/console $2 $3
        ;;
    --tests)
        docker-compose exec php ./bin/phpunit $2 $3
        ;;
    --testdox)
        docker-compose exec php ./bin/phpunit --testdox --stop-on-failure $2
        ;;
    --exec)
        docker-compose exec php $@
        ;;
    *)
        echo "Options are: '--rebuild-db', '--console', '--tests', '--testdox', '--exec'"
        ;;
esac


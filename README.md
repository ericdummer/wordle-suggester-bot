# Worlde Game and Suggester
### REQUIREMENTS
* You must have docker installed and running (Docker Desktop works)
* To run the commands below you need to be on a linux based machine or Mac

## Command
The game

```./play.sh```

The suggester

```./suggester.sh```


### Docker stuff
To launch the container:

```docker compose up -d --build```

To rebuild it after a Docker change:

```docker compose up -d --build --remove-orphans```

To connect to the container (using bash)

```docker exec -it wordle-suggester-bot-core-1 /bin/bash```

---
Docker command to play:

```docker exec -it wordle-suggester-bot-core-1 composer play```

Docker command to use the suggester:

```docker exec -it wordle-suggester-bot-core-1 composer suggester```


## tests
To run tests (when connected to the container)

```composer install``` (first time only)

```composer test```

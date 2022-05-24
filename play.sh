#!/bin/bash
if [ ! "$(docker ps -q -f name=wordle-suggester-bot-core-1)" ]; then
    if [ "$(docker ps -aq -f status=exited -f name=wordle-suggester-bot-core-1)" ]; then
        # cleanup
        docker rm wordle-suggester-bot-core-1
    fi
    # run your container
    docker compose up -d --build
fi
docker exec -it wordle-suggester-bot-core-1 composer play

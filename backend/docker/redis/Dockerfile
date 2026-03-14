FROM redis:latest

ARG REDIS_PORT=6380
ENV REDIS_PORT=${REDIS_PORT}

COPY redis.conf /redis.conf

RUN mkdir -p /usr/local/etc/redis
COPY redis.conf /usr/local/etc/redis/redis.conf

VOLUME /data

ENTRYPOINT redis-server /usr/local/etc/redis/redis.conf --port $REDIS_PORT

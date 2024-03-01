FROM alpine:3.15

WORKDIR /app
COPY bin/ bin/
COPY docker/entrypoint.sh entrypoint.sh
EXPOSE 2137
ENTRYPOINT /app/entrypoint.sh

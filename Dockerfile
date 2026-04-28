FROM postgres:16-alpine

# Install additional PostgreSQL extensions and tools
RUN apk add --no-cache \
    postgresql-contrib \
    postgresql-client

# Expose PostgreSQL port
EXPOSE 5432

# PostgreSQL will run with the default entrypoint
CMD ["postgres"]

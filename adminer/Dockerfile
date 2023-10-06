FROM adminer:latest

USER root

# Store PHP Configs at /usr/local/etc/php/conf.d
#RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/upload_large_dumps.ini \
#    && echo "post_max_size = 50M"       >> /usr/local/etc/php/conf.d/upload_large_dumps.ini \
#    && echo "memory_limit = 512M"           >> /usr/local/etc/php/conf.d/upload_large_dumps.ini \
#    && echo "max_execution_time = 0"      >> /usr/local/etc/php/conf.d/upload_large_dumps.ini

# Add volume for sessions to allow session persistence
VOLUME /sessions

USER adminer

# We expose Adminer on port 8080 (Adminer's default)
EXPOSE 8080
FROM nginxinc/nginx-unprivileged:stable-alpine 

USER root
RUN apk add --no-cache shadow openssl bash tzdata curl &&\
    rm -rf /etc/nginx/conf.d/default.conf &&\
    usermod -u 101 nginx
    
RUN cp /usr/share/zoneinfo/Europe/Moscow /etc/localtime
RUN echo "Europe/Moscow" >  /etc/timezone

ADD /.docker/nginx/nginx.conf  /etc/nginx/
ADD /.docker/nginx/common.conf /etc/nginx/conf.d/
ADD /.docker/nginx/server-prod.conf /etc/nginx/conf.d/server-prod.conf

COPY --chown=nginx /web/  /var/www/html/web/
RUN rm -rf /var/www/html/web/*.php

COPY --chown=nginx /.docker/php/wait-for.sh /var/run/wait-for.sh
RUN chmod +x /var/run/wait-for.sh

USER 101
CMD nginx

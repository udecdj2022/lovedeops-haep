FROM phpmyadmin:apache
copy ./000-default.conf /etc/apache2/sites-available/000-default.conf
RUN service apache2 restart
EXPOSE 80


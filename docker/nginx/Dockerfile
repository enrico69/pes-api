# Use the default image. Do not use like those guys who use a Linux distro then install nginx
FROM nginx

# Install nano because using vi is like using a donkey at the age of intergalactic travel
RUN apt-get update && apt-get -q -y install nano

# Add the configuration file of my Symfony project
#ADD symfony.conf /etc/nginx/sites-available/
#RUN mkdir /etc/nginx/sites-enabled
#RUN ln -s /etc/nginx/sites-available/symfony.conf /etc/nginx/sites-enabled/symfony.conf

# Remove the default nginx file to be sure that our configuration (websites) are loaded
# If not: you will always get the Nginx default page
RUN rm -f /etc/nginx/nginx.conf
ADD nginx.conf /etc/nginx/

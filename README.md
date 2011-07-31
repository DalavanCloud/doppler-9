Doppler
=======

A most basic doppler implementation that requires human oversight.

Setup
-----

### Server-Side

The folder `xyzroot` will be put on the server side and you have to change the
`config.php` file and add the doppler front-end url from where you will run the
test. For instance, `http://doppler.example.com`.

Also you have to configure your web server with re-rewrite rules:

    <VirtualHost *>
      ServerName xyz.example.com
      DocumentRoot /path/to/doppler/xyzroot

      RewriteEngine on
      RewriteRule ^(.*)$  /index.php?__path__=$1  [L,QSA]
    </VirtualHost>


### Client-Side

Since you have configured you server now you need to configure the front-end
to point to the desired server. This has to be setup on the `config.php` file
on you client-side doppler under the folder `webroot`, and add the target server
i.e.: `xyz.example.com`.

Usage
-----

By now you can run two test:

  * Lorem Ipsum: get a blob of 1k of test from the server.
  * Image test: get the content of an image ~1.4k.


Cristian Adamo.


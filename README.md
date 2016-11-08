# New [ShortEdge](https://edge.shortcord.com)

My origin edge server's `public_html`

### Getting a copy working
Firstly, you should have PHP7 and a webserver running, setting up said web server for Slim is covered in Slim's [docs](http://www.slimframework.com/docs/start/web-servers.html)

You should install [Composer](https://getcomposer.org/), I use that to handle all the dependencies

It uses [Slim](www.slimframework.com) and [Twig-View](https://github.com/slimphp/Twig-View)

Once you clone this repo, you need to install some software on your box, mainly [lm_sensors](https://wiki.archlinux.org/index.php/lm_sensors)

Then you need to copy the scripts from `bin` to what ever your web data user's home folder is.

_For example, my `nginx` and `php7-cgi` processes are both owned by `www-data` whos home is `/var/www/`_

_so I would copy the contents of `bin` to `/var/www/` using `cp bin/* /var/www/`_

It _should_ work after all that, otherwise mess with the source.
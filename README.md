# New [ShortEdge](https://edge.shortcord.com)

My origin edge server's `public_html`

### Getting a copy working
Firstly, you should have PHP7 and a webserver running, setting up said web server for Slim is covered in Slim's [docs](http://www.slimframework.com/docs/start/web-servers.html)

You should install [Composer](https://getcomposer.org/), I use that to handle all the dependencies

It uses [Slim](www.slimframework.com) and [Twig-View](https://github.com/slimphp/Twig-View)

Once you clone this repo, you need to install some software on your box, mainly [lm_sensors](https://wiki.archlinux.org/index.php/lm_sensors)

Take a look at [sensorBuild.sh](../blob/master/bin/sensorBuild.sh) to customize the `lm_sensors` polling. Otherwise polling will fail.

It _should_ work after all that, otherwise mess with the source.
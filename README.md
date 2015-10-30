PerformerVagrantBundle
====

Symfony bundle -> Contain vagrant commands + Symfony and Doctrine Commands executed directly in "vagrant ssh --command 'bin/console some:command' "

---------------------

### Composer

Enable the Bundle in AppKernel.php

```json
"require": {
        ...
        "performer/vagrant": "dev-master"
    }
```

```
// app/AppKernel.php
// use ...

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Performer\VagrantBundle\PerformerVagrantBundle(),
        );

        // ...
    }

    // ...
}
```

---------------------

### Use PHPStorm

 Settings -> Command Line Tools Console -> + (new command) -> Choose Tool (Tool based on Symfony Console) -> Alias (v) -> Path to script (bin/vagrant)


---------------------

### Configuration example

You can configure default query parameter names and templates

```yaml
performer_vagrant:
    defaults:
        remote_php_interpreter: /usr/bin/php
        remote_site_dir: /var/www
        remote_symfony_console: /bin/console
    users:
        remote_commands: ['Vendor\Path\Command', ... ]
```

---------------------

### Command example

```
vagrant ssh --command '/usr/bin/php /var/www/bin/console cache:clear --env=dev'
```
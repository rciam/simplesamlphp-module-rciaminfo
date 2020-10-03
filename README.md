# simplesamlphp-module-rciaminfo

A SimpleSAMLphp module for displaying information about RCIAM. To this end, the module displays a page containing information about the connected services. The information is organised in a table that includes the name, description and privacy statement of each connected service.

## Installation

### Clone repository

Clone this repository into the `modules` directory of your SimpleSAMLphp
installation as follows:

    cd /path/to/simplesamlphp/modules
    git clone https://github.com/rciam/simplesamlphp-module-rciaminfo.git rciaminfo
    🍺

## Configuration

Copy the module configuration template from `config-templates` into the global `config/` directory:

    cp rciaminfo/config-templates/module_rciaminfo.php /path/to/simplesamlphp/config/

The following configuration options are available:

* `store`: Configuration options for database-based service store
* `serviceIdExcludeList`: List of Service IDs (SAML SP entityIDs or OIDC Client IDs) to exclude
* `metadataSrcExcludeList`: List of SAML SP metadata sources to exclude from the displayed services
* `infoConfig`: Configuration options for the service overview table 

## Compatibility matrix

This table matches the module version with the supported SimpleSAMLphp version.

| Module |  SimpleSAMLphp  |
|:------:|:--------------:|
| v1.0   | v1.14          |
| v2.0   | v1.17          |

## License

Licensed under the Apache 2.0 license, for details see `LICENSE`.

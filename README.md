# Install & Configure

```php
services:
    Arxy\GdprDumpBundle\Converter\DoctrineConverter:
        arguments: ["@doctrine.orm.entity_manager"]

    Arxy\GdprDumpBundle\Transformer\FakerTransformer: ~
    Arxy\GdprDumpBundle\Transformer\StaticValueTransformer: ~
    Arxy\GdprDumpBundle\Transformer\SymfonyPasswordTransformer: ~

arxy_gdpr_dump:
    dsn: 'mysql://user:password@host:port/dbName'
    value_converter: 'Arxy\GdprDumpBundle\Converter\DoctrineConverter' // optional
    gdpr:
        table_name:
            column_name:
                transformer: Arxy\GdprDumpBundle\Transformer\FakerTransformer
                options:
                    generator: firstName
                    arguments: { 'gender': 'male' }
                    locale: en_US
```

## Converters:
Converters are used to convert value from database to PHP and vice-versa. (For example - convert string '2019-08-20 23:50:50' to \DateTime(''2019-08-20 23:50:50'))

### Available converters:
`Arxy\GdprDumpBundle\Converter\DoctrineConverter` - uses Doctrine metadata to convert values.

## Transformers:
Transformers are used to transform value of column to GDPR-compatible.

### Available transformer:

`Arxy\GdprDumpBundle\Transformer\FakerTransformer` used with option `generator`, `arguments` and/or `locale`
See https://github.com/fzaninotto/Faker for all available `generator`/`arguments` values.

`Arxy\GdprDumpBundle\Transformer\JsonTransformer` - decorates another transformer. Simply `json_encode` it's value. 

`Arxy\GdprDumpBundle\Transformer\SerializeTransfoemr` - decorates another transformer. Simply `serialize` it's value.

`Arxy\GdprDumpBundle\Transformer\StaticValueTransformer` - sets the value of option `value`

`Arxy\GdprDumpBundle\Transformer\SymfonyPasswordTransformer` used with option `password` and `saltColumn`.
Encodes `password` as per `security` section of Symfony Configuration.

## How it works
First values are fetched from database. If ValueConverter is available - it converts the value into PHP variable.
Then ValueTransformer is called. The returned value is passed to ValueConverter, if available which converts it into Database Value.

# Usage

Use type-hint `Ifsnop\Mysqldump\Mysqldump` and see documentation of library at `https://github.com/ifsnop/mysqldump-php`.

# Configuring and installing using pure php.

```php
$valueTransformer = new \Arxy\GdprDumpBundle\ValueTransformer();
$valueTransformer->addTransformer(new \Arxy\GdprDumpBundle\Transformer\FakerTransformer());

$tableCustomers = new \Arxy\GdprDumpBundle\Metadata\TableMetadata("customers");
$tableCustomers->addColumn(new \Arxy\GdprDumpBundle\Metadata\ColumnMetadata("firstName", \Arxy\GdprDumpBundle\Transformer\FakerTransformer::class, [
'generator' => 'firstName']));
$valueTransformer->addTableMetadata($tableCustomers);

$mysqldump = new \Ifsnop\Mysqldump\Mysqldump($dsn, $user, $password, $dumpSettings, $pdoSettings);
$mysqldump->setTransformColumnValueHook([$transformValue, 'transform']);
```
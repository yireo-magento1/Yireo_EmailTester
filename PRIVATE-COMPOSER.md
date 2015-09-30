# Instructions for using composer with this private extension

We include `composer` instructions for all our free extensions. However, with paid subscriptions,
using `composer` is a bit more difficult, because there is no public repository to make use of.
There are ways though: Make sure to use an up-to-date `composer` version (2015-09-25 or later) because
we are making use of the new `path` type.

Use `composer` to install this extension. First make sure to initialize composer with the right settings:

    composer -n init
    composer install --no-dev

Next, download this extension folder, including the `source` folder and the `composer.json` to a folder
(for instance `magento_dir/../extensions/yireo/yireo_emailtester`) outside of your Magento root:

    magento_dir/../extensions/yireo/yireo_emailtester

Next, modify your local composer.json file:

    {
        "require": {
            "yireo/yireo_emailtester": "dev-master",
            "magento-hackathon/magento-composer-installer": "*"
        },    
        "repositories":[
            {
                "packagist": false
            },
            {
                "type":"composer",
                "url":"http://packages.firegento.com"
            },
            {
                "type":"path",
                "url":"/magento_dir/../extensions/yireo/yireo_emailTester"
            }
        ],
        "extra":{
            "magento-root-dir":"/magento_dir",
            "magento-deploystrategy":"copy"           
        }
    }

Make sure to change the `url` value of the `path` type if you are using another folder.
Also make sure to set the `magento-root-dir` properly. Test this by running:

    composer update --no-dev

Done.


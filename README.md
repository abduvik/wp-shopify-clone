# wp-shopify-clone
Shopify-Clone SaaS using WordPress and WPCS.io

Start Docker compose

`docker-compose up`

Create symlink

`fswatch -o src | xargs -n1 -I{} rsync -a src/ dist/plugins/wpcs-woo-subscriptions`
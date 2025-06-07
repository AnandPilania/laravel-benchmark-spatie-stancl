#!/bin/bash

cat > spatie_urls.txt << EOF
http://tenant1.benchmark-packages.test/api/products
http://tenant2.benchmark-packages.test/api/products
http://tenant3.benchmark-packages.test/api/products
http://tenant1.benchmark-packages.test/api/dashboard-stats
http://tenant2.benchmark-packages.test/api/dashboard-stats
EOF

cat > stancl_urls.txt << EOF
http://tenant1.benchmark-packages.test/api/products
http://tenant2.benchmark-packages.test/api/products
http://tenant3.benchmark-packages.test/api/products
http://tenant1.benchmark-packages.test/api/dashboard-stats
http://tenant2.benchmark-packages.test/api/dashboard-stats
EOF

echo "Siege testing Spatie..."
siege -c 50 -t 5m -f spatie_urls.txt --json > results/spatie_siege_results.json

echo "Siege testing Stancl..."
siege -c 50 -t 5m -f stancl_urls.txt --json > results/stancl_siege_results.json

#!/bin/bash

echo "Starting HTTP benchmarks..."

SPATIE_URL="http://tenant1.benchmark-packages.test"
STANCL_URL="http://tenant1.benchmark-packages.test"

echo "=== Spatie HTTP Benchmarks ==="
echo "Products endpoint:"
ab -n 1000 -c 10 -H "Accept: application/json" ${SPATIE_URL}/api/products

echo "Dashboard stats endpoint:"
ab -n 1000 -c 10 -H "Accept: application/json" ${SPATIE_URL}/api/dashboard-stats

echo "=== Stancl HTTP Benchmarks ==="
echo "Products endpoint:"
ab -n 1000 -c 10 -H "Accept: application/json" ${STANCL_URL}/api/products

echo "Dashboard stats endpoint:"
ab -n 1000 -c 10 -H "Accept: application/json" ${STANCL_URL}/api/dashboard-stats

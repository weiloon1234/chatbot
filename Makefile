# Makefile for Flutter project versioning & archiving

.PHONY: all

# Print current pubspec version
all:
	npm run prod && php artisan config:clear && php artisan cache:clear

improve:
	npm run improve && php artisan config:clear && php artisan cache:clear

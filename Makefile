clean:
	echo "Cleaning up..."

test: clean
	./vendor/bin/pest ./tests --colors=always

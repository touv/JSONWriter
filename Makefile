PEAR=pear
PHPUNIT=phpunit
XSLTPROC=xsltproc
CP=cp
MKDIR=mkdir
RM=rm

all : 
	@echo "try :"
	@echo "make release "
	@echo "make push"


test :
	$(PHPUNIT) JSONWriterTest.php

push:
	git push
	git push --tags

release: JSONWriter-`./extract-version.sh`.tgz

JSONWriter-`./extract-version.sh`.tgz: package.xml
	$(PEAR) package package.xml
	git tag -a -m "Version `./extract-version.sh`"  v`./extract-version.sh`

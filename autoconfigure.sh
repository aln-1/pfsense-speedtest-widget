#!/bin/sh

if [ "$(cat /etc/version | cut -c 1-3)" == "2.4" ]; then
	pkg install -y py37-speedtest-cli
	fetch -q -o /usr/local/www/widgets/widgets/speedtest.widget.php https://raw.githubusercontent.com/aln-1/pfsense-speedtest-widget/master/speedtest.widget.php
fi

if [ "$(cat /etc/version | cut -c 1-3)" == "2.5" ]; then
	pkg install -y py38-speedtest-cli-2.1.3 
	fetch -q -o /usr/local/www/widgets/widgets/speedtest.widget.php https://raw.githubusercontent.com/aln-1/pfsense-speedtest-widget/master/speedtest.widget.php
fi
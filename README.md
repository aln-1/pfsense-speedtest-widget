# Speedtest dashboard widget for pfSense

## Install Manual Mode

To use this widget you will need to install the speedtest package

```
pkg update ; pkg install -y py37-speedtest-cli
```

Copy the widget file **speedtest.widget.php** to **/usr/local/www/widgets/widgets/** on your pfSense machine.

Install the widget on your dashboard.

## Install Automatic mode

Diagnostics - Command prompt

Run this command

fetch -q -o - https://raw.githubusercontent.com/aln-1/pfsense-speedtest-widget/master/autoconfigure.sh | sh
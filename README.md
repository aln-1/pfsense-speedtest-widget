# Speedtest dashboard widget for pfSense

## Install

To use this widget you will need to install the speedtest package

```
pkg update ; pkg install -y $( pkg search speedtest-cli | awk '{ print $1 }' )
```

Copy the widget file **speedtest.widget.php** to **/usr/local/www/widgets/widgets/** on your pfSense machine.

Install the widget on your dashboard.


# TPLink TL-WPA4220 V5 - Helper scripts

Helper scripts I made to reboot my TPLink TL-WPA4220 V5 powerline

## Brief

Simple PHP script I created by reverse engineering the web interface of my TP-Link TL-WPA4220 5.0 powerline adapter.

I’ve seen libraries around that do the same thing, but I believe they’re not suitable for version V5 (although they worked on my old V4).

This code isn’t intended to be elegant, modular, or universal: I just needed a quick way to automate the powerline reboot.

I'm publishing it anyway in case it might be helpful or serve as a starting point for someone: it could save you a few hours of time.

The code replicates the encryption and hash generation mechanism required to make API calls to the powerline, and then it makes the reboot request.

Note that if you're willing to inspect the other command codes using your browser’s web tools while using the powerline webgui, you can likely adapt it easily to make other requests (turning LEDs on and off, etc...).

## Tested hardware
```
TL-WPA4220 5.0
Firmware: 1.0.12 Build 230309 Rel.64136n (6985)
```

## Usage sample:

Command:
```
php reboot.php 192.168.0.4 youradminpasswd
```

Remember to put your real powerline IP address and your admin password ;)

Output:
```
Calling url: http://192.168.0.4/?code=7&asyn=1
Calling url: http://192.168.0.4/?code=7&asyn=0&id=xxxxxxyyy
Calling url: http://192.168.0.4/?code=6&asyn=1&id=xxxxxxyyy
Success!
```

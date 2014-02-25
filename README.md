REMOTE WAKE/SLEEP-ON-LAN SERVER
=========================
This is a modified version to fit my needs.
I have a Buffalo NAS which needs every 4 minutes an wake on LAN signal to stay on.
I'm using the modified script to start the NAS and set a flag-file (wakeupflag).
Another shell script which runs in a cronjob every 4 minutes tests for the flag-file 
and sends if it exists periodically the wake on LAN signal.
When the user chooses to shutdown the NAS via the PHP script only the flag-file gets deleted 
and the NAS will shutdown after ~5 minutes.
I have removed the password prodtection since it should be only reachable via the local network
and gives easier access.

[Original Description:]
This ia simple webapp that runs on your Raspberry Pi to turn it into a remotely accessible Wake/Sleep-On-LAN Server. [Follow the detailed tutorial](http://www.jeremyblum.com/2013/07/14/rpi-wol-server/) on my website for instructions on how to get this working, and forwarded through a router. This is very useful when you have high-powered machine that you don't want to keep on all the time, but that you want to keep remotely accessible for Remote Desktop, SSH, FTP, etc.

License
-------
This work is licensed under the [GNU GPL v3](http://www.gnu.org/licenses/gpl.html).
Please share improvements or remixes with the community, and attribute me (Jeremy Blum, <http://www.jeremyblum.com>) when reusing portions of my code.
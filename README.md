# um-schedule
Scheduler for University of Manitoba students that pulls live data from Aurora

## getting started

1. Copy all the files into a PHP supported environment (i.e XAMPP/LAMP/apache2)
2. Run localhost/index.html in your browser

## explanation of files

### 404.php
used to redirect back to /
    
### app.yaml
used to deploy this application on GAE - not required if you're not using Google App Engine

### dom.php
used to parse raw HTML into PHP objects

### favicon.ico
wrench favicon for the website

### index.html
landing page to choose between the fall or winter semester

### main.php
the page that processes the ajax requests from script.js

### script.js
processes the information provided by main.php

### sprite.png
used by the selector box

### style.css
basic styling for the wesbtie

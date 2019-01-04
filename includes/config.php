<?php
# CONFIG
# Age in days of an item request at which it gets a priority bump:
define("DATE_CUTOFF",7);
# Number of votes required for first priority bump:
define("MID_VOTE_CUTOFF",2);
# Number of votes required for highest priority:
define("HIGH_VOTE_CUTOFF",5);
# Password Hash for Admin Authentication:
define('PASS_HASH','$2y$12$HB9WAkFvhdk6Qcph1KG1oOnaDv3FFh7Fw.UKsV5sDKpXF5Ps6tzka');
# To create one, enter the following at a php prompt: 
# Echo password_hash("PASSWORD", PASSWORD_BCRYPT, ['cost' => 12]);
# To use heavier encryption increase the cost parameter
# Note that the time taken to verify increases exponentially with cost

# DB CONNECTION PARAMS: Set these to properly reflect your db setup.
define("DB_SERVER",'localhost');
define("DB_USER",'webapp');
define("DB_PASS",'password');
define("DB_NAME",'shopping_list');
#Random PHP tools

## What is it?

A Collection of random tools I have made in PHP I thought worth sharing.

## getHeader.php

Use by specifying a url to return the headers of e.g. getHeader.php?u=www.google.com would return and print out the headers from www.google.com

## anonEmail.php

Use to send spoof an e-mail from any e-mail address. Please don't abuse. 

## randomIP.php

This file generates 5 random public IP addresses at a time. It uses multi curl to get the headers from these IP addresses simultaneously. If the http status code of a random ip doesn't equal 0 then it echos the headers out in green. Great for finding home routers and other devices connected to the Internet at random. For educational purposes only, please don't abuse.

By default it generates 5 blocks of IP addresses out. If you want to specify more you can do this with the param l=x where x is the number of IP address blocks to generate. e.g. randomIP.php?l=50 generates 250 random IP addresses (50 blocks of 5 IP addresses).

## tracking/index.php

This file is an implementation of a really simple 1 pixel tracking gif. When called it increases a count in a database by 1 and returns a 1 pixel gif tracking pixel. It can be used as a simple counter to find out how many times a page or e-mail has been accessed. It has two options, reset and display which can be specified by url parameters do=reset or do=display. These parameters reset the database count or display the number of times the tracking pixel has been called. 

For this script to work you need to create a MySQL database called tracking with a table called count1 which has one integer field called count. Connection to this database then needs to be made in the databaseConnection.php file which is included on line 6. I've not included this file in github for obvious reasons.
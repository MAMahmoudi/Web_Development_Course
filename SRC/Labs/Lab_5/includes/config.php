<?php
// Settings of the Syaha website.
// XAMPP's MySQL account is "root" with an empty password: fine on your own computer,
// never on a real server (see Lab 10). Keep the real settings of a server out of Git.

const DB_HOST = '127.0.0.1';
const DB_PORT = 3307;
const DB_NAME = 'syaha';
const DB_USER = 'root';
const DB_PASS = '';

// the images of the site are in SRC/IMAGES, two folders above the pages of Lab_5
const IMAGES_URL = '../../IMAGES/';

// a booking must be made at least this many days before departure
const BOOKING_MIN_DAYS = 3;

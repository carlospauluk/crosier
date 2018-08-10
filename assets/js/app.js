// assets/js/app.js
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
import $ from 'jquery';
import '../css/app.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// var $ = require('jquery');

// console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

// loads the jquery package from node_modules



// import the function from greet.js (the .js extension is optional)
// ./ (or ../) means to look for a local file
var greet = require('./greet');

$(document).ready(function() {
    $('body').prepend('<h1>'+greet('john')+'</h1>');
});

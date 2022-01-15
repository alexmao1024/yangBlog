/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

import 'bootstrap';

import $ from 'jquery';

$(document).ready(function (){
    $('button.js-reply-comment-btn').on('click',function (){
        let postId = $(this).data('post-id');
        let parentId = $(this).data(('parent-id'));

    })
})
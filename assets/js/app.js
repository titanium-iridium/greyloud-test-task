// require('../css/app.scss');
// require('bootstrap-sass');

var $ = require('jquery');


$(document).ready(function() {
    // DOM ready handler
    
    // collection of executors
    var $execList = $('.task-executors');
    
    // append "Delete executor" button to all assigned executors
    $execList.find('li').each(function() {
        apendDeleteExecButton($(this));
    });
    
    // append the "Add new executor" button to executors list
    var $addExecBtn = $('<button class="btn btn-success btn-sm btn-add-exec" title="Add new executor">+</button>');
    var $addExecLi = $('<li class="executor input-group mt-2"></li>').append($addExecBtn);
    $execList.append($addExecLi);
    
    $addExecBtn.on('click', function(e) {
        // "Add new executor" click handler
        e.preventDefault();
        addExecutor($execList);
    });

    $execList.on('click', '.btn-delete-executor', function (e) {
        // "Delete executor" click handler
        e.preventDefault();
        $(this).closest('.executor').remove();
    });
    
    $execList.closest('form').submit(function () {
        // form submit handler
        //
        // checking for duplicates in executors list
        // server validation here: \App\Entity\Task::validate (not needed actually) 
        var assignedExecs = [];
        var haveDups = false;
        $execList.find('.executor').each(function () {
            var value = $(this).find('select').val();
            if (assignedExecs.indexOf(value) === -1) {
                assignedExecs.push(value);
            } else {
                haveDups = true;
                return false;
            }
        });
        
        if (haveDups) {
            alert('No duplicates allowed in executors list!');
            return false;
        }
    });


    /**
     * Creates and appends new executor subform
     * @param $list
     */
    function addExecutor($list) {
        // get new executor subform prototype
        var newForm = $list.data('prototype');
        // get new executor index
        var index = $execList.find('.executor').length - 1;
        // replace '__name__' in prototype's HTML with subform index
        newForm = newForm.replace(/__name__/g, index);
    
        // append new executor subform before "Add new executor" button 
        var $newFormLi = $('<li class="executor input-group mb-2"></li>').append(newForm);
        $addExecLi.before($newFormLi);
        
         // append "Delete executor" button to new subform
        apendDeleteExecButton($newFormLi);
    }


    /**
     * Appends "Delete executor" button
     * @param $parent
     */
    function apendDeleteExecButton($parent) {
        var $removeFormA = $('<div class="input-group-append">' 
            + '<button class="btn-delete-executor btn btn-danger btn-sm" title="Delete executor">-</button></div>');
        $parent.append($removeFormA);
    }
    
});


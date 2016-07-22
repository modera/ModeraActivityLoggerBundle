<?php

// create

$createActionRequestPayload = array(
    'hydration' => array(
        'profile' => 'new_record',
    ),
    'record' => array(
        'title' => 'Some title',
        'body' => 'Some text goes here',
    ),
);

$successfulCreateActionResponse = array(
    'success' => true,
    'created_models' => array(
        'article',
    ),
    'result' => array(
        'id' => 1,
        'title' => 'Some title',
        'body' => 'Some text goes here',
    ),
);

$validationFailedCreateActionResponse = array(
    'success' => false,
    'field_errors' => array(
        'title' => array(
            'Title is too short',
        ),
    ),
    'general_errors' => array(
        'Admin has disabled functionality of adding new articles',
    ),
);

// list

$listActionRequestPayload = array(
    'hydration' => array(
        'profile' => 'list',
    ),
    'filter' => array(
        array(
            'property' => 'category',
            'value' => 'eq:1',
        ),
        array(
            'property' => 'isPublished',
            'value' => 'eq:true',
        ),
    ),
    'fetch' => array(
        'author',
    ),
    'limit' => 25,
);

$listActionResponse = array(
    'success' => true,
    'items' => array(
        array(
            'title' => 'Some title',
            'body' => 'Some text goes here',
        ),
    ),
);

// misc

$exceptionResponseInDev = array(
    'success' => false,
    'exception_class' => 'RuntimeException',
    'stack_trace' => array(
        '-',
        '--',
        '---',
    ),
    'file' => 'FooController.php',
    'message' => 'Something went terribly wrong',
    'code' => '123',
    'line' => '123',
);

$exceptionResponseInProd = array(
    'success' => false,
    'message' => 'Some preconfigured default message',
);

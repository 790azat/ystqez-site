<?php

return [
    'accepted' => 'The :attribute must be accepted.',
    'array' => 'The :attribute must be a list.',
    'boolean' => 'The :attribute must be yes or no.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'date' => 'The :attribute must be a valid date.',
    'email' => 'Please enter a valid email address.',
    'in' => 'The selected :attribute is invalid.',
    'integer' => 'The :attribute must be a whole number.',
    'json' => 'The :attribute must be valid JSON.',
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'string' => 'The :attribute may not be longer than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
        'file' => 'The :attribute may not be larger than :max KB.',
    ],
    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
        'file' => 'The :attribute must be at least :min KB.',
    ],
    'numeric' => 'The :attribute must be a number.',
    'regex' => 'The :attribute format is invalid.',
    'required' => 'Please fill in the :attribute.',
    'string' => 'The :attribute must be text.',
    'unique' => 'This :attribute is already taken.',
    'url' => 'The :attribute must be a valid link.',
    'password' => [
        'letters' => 'The password must contain at least one letter.',
        'mixed' => 'The password must contain both upper and lower case letters.',
        'numbers' => 'The password must contain at least one number.',
        'symbols' => 'The password must contain at least one symbol.',
        'uncompromised' => 'This password has appeared in a data leak. Please choose another one.',
    ],

    'attributes' => [
        'name' => 'name',
        'email' => 'email',
        'password' => 'password',
        'title' => 'title',
        'body' => 'text',
        'message' => 'message',
        'nickname' => 'nickname',
    ],
];

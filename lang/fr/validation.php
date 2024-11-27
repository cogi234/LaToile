<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champ :attribute doit être une URL valide.',
    'after' => 'Le champ :attribute doit être une date après :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date égale ou après :date.',
    'alpha' => 'Le champ :attribute ne peut contenir que des lettres.',
    'alpha_dash' => 'Le champ :attribute ne peut contenir que des lettres, des nombres et des tirets.',
    'alpha_num' => 'Le champ :attribute ne peut contenir que des lettres et des nombres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'ascii' => 'Le champ :attribute ne peut contenir que des caractères ASCII.',
    'before' => 'Le champ :attribute doit être une date avant :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date égale ou avant :date.',
    'between' => [
        'array' => 'Le champ :attribute doit avoir entre :min et :max articles.',
        'file' => 'Le champ :attribute doit être entre :min et :max kilo octets.',
        'numeric' => 'Le champ :attribute doit être entre :min et :max.',
        'string' => 'Le champ :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non valide.',
    'confirmed' => 'Le champ de confirmation ne correspond pas.',
    'contains' => 'Le champ :attribute manque une valeur obligatoire.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute doit correspondre au format :format.',
    'decimal' => 'Le champ :attribute doit avoir :decimal places décimales.',
    'declined' => 'Le champ :attribute doit être refusé.',
    'declined_if' => 'Le champ :attribute doit être refusé lorsque :other est :value.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit être :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit être entre :min et :max chiffres.',
    'dimensions' => 'Le champ :attribute a des dimensions invalides.',
    'distinct' => 'Le champ :attribute a une valeur dupliquée.',
    'doesnt_end_with' => 'Le champ :attribute ne peut pas finir avec: :values.',
    'doesnt_start_with' => 'Le champ :attribute ne peut pas commencer avec: :values.',
    'email' => 'Le champ :attribute doit être une adresse courriel valide.',
    'ends_with' => 'Le champ :attribute doit finir avec: :values.',
    'enum' => 'Le :attribute sélectionné est invalide.',
    'exists' => 'Le :attribute sélectionné est invalide.',
    'extensions' => 'Le champ :attribute doit avoir une de ces extensions: :values.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit contenir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit avoir plus de :value articles.',
        'file' => 'Le champ :attribute doit être plus grand que :value kilo octets.',
        'numeric' => 'Le champ :attribute doit être plus grand que :value.',
        'string' => 'Le champ :attribute doit contenir plus que :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit avoir au moins :value articles.',
        'file' => 'Le champ :attribute doit être au moins :value kilo octets.',
        'numeric' => 'Le champ :attribute doit être au moins :value.',
        'string' => 'Le champ doit avoir au moins :value caractères.',
    ],
    'hex_color' => 'Le champ :attribute doit être une couleur hexadécimale valide.',
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le :attribute sélectionné est invalide.',
    'in_array' => 'Le champ :attribute doit exister dans :other.',
    'integer' => 'Le champ :attribute doit être un nombre entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPV4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPV6 valide.',
    'json' => 'Le champ :attribute doit être du JSON valide.',
    'list' => 'Le champ :attribute doit être une liste.',
    'lowercase' => 'Le champ :attribute doit être en minuscules.',
    'lt' => [
        'array' => 'Le champ :attribute doit contenir moins de :value articles.',
        'file' => 'Le champ :attribute doit être plus petit que :value kilo octets.',
        'numeric' => 'Le champ :attribute doit être plus petit que :value.',
        'string' => 'Le champ :attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute ne peut pas avoir plus que :value articles.',
        'file' => 'Le champ :attribute ne peut pas être plus grand que :value kilo octets.',
        'numeric' => 'Le champ :attribute ne peut pas être plus grand que :value.',
        'string' => 'Le champ :attribute ne peut pas avoir plus que :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne peut pas avoir plus que :max articles.',
        'file' => 'Le champ :attribute ne peut pas être plus grand que :max kilo octets.',
        'numeric' => 'Le champ :attribute ne peut pas être plus grand que :max.',
        'string' => 'Le champ :attribute ne peut pas avoir plus que :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute must not have more than :max digits.',
    'mimes' => 'Le champ :attribute must be a file of type: :values.',
    'mimetypes' => 'Le champ :attribute must be a file of type: :values.',
    'min' => [
        'array' => 'Le champ :attribute doit avoir au moins :min articles.',
        'file' => 'Le champ :attribute doit être au moins :min kilo octets.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
        'string' => 'Le champ doit avoir au moins :min caractères.',
    ],
    'min_digits' => 'Le champ :attribute doit avoir au moins :min chiffres.',
    'missing' => 'Le champ :attribute doit être vide.',
    'missing_if' => 'Le champ :attribute doit être vide lorsque :other est :value.',
    'missing_unless' => 'Le champ :attribute doit être vide lorsque :other n\'est pas :value.',
    'missing_with' => 'Le champ :attribute doit être vide lorsque :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être vide lorsque :values sont présents.',
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'Le :attribute sélectionné est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => [
        'letters' => 'Le champ :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champ :attribute doit contenir au moins une lettre minuscule et majuscule.',
        'numbers' => 'Le champ :attribute doit contenir au moins un nombre.',
        'symbols' => 'Le champ :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le :attribute donné est apparu dans une fuite de données. Choississez un :attribute différent.',
    ],
    'present' => 'Le champ :attribute doit être présent.',
    'present_if' => 'Le champ :attribute doit être présent lorsque :other est :value.',
    'present_unless' => 'Le champ :attribute doit être présent lorsque :other n\'est pas :value.',
    'present_with' => 'Le champ :attribute doit être présent lorsque :values est présent.',
    'present_with_all' => 'Le champ :attribute doit être présent lorsque :values sont présents.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit lorsque :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit lorsque :other n\'est pas dans :values.',
    'prohibits' => 'Le champ :attribute interdit :other d\'être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est requis.',
    'required_array_keys' => 'Le champ :attribute doit contenir des entrées pour: :values.',
    'required_if' => 'Le champ :attribute est requis lorsque :other est :value.',
    'required_if_accepted' => 'Le champ :attribute est requis lorsque :other est accepté.',
    'required_if_declined' => 'Le champ :attribute est requis lorsque :other est refusé.',
    'required_unless' => 'Le champ :attribute est requis lorsque :other n\'est pas dans :values.',
    'required_with' => 'Le champ :attribute est requis lorsque :values est présent.',
    'required_with_all' => 'Le champ :attribute est requis lorsque :values sont présents.',
    'required_without' => 'Le champ :attribute est requis lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est requis lorsque aucuns des :values sont présents.',
    'same' => 'Le champ :attribute doit correspondre à :other.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size articles.',
        'file' => 'Le champ :attribute doit être :size kilo octets.',
        'numeric' => 'Le champ :attribute doit être :size.',
        'string' => 'Le champ :attribute doit être :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer avec un de: :values.',
    'string' => 'Le champ :attribute doit être du texte.',
    'timezone' => 'Le champ :attribute doit être un fuseau horaire valide.',
    'unique' => 'Ce :attribute a déjà été pris.',
    'uploaded' => 'Le téléchargement de :attribute a échoué.',
    'uppercase' => 'Le champ :attribute doit être majuscule.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'ulid' => 'Le champ :attribute doit être un ULID valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

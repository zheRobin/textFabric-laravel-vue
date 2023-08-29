<?php

return [
    'presets' => [
        'en' => [
            [
                'name' => 'Headline',
                'system_prompt' => 'Write a one-line headline about the following product.',
                'user_prompt' => '@Product group',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
            [
                'name' => 'Subheading',
                'system_prompt' => 'Write a 2 sentence subheading for the product.',
                'user_prompt' => '@Product group @Product',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
            [
                'name' => 'Bulletpoints',
                'system_prompt' => 'Write 3 bullet points with one sentence each about the most important features of the product and its characteristics. Start with an appropriate headline followed by the bullet points.',
                'user_prompt' => '@Product group
@Product
@Feature 1
@Feature 2
@Feature 3
@Feature 4
@Feature 5',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
            [
                'name' => 'Content',
                'system_prompt' => 'Write a marketing text with 3 paragraphs about the mentioned product.',
                'user_prompt' => '@Product group
@Product
@Feature 1
@Feature 2
@Feature 3
@Feature 4
@Feature 5',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 2,
                'frequency_penalty' => 0,
            ],
        ],
        'de' => [
            [
                'name' => 'Headline',
                'system_prompt' => 'Schreibe eine einzeilige Headline zu folgendem Produkt.',
                'user_prompt' => '@Produktgruppe',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
            [
                'name' => 'Subheading',
                'system_prompt' => 'Schreibe ein Subheading mit 2 Sätzen für das Produkt.',
                'user_prompt' => '@Produktgruppe @Produkt',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
            [
                'name' => 'Bulletpoints',
                'system_prompt' => 'Schreibe 3 Bulletpoints mit jeweils einem Satz über die wichtigsten Features zu dem genanten Produkt und dessen Eigenschaften. Beginne mit einer passenden Überschrift gefolgt von den Bulletpoints.',
                'user_prompt' => '@Produktgruppe
@Produkt
@Merkmal 1
@Merkmal 2
@Merkmal 3
@Merkmal 4
@Merkmal 5',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 0,
                'frequency_penalty' => 0,
            ],
            [
                'name' => 'Content',
                'system_prompt' => 'Schreibe einen Marketingtext mit 3 Absätzen zu dem genannten Produkt.',
                'user_prompt' => '@Produktgruppe
@Produkt
@Merkmal 1
@Merkmal 2
@Merkmal 3
@Merkmal 4
@Merkmal 5',
                'model' => 'gpt-3.5-turbo',
                'temperature' => 1,
                'top_p' => 1,
                'presence_penalty' => 2,
                'frequency_penalty' => 0,
            ],
        ]
    ]
];

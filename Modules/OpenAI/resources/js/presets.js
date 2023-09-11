export const defaultPresets = {
    'en': [
        {
            slug: 'headline',
            name: 'Headline',
            system_prompt: 'Write a one-line title about the following product. Do not use any quotation marks.',
            user_prompt: '@Product group',
        },
        {
            slug: 'subheading',
            name: 'Subheading',
            system_prompt: 'Write a 2 sentence subheading for the product.',
            user_prompt: '@Product group @Product',
        },
        {
            slug: 'bulletpoints',
            name: 'Bulletpoints',
            system_prompt: 'Write 3 bullet points with one sentence each about the most important features of the product and its characteristics. Use only dashes.',
            user_prompt: '@Product group\n' +
                '@Product\n' +
                '@Feature 1\n' +
                '@Feature 2\n' +
                '@Feature 3\n' +
                '@Feature 4\n' +
                '@Feature 5',
        },
        {
            slug: 'content',
            name: 'Content',
            system_prompt: 'Write a copy with 3 paragraphs about the mentioned product.',
            user_prompt: '@Product group\n' +
                '@Product\n' +
                '@Feature 1\n' +
                '@Feature 2\n' +
                '@Feature 3\n' +
                '@Feature 4\n' +
                '@Feature 5',
        }
    ],
    'de': [
        {
            slug: 'headline',
            name: 'Headline',
            system_prompt: 'Schreibe einen einzeiligen Titel zu folgendem Produkt. Verwende keine Anführungszeichen.',
            user_prompt: '@Produktgruppe',
        },
        {
            slug: 'subheading',
            name: 'Subheading',
            system_prompt: 'Schreibe ein Subheading mit 2 Sätzen für das Produkt.',
            user_prompt: '@Produktgruppe @Produkt',
        },
        {
            slug: 'bulletpoints',
            name: 'Bulletpoints',
            system_prompt: 'Schreibe 3 Bulletpoints mit jeweils einem Satz über die wichtigsten Features zu dem genanten Produkt und dessen Eigenschaften. Verwende nur Bindestriche.',
            user_prompt: '@Produktgruppe\n' +
                '@Produkt\n' +
                '@Merkmal 1\n' +
                '@Merkmal 2\n' +
                '@Merkmal 3\n' +
                '@Merkmal 4\n' +
                '@Merkmal 5',
        },
        {
            slug: 'content',
            name: 'Content',
            system_prompt: 'Schreibe einen Werbetext mit 3 Absätzen zu dem genannten Produkt.',
            user_prompt: '@Produktgruppe\n' +
                '@Produkt\n' +
                '@Merkmal 1\n' +
                '@Merkmal 2\n' +
                '@Merkmal 3\n' +
                '@Merkmal 4\n' +
                '@Merkmal 5',
        },
    ]
}

export function getPresets(locale) {
    return [...defaultPresets[locale]];
}

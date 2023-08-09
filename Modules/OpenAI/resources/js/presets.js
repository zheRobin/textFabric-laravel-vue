export const defaultPresets = {
    'en': [
        {
            slug: 'headline',
            name: 'Headline',
            system_prompt: 'Write a one-line headline about the following product.',
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
            system_prompt: 'Write 3 bullet points with one sentence each about the most important features of the product and its characteristics. Start with an appropriate headline followed by the bullet points.',
            user_prompt: '@Product group\n' +
                '@Product\n' +
                '@Headline\n' +
                '@Feature 1\n' +
                '@Feature 2\n' +
                '@Feature 3\n' +
                '@Feature 4\n' +
                '@Feature 5',
        },
        {
            slug: 'content',
            name: 'Content',
            system_prompt: 'Write a marketing text with 3 paragraphs about the mentioned product.',
            user_prompt: '@Product group\n' +
                '@Product\n' +
                '@Headline\n' +
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
            system_prompt: 'Schreibe eine einzeilige Headline zu folgendem Produkt.',
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
            system_prompt: 'Schreibe 3 Bulletpoints mit jeweils einem Satz über die wichtigsten Features zu dem genanten Produkt und dessen Eigenschaften. Beginne mit einer passenden Überschrift gefolgt von den Bulletpoints.',
            user_prompt: '@Produktgruppe\n' +
                '@Produkt\n' +
                '@Headline\n' +
                '@Merkmal 1\n' +
                '@Merkmal 2\n' +
                '@Merkmal 3\n' +
                '@Merkmal 4\n' +
                '@Merkmal 5',
        },
        {
            slug: 'content',
            name: 'Content',
            system_prompt: 'Schreibe einen Marketingtext mit 3 Absätzen zu dem genannten Produkt.',
            user_prompt: '@Produktgruppe\n' +
                '@Produkt\n' +
                '@Headline\n' +
                '@Merkmal 1\n' +
                '@Merkmal 2\n' +
                '@Merkmal 3\n' +
                '@Merkmal 4\n' +
                '@Merkmal 5',
        },
    ]
}

export function getPresets(locale) {
    return defaultPresets[locale];
}

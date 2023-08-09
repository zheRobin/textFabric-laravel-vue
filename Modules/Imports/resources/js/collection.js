export const defaultHeaders = {
    'en': [
        {
            name: 'Product group'
        },
        {
            name: 'Product'
        },
        {
            name: 'Headline'
        },
        {
            name: 'Feature 1'
        },
        {
            name: 'Feature 2'
        },
        {
            name: 'Feature 3'
        },
        {
            name: 'Feature 4'
        },
        {
            name: 'Feature 5'
        },
    ],
    'de': [
        {
            name: 'Produktgruppe'
        },
        {
            name: 'Produkt'
        },
        {
            name: 'Headline'
        },
        {
            name: 'Merkmal 1'
        },
        {
            name: 'Merkmal 2'
        },
        {
            name: 'Merkmal 3'
        },
        {
            name: 'Merkmal 4'
        },
        {
            name: 'Merkmal 5'
        },
    ]
}

export const defaultItems = {
    'en': [
        [
            {
                value: 'Cordless drill screwdriver',
                header: 'Product group',
            },
            {
                value: 'Cordless drill driver X234-1',
                header: 'Product',
            },
            {
                value: 'Cordless drill screwdriver for use in construction, furniture assembly and metal processing',
                header: 'Headline',
            },
            {
                value: 'high quality and robustness',
                header: 'Feature 1',
            },
            {
                value: 'Rated voltage: 12 V/DC',
                header: 'Feature 2',
            },
            {
                value: 'Machine weight with battery: 1.21 kg',
                header: 'Feature 3',
            },
            {
                value: 'handy',
                header: 'Feature 4',
            },
            {
                value: 'powerful',
                header: 'Feature 5',
            }
        ],
        [
            {
                value: 'Undercounter dishwasher',
                header: 'Product group',
            },
            {
                value: 'Dishwasher G 5423 Active Plus',
                header: 'Product',
            },
            {
                value: 'Optimum drying results thanks to AutoOpen drying',
                header: 'Headline',
            },
            {
                value: 'Best results in less than one hour',
                header: 'Feature 1',
            },
            {
                value: 'Save up to 50 % electricity',
                header: 'Feature 2',
            },
            {
                value: 'from 6.00 l liters in the Automatic program',
                header: 'Feature 3',
            },
            {
                value: 'Flexibly and securely placed',
                header: 'Feature 4',
            },
            {
                value: 'Maximum flexibility',
                header: 'Feature 5',
            }
        ],
        [
            {
                value: 'Paint spraying system',
                header: 'Product group',
            },
            {
                value: 'SuperSpray 45',
                header: 'Product',
            },
            {
                value: 'Paint sprayer for airless beginners',
                header: 'Headline',
            },
            {
                value: 'Professional, practical and lightweight',
                header: 'Feature 1',
            },
            {
                value: 'Simplest application thanks to HEA technology',
                header: 'Feature 2',
            },
            {
                value: 'Up to 55% less paint mist',
                header: 'Feature 3',
            },
            {
                value: 'Up to 35% less ink consumption',
                header: 'Feature 4',
            },
            {
                value: 'Excellent results when painting',
                header: 'Feature 5',
            }
        ],
    ],
    'de': [
        [
            {
                value: 'Akku-Bohrschrauber',
                header: 'Produktgruppe',
            },
            {
                value: 'Akku-Bohrschrauber X234-1',
                header: 'Produkt',
            },
            {
                value: 'Akkubohrschrauber zum Einsatz im Bauwesen, bei der Möbelmontage und in der Metallverarbeitung',
                header: 'Headline',
            },
            {
                value: 'hohe Qualität und Robustheit',
                header: 'Merkmal 1',
            },
            {
                value: 'Nennspannung: 12 V/DC',
                header: 'Merkmal 2',
            },
            {
                value: 'Maschinengewicht mit Akku: 1,21 kg',
                header: 'Merkmal 3',
            },
            {
                value: 'handlich',
                header: 'Merkmal 4',
            },
            {
                value: 'leistungsstark',
                header: 'Merkmal 5',
            }
        ],
        [
            {
                value: 'Unterbau-Geschirrspüler',
                header: 'Produktgruppe',
            },
            {
                value: 'Geschirrspüler G 5423 Active Plus',
                header: 'Produkt',
            },
            {
                value: 'Optimale Trocknungsergebnisse dank AutoOpen-Trocknung',
                header: 'Headline',
            },
            {
                value: 'Beste Ergebnisse in weniger als einer Stunde',
                header: 'Merkmal 1',
            },
            {
                value: 'bis zu 50 % Strom sparen',
                header: 'Merkmal 2',
            },
            {
                value: 'ab 6,00 l Liter im Automatic Programm',
                header: 'Merkmal 3',
            },
            {
                value: 'Flexibel und sicher platziert',
                header: 'Merkmal 4',
            },
            {
                value: 'Maximale Flexibilität',
                header: 'Merkmal 5',
            }
        ],
        [
            {
                value: 'Farbsprühsystem',
                header: 'Produktgruppe',
            },
            {
                value: 'Farbsprühsystem SuperSpray 45',
                header: 'Produkt',
            },
            {
                value: 'Farbspritzgerät für Airless-Einsteiger',
                header: 'Headline',
            },
            {
                value: 'Professionell, praktisch und leicht',
                header: 'Merkmal 1',
            },
            {
                value: 'Einfachste Anwendung dank der HEA-Technologie',
                header: 'Merkmal 2',
            },
            {
                value: 'Bis zu 55 % weniger Farbnebel',
                header: 'Merkmal 3',
            },
            {
                value: 'Bis zu 35 % weniger Farbverbrauch',
                header: 'Merkmal 4',
            },
            {
                value: 'Hervorragende Ergebnisse beim Streichen',
                header: 'Merkmal 5',
            }
        ],
    ]
}

const itemsKey = 'demo-items';

export function getItems(locale) {
    if (!localStorage.getItem(itemsKey)) {
        setupItems();
    }

    const items = JSON.parse(localStorage.getItem(itemsKey));

    return locale
        ? items[locale]
        : items;
}

export function setupItems() {
    localStorage.setItem(itemsKey, JSON.stringify(defaultItems));
}

export function saveItem(index, data, locale) {
    let items = getItems(locale);

    items[index] = data;

    saveItems(items, locale);
}

export function saveItems(data, locale) {
    let items = getItems();

    if (locale) {
        items[locale] = data;
    } else {
        items = data;
    }

    localStorage.setItem(itemsKey, JSON.stringify(items));
}

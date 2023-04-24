export const subscriptionContactLink = 'https://www.textfabrik.io/kontakt/';

export function daysLeft(date) {
    const total = Date.parse(date) - Date.parse(new Date().toISOString());

    return  Math.floor(total/(1000*60*60*24));
}

export function toLocaleDate(date, locale, options) {
    return new Date(date).toLocaleDateString(locale, options);
}

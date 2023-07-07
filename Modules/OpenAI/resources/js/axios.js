import axios from "axios";

export const iterateCollectionItems = (pageNumber) => {
    return axios.get(route('current-collection.items.iterate'), {
        params: {
            page: pageNumber
        }
    });
}

export const itemCompletion = (presetId, itemId) => {
    return axios.get(route('openai.item-completion', {
        preset: presetId,
        item: itemId
    }));
}

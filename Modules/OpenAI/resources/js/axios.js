import axios from "axios";

export const showCollectionItem = (pageNumber) => {
    return axios.get(route('collection-items.index'), {
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

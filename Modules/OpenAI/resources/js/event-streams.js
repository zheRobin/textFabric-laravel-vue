export const streamItemCompletion = (preset, item, callable, closeCallable, errorCallable) => {
    const eventSource = new EventSource(route('openai.item-completion', {
        preset: preset,
        item: item,
    }));

    eventSource.addEventListener('update', event => {
        if (event.data === "<END_STREAMING_SSE>") {
            eventSource.close();
            closeCallable();
            return;
        }

        const data = JSON.parse(event.data);
        callable(data);
    });

    eventSource.addEventListener('error', error => {
        eventSource.close();
        errorCallable(error.data);
    });

    return eventSource;
}

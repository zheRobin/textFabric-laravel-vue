export const streamItemCompletion = (preset, item, callable, closeCallable) => {
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

    return eventSource;
}

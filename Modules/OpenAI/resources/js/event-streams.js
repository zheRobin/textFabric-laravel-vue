export const streamItemCompletion = (preset, item, callable, closeCallable, errorCallable) => {
    const uri = route('openai.item-completion', {
        preset: preset,
        item: item,
    });

    return createEventSource(uri, callable, closeCallable, errorCallable);
}

export const streamDemoItemCompletion = (item, system_prompt, user_prompt, callable, closeCallable, errorCallable) => {
    const uri = route('demo.item.completion', {
        item: JSON.stringify(item),
        system_prompt: system_prompt,
        user_prompt: user_prompt,
    });

    return createEventSource(uri, callable, closeCallable, errorCallable);
}

function createEventSource(route, callable, closeCallable, errorCallable) {
    const eventSource = new EventSource(route);

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

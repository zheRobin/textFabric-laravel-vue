<script>
import Pusher from 'pusher-js';
import { notify } from 'notiwind';

export default {
    data() {
        return {
            messages: [],
        };
    },
    mounted() {
        Pusher.logToConsole = false;

        const pusher = new Pusher('1355393f3c1191c3b6f9', {
            cluster: 'eu',
        });

        const channel = pusher.subscribe('my-channel');
        const self = this; // Store a reference to the component context

        channel.bind('my-event', function (data) {
            self.messages.push(JSON.stringify(data));
            notify({
                group: 'success',
                title: 'Success',
                text: 'Generation is complete!',
            }, 4000);

            // Emit a custom event using Vue 2's $emit
            self.$emit('update:generationDone', 'done');
        });
    },
};
</script>


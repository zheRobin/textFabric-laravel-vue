<script setup>
import {computed, ref} from 'vue';
import {useForm, usePage} from '@inertiajs/vue3';
import ActionMessage from 'Jetstream/Components/ActionMessage.vue';
import ActionSection from 'Jetstream/Components/ActionSection.vue';
import Checkbox from 'Jetstream/Components/Checkbox.vue';
import ConfirmationModal from 'Jetstream/Components/ConfirmationModal.vue';
import DangerButton from 'Jetstream/Components/DangerButton.vue';
import ApiModal from 'Jetstream/Components/ApiModal.vue';
import DialogModal from 'Jetstream/Components/DialogModal.vue';
import FormSection from 'Jetstream/Components/FormSection.vue';
import InputError from 'Jetstream/Components/InputError.vue';
import InputLabel from 'Jetstream/Components/InputLabel.vue';
import PrimaryButton from 'Jetstream/Components/PrimaryButton.vue';
import SecondaryButton from 'Jetstream/Components/SecondaryButton.vue';
import SectionBorder from 'Jetstream/Components/SectionBorder.vue';
import TextInput from 'Jetstream/Components/TextInput.vue';
import {InformationCircleIcon} from "@heroicons/vue/24/outline";
import VueJsonPretty from 'vue-json-pretty';
import 'vue-json-pretty/lib/styles.css';

const props = defineProps({
    tokens: Array,
    availablePermissions: Array,
    defaultPermissions: Array,
    apiDocumentations: Object,
    translateDocumentation: Object
});

const documentationSelect = ref(null);
const selectExampleRequest = ref(null);
const responseExample = ref(null);
const exampleRequest = {
    translate: {
        "text":"Text für die Übersetzung",
        "translate-target-list": [
            "UK",
            "EN-US",
            "FR"
        ]
    },
    translateResponse:{
        "UK": "Текст для перекладу",
        "EN-US": "Text for translation",
        "FR": "Texte à traduire"
    },
    generate: {
        "preset-id": 5,
        "source-list":
            {
                "@test1": 'test',
                "@test2": 'test'
            },
        "translate-target-list": [
            "UK",
            "EN-US",
            "FR",
            "ES"
        ]
    },
    generateResponse: {
        "output": "Ein Signifikanztest ist ein statistischer Test, mit dem die Signifikanz einer Prädiktor- oder unabhängigen Variable bei der Erklärung der Variabilität einer abhängigen Variable in einem statistischen Modell ermittelt wird. Der Hauptzweck eines Signifikanztests besteht darin, die Variablen zu ermitteln, die für die Vorhersage der Ergebnisvariablen am wichtigsten sind. Es gibt mehrere Methoden zur Durchführung eines Signifikanztests, darunter der F-Test, der t-Test und Methoden zur Variablenauswahl wie die schrittweise Regression. Der F-Test wird in der Regel verwendet, um die Anpassung zwischen dem vollständigen Modell (mit allen Prädiktoren) und dem reduzierten Modell (ohne den interessierenden Prädiktor) zu vergleichen. Ist die F-Statistik signifikant, bedeutet dies, dass der Prädiktor für die Erklärung der Variation der Ergebnisvariablen wichtig ist. Der t-Test ist eine einfachere Alternative zum F-Test, der verwendet wird, wenn es nur eine Prädiktorvariable im Modell gibt. Die Signifikanz des t-Wertes bestimmt, ob die Prädiktorvariable wichtig ist oder nicht. Die Methoden der Variablenauswahl sind komplexer und beinhalten die Auswahl einer Teilmenge von Variablen, die die Variation der Ergebnisvariablen am besten erklären. Zu diesen Methoden gehören die Vorwärtsselektion, die Rückwärtselimination und die schrittweise Regression. Ein Signifikanztest ist für die Bestimmung der wichtigsten Variablen in einem statistischen Modell unerlässlich, da er es den Forschern ermöglicht, sich auf die Variablen zu konzentrieren, die den größten Einfluss auf die Ergebnisvariable haben. Dies gewährleistet eine bessere Modellgenauigkeit, aussagekräftige Ergebnisse und eine effektive Entscheidungsfindung.",
        "UK": "Тест на значущість - це статистичний тест, який використовується для визначення значущості предиктора або незалежної змінної для пояснення мінливості залежної змінної в статистичній моделі. Основна мета проведення тесту на значущість - визначити змінні, які є найбільш важливими для прогнозування результативної змінної.\n\nІснує кілька методів проведення тесту на значущість, включаючи F-тест, t-тест і методи відбору змінних, такі як покрокова регресія. F-тест зазвичай використовується для порівняння відповідності між повною моделлю (з усіма предикторами) і скороченою моделлю (без предиктора, який нас цікавить). Якщо F-статистика є значущою, це означає, що предиктор є важливим для пояснення варіації результативної змінної.\n\nT-тест є простішою альтернативою F-тесту, який використовується, коли в моделі є лише одна змінна-предиктор. Значущість t-значення визначає, чи є змінна предиктор важливою чи ні.\n\nМетоди відбору змінних є більш складними і передбачають вибір підмножини змінних, які найкраще пояснюють варіацію результативної змінної. Ці методи включають прямий відбір, зворотне виключення та покрокову регресію.\n\nТест на значущість має важливе значення для визначення найбільш релевантних змінних у статистичній моделі, оскільки він дозволяє дослідникам зосередитися на змінних, які мають найбільш суттєвий вплив на результуючу змінну. Це забезпечує кращу точність моделі, значущі результати та ефективне прийняття рішень.",
        "EN-US": "An importance test is a statistical test used to determine the significance of a predictor or independent variable in explaining the variability of a dependent variable in a statistical model. The main purpose of conducting an importance test is to identify variables that are most important in predicting the outcome variable.\n\nThere are several methods of conducting an importance test, including the F-test, t-test, and variable selection techniques like stepwise regression. The F-test is commonly used to compare the goodness of fit between a full model (with all predictors) to a reduced model (without the predictor of interest). If the F-statistic is significant, it indicates that the predictor is important in explaining the variation in the outcome variable.\n\nA t-test is a simpler alternative to the F-test, used when there is only one predictor variable in the model. The significance of the t-value determines whether the predictor variable is important or not.\n\nVariable selection techniques are more complex and involve selecting a subset of variables that best explain the variation in the outcome variable. These methods include forward selection, backward elimination, and stepwise regression.\n\nThe importance test is essential in identifying the most relevant variables in a statistical model, as it allows researchers to focus on the variables that have the most significant impact on the outcome variable. This allows for better model accuracy, significant results and effective decision making.",
        "FR": "Un test d'importance est un test statistique utilisé pour déterminer l'importance d'un prédicteur ou d'une variable indépendante pour expliquer la variabilité d'une variable dépendante dans un modèle statistique. L'objectif principal d'un test d'importance est d'identifier les variables les plus importantes pour prédire la variable de résultat.\n\nIl existe plusieurs méthodes pour réaliser un test d'importance, notamment le test F, le test t et les techniques de sélection de variables telles que la régression par étapes. Le test F est généralement utilisé pour comparer la qualité de l'ajustement entre un modèle complet (avec tous les prédicteurs) et un modèle réduit (sans le prédicteur d'intérêt). Si la statistique F est significative, cela indique que le prédicteur est important pour expliquer la variation de la variable de résultat.\n\nLe test t est une alternative plus simple au test F. Il est utilisé lorsque le modèle ne comporte qu'une seule variable prédictive. La signification de la valeur t détermine si la variable prédictive est importante ou non.\n\nLes techniques de sélection des variables sont plus complexes et impliquent la sélection d'un sous-ensemble de variables qui expliquent le mieux la variation de la variable de résultat. Ces méthodes comprennent la sélection en amont, l'élimination en aval et la régression par étapes.\n\nLe test d'importance est essentiel pour identifier les variables les plus pertinentes dans un modèle statistique, car il permet aux chercheurs de se concentrer sur les variables qui ont l'impact le plus significatif sur la variable de résultat. Cela permet d'améliorer la précision du modèle, d'obtenir des résultats significatifs et de prendre des décisions efficaces.",
        "ES": "Una prueba de importancia es una prueba estadística utilizada para determinar la importancia de un predictor o variable independiente a la hora de explicar la variabilidad de una variable dependiente en un modelo estadístico. El objetivo principal de realizar una prueba de importancia es identificar las variables más importantes para predecir la variable de resultado.\n\nExisten varios métodos para realizar una prueba de importancia, como la prueba F, la prueba t y técnicas de selección de variables como la regresión por pasos. La prueba F suele utilizarse para comparar la bondad del ajuste entre un modelo completo (con todos los predictores) y un modelo reducido (sin el predictor de interés). Si el estadístico F es significativo, indica que el predictor es importante para explicar la variación de la variable de resultado.\n\nLa prueba t es una alternativa más sencilla a la prueba F, que se utiliza cuando sólo hay una variable predictora en el modelo. La significación del valor t determina si la variable predictora es importante o no.\n\nLas técnicas de selección de variables son más complejas e implican la selección de un subconjunto de variables que expliquen mejor la variación de la variable de resultado. Estos métodos incluyen la selección hacia delante, la eliminación hacia atrás y la regresión por pasos.\n\nLa prueba de importancia es esencial para identificar las variables más relevantes en un modelo estadístico, ya que permite a los investigadores centrarse en las variables que tienen el impacto más significativo en la variable de resultado. Esto permite mejorar la precisión del modelo, obtener resultados significativos y tomar decisiones eficaces."
    },
}

const names = [
    {
        id: 1,
        url: `${location.origin}/api/rest/generate`,
    },
    {
        id: 2,
        url: `${location.origin}/api/rest/translate`
    }
];

const createApiTokenForm = useForm({
    name: '',
    permissions: props.defaultPermissions,
});

const updateApiTokenForm = useForm({
    permissions: [],
});

const deleteApiTokenForm = useForm({});

const displayingToken = ref(false);
const managingPermissionsFor = ref(null);
const apiTokenBeingDeleted = ref(null);

const showDocumentation = ref(false);

const isApiTokenCopied = ref(false);
const newApiToken = computed(() => usePage().props.jetstream.flash.token);
const copyApiTokenToClipboard = (e) => navigator.clipboard.writeText(newApiToken.value).then(() => {
    e.target.parentNode.classList.add('text-green-500');
    isApiTokenCopied.value = true;

    setTimeout(() => {
        e.target.parentNode.classList.remove('text-green-500');
        isApiTokenCopied.value = false;
    }, 2000);
});

const isTextCopied = ref(false);
const copyToClipboard = (e, text, ctcLabelId) => navigator.clipboard.writeText(text).then(() => {
    const parentNode = e.target.parentNode;
    const labelNode = parentNode.querySelector(`#${ctcLabelId}`);

    parentNode.classList.add('text-green-500');
    labelNode?.classList.remove('hidden');
    isTextCopied.value = true;

    setTimeout(() => {
        parentNode.classList.remove('text-green-500');
        labelNode?.classList.add('hidden');
        isTextCopied.value = false;
    }, 2000);
});

const createApiToken = () => {
    createApiTokenForm.post(route('api-tokens.store'), {
        preserveScroll: true,
        onSuccess: () => {
            displayingToken.value = true;
            createApiTokenForm.reset();
        },
    });
};

const documentation = id => {
    if(id === 1){
        documentationSelect.value = props.apiDocumentations.generate;
        selectExampleRequest.value = exampleRequest.generate;
        responseExample.value = exampleRequest.generateResponse;
    }else{
        documentationSelect.value = props.apiDocumentations.translate;
        selectExampleRequest.value = exampleRequest.translate;
        responseExample.value = exampleRequest.translateResponse;
    }
    showDocumentation.value = true;
}

const manageApiTokenPermissions = (token) => {
    updateApiTokenForm.permissions = token.abilities;
    managingPermissionsFor.value = token;
};

const updateApiToken = () => {
    updateApiTokenForm.put(route('api-tokens.update', managingPermissionsFor.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (managingPermissionsFor.value = null),
    });
};

const confirmApiTokenDeletion = (token) => {
    apiTokenBeingDeleted.value = token;
};

const deleteApiToken = () => {
    deleteApiTokenForm.delete(route('api-tokens.destroy', apiTokenBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (apiTokenBeingDeleted.value = null),
    });
};
const test = {...props.apiDocumentations.generate};
</script>

<template>
    <div>
        <!-- Generate API Token -->
        <FormSection @submitted="createApiToken">
            <template #title>
                Create API Token
            </template>

            <template #description>
                API tokens allow third-party services to authenticate with our application on your behalf.
            </template>

            <template #form>
                <!-- Token Name -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="name" value="Name" />
                    <TextInput
                        id="name"
                        v-model="createApiTokenForm.name"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                    <InputError :message="createApiTokenForm.errors.name" class="mt-2" />
                </div>

                <!-- Token Permissions -->
                <div v-if="availablePermissions.length > 0" class="col-span-6">
                    <InputLabel for="permissions" value="Permissions" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="permission in availablePermissions" :key="permission">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="createApiTokenForm.permissions" :value="permission" />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ permission }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <ActionMessage :on="createApiTokenForm.recentlySuccessful" class="mr-3">
                    Created.
                </ActionMessage>

                <PrimaryButton :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing">
                    Create
                </PrimaryButton>
            </template>
        </FormSection>

        <div v-if="tokens.length > 0">
            <SectionBorder />

            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <ActionSection>
                    <template #title>
                        Manage API Tokens
                    </template>

                    <template #description>
                        You may delete any of your existing tokens if they are no longer needed.
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div v-for="token in tokens" :key="token.id" class="flex items-center justify-between">
                                <div class="break-all dark:text-white">
                                    {{ token.name }}
                                </div>

                                <div class="flex items-center ml-2">
                                    <div v-if="token.last_used_ago" class="text-sm text-gray-400">
                                        Last used {{ token.last_used_ago }}
                                    </div>

                                    <button
                                        v-if="availablePermissions.length > 0"
                                        class="cursor-pointer ml-6 text-sm text-gray-400 underline"
                                        @click="manageApiTokenPermissions(token)"
                                    >
                                        Permissions
                                    </button>

                                    <button class="cursor-pointer ml-6 text-sm text-red-500" @click="confirmApiTokenDeletion(token)">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </ActionSection>
            </div>
            <SectionBorder />

            <div class="mt-10 sm:mt-0">
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="text-base font-semibold leading-6 text-gray-900">
                            API Documentation
                        </div>
                        <div class="text-sm text-gray-500">
                            <div class="mb-2">API Token must be passed as Bearer Token in the request header.</div>
                            <div class="my-4"><span class="border p-2 bg-gray-100 dark:bg-gray-900 rounded font-mono">Authorization: Bearer &lt;api-token&gt;</span></div>
                        </div>
                        <div class="mt-8 max-w-xl text-sm text-gray-500">
                            API Endpoints:
                        </div>
                        <div class="relative flex items-center border mt-2 p-2 rounded bg-gray-100" v-for="item in names" :key="item.id">
                            <div class="flex items-center text-sm font-mono gap-x-2 pr-10">
                                <span class="font-semibold text-orange-500 break-inside-avoid">POST</span>
                                <div class="break-all">{{item.url}}</div>
                                <button @click="documentation(item.id)" class="w-5 ml-1 inline-flex items-center justify-center">
                                    <InformationCircleIcon />
                                </button>
                            </div>
                            <span class="absolute flex items-center gap-x-2 hover:cursor-pointer" style="right: 1rem;" @click="(e) => copyToClipboard(e, item.url, `ctc-label-${item.id}`)">
                                <span :id="`ctc-label-${item.id}`" class="text-sm hidden">Copied</span>
                                <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" fill="currentColor">
                                    <path d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Token Value Modal -->
        <DialogModal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                API Token
            </template>

            <template #content>
                <div>
                    Please copy your new API token. For your security, it won't be shown again.
                </div>

                <div v-if="newApiToken" class="relative flex items-center justify-between my-4 bg-gray-100 dark:bg-gray-900 pl-4 pr-10 py-2 rounded font-mono text-sm text-gray-500 break-all">
                    <span>{{ newApiToken }}</span>
                    <span class="absolute hover:cursor-pointer" style="right: 1rem;" @click="copyApiTokenToClipboard">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512" fill="currentColor">
                            <path d="M384 336H192c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16l140.1 0L400 115.9V320c0 8.8-7.2 16-16 16zM192 384H384c35.3 0 64-28.7 64-64V115.9c0-12.7-5.1-24.9-14.1-33.9L366.1 14.1c-9-9-21.2-14.1-33.9-14.1H192c-35.3 0-64 28.7-64 64V320c0 35.3 28.7 64 64 64zM64 128c-35.3 0-64 28.7-64 64V448c0 35.3 28.7 64 64 64H256c35.3 0 64-28.7 64-64V416H272v32c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192c0-8.8 7.2-16 16-16H96V128H64z"/>
                        </svg>
                    </span>
                </div>

                <Transition leave-active-class="transition ease-in duration-400" leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="isApiTokenCopied" class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512" fill="currentColor" class="inline m-1 text-green-500">
                            <path d="M256 48a208 208 0 1 1 0 416 208 208 0 1 1 0-416zm0 464A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM369 209c9.4-9.4 9.4-24.6 0-33.9s-24.6-9.4-33.9 0l-111 111-47-47c-9.4-9.4-24.6-9.4-33.9 0s-9.4 24.6 0 33.9l64 64c9.4 9.4 24.6 9.4 33.9 0L369 209z"/>
                        </svg>
                        <span>API Token copied to clipboard!</span>
                    </div>
                </Transition>
            </template>

            <template #footer>
                <SecondaryButton @click="displayingToken = false">
                    Close
                </SecondaryButton>
            </template>
        </DialogModal>

        <!-- Token Value Modal -->
        <ApiModal :show="showDocumentation" @close="showDocumentation = false">
            <template #title>
            </template>

            <template #content>
                <vue-json-pretty class="mb-4" :data="documentationSelect" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Example JSON</h3>
                <vue-json-pretty class="mt-4 mb-4" :data="selectExampleRequest" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Example Response</h3>
                <vue-json-pretty class="mt-4" :data="responseExample" />
            </template>

            <template #footer>
                <SecondaryButton @click="showDocumentation = false">
                    Close
                </SecondaryButton>
            </template>
        </ApiModal>

        <!-- API Token Permissions Modal -->
        <DialogModal :show="managingPermissionsFor != null" @close="managingPermissionsFor = null">
            <template #title>
                API Token Permissions
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="permission in availablePermissions" :key="permission">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="updateApiTokenForm.permissions" :value="permission" />
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ permission }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="managingPermissionsFor = null">
                    Cancel
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': updateApiTokenForm.processing }"
                    :disabled="updateApiTokenForm.processing"
                    @click="updateApiToken"
                >
                    Save
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Token Confirmation Modal -->
        <ConfirmationModal :show="apiTokenBeingDeleted != null" @close="apiTokenBeingDeleted = null">
            <template #title>
                Delete API Token
            </template>

            <template #content>
                Are you sure you would like to delete this API token?
            </template>

            <template #footer>
                <SecondaryButton @click="apiTokenBeingDeleted = null">
                    Cancel
                </SecondaryButton>

                <DangerButton
                    class="ml-3"
                    :class="{ 'opacity-25': deleteApiTokenForm.processing }"
                    :disabled="deleteApiTokenForm.processing"
                    @click="deleteApiToken"
                >
                    Delete
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>

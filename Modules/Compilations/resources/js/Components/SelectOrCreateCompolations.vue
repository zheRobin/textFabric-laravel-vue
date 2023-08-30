<script setup>
import {
    ArrowDownTrayIcon,
    MinusCircleIcon,
    PencilSquareIcon,
    PlusCircleIcon,
    XCircleIcon
} from "@heroicons/vue/20/solid";
import TextInput from "Jetstream/Components/TextInput.vue";
import SelectMenu from "Jetstream/Components/SelectMenu.vue";
import DangerButton from "Jetstream/Components/DangerButton.vue";
import RenamePreset from "Modules/OpenAI/resources/js/Components/RenamePreset.vue";
import DeletePreset from "./DeletePreset.vue";
import PrimaryButton from "Jetstream/Components/PrimaryButton.vue";
import SecondaryButton from "Jetstream/Components/SecondaryButton.vue";

import {ref, watch} from "vue";
import {notify} from "notiwind";
import {useForm, usePage} from "@inertiajs/vue3";
import { onMounted } from 'vue';
const props = defineProps({
    complications: Array,
    positions: Array,
    canManageCompilations: Boolean,
});

const initSelectedCompilation = () => {
    const preset = localStorage.getItem('selected-compilations');

    if (preset) {
        changePreset(Number.parseInt(preset));
    }
}

const form = useForm(
    {
        id: null,
        name: null,
        owner: null,
        preset_ids: [],
    }
)
console.log('selceted', localStorage.getItem('selected-compilations'))
const selectedPreset = ref(null);
const addingPreset = ref(!props.complications.length);

const cancelPreset = () => {
    addingPreset.value = false;
    changePreset(selectedPreset.value);
}
const getPreset = (presetId) => {
    if (!presetId) {
        return null;
    }

    return props.complications.find((el) => el.id === presetId);
}

const fillPresetForm = (preset) => {
    Object.getOwnPropertyNames(preset).forEach((property) => {
        if (form.hasOwnProperty(property)) {
            if(property === 'preset_ids'){
                form[property] = preset[property];
            }else{
                form[property] = preset[property];
            }
        }
    });
}

const addPreset = () => {
    addingPreset.value = true;
    form.defaults({
        name: null,
        owner: null,
        preset_ids: [],
    });
    form.reset();
}

const presetOptions = () => {
    const presets = [];

    props.complications.forEach((el) => {
        presets.push({value: el.id,label: el.name});
    })
    return presets;
}

const newItems = (items) => {
}
const emit = defineEmits(['selectedPreset']);
const changePreset = (value) => {
    console.log(value)
    selectedPreset.value = value;
    localStorage.setItem('selected-compilations', value);
    addingPreset.value = false;
    const preset = getPreset(value);
    emit('selectedPreset', selectedPreset.value);
    if (preset) {
        fillPresetForm(preset);
    }
}

const renamePreset = (value) => {
    form.name = value;
    updatePreset();
}


const savePreset = (status) => {
    console.log(addingPreset.value);
    if(status === 'create'){
        addingPreset.value = true;
    }
    if (addingPreset.value) {
        createPreset();
    } else if (selectedPreset.value) {
        updatePreset();
    }
}

const createPreset = () => {
    console.log(emit(''));
    const newPresetIds = [];
    form.preset_ids = newPresetIds;
    form.patch(route('compilations.store'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: (res) => {
            console.log(res.props.complications[res.props.complications.length - 1].id);
            changePreset(res.props.complications[res.props.complications.length - 1].id);
            notify({
                group: "success",
                title: "Success",
                text: "Compilation created!"
            }, 4000)
        },
        onError: (error) => {
            notify({
                group: "error",
                title: "Error",
                text: error[Object.keys(error)[0]] ?? "Something wrong happens."
            }, 4000)
        }
    })
}

const change = (e) => {
    console.log(e);
}

const deletePreset = () => {
    form.delete(route('compilations.delete'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
            emit('onDelete', true)
            selectedPreset.value = null;
            notify({
                group: "success",
                title: "Success",
                text: "Compilation deleted!"
            }, 4000)
            localStorage.removeItem('selected-compilations');
            form.name = null;
        },
    })
}

const updatePreset = () => {
    console.log(props.positions)
    const newPresetIds = [];
    if(props.positions){
        props.positions.map(item => newPresetIds.push(item.id))
        form.preset_ids = newPresetIds;
    }
    console.log(form.preset_ids);
    form.patch(route('compilations.update'), {
        errorBag: 'errors',
        preserveScroll: true,
        onSuccess: () => {
            notify({
                group: "success",
                title: "Success",
                text: "Compilation updated!"
            }, 4000)
        },
        onError: (error) => {
            notify({
                group: "error",
                title: "Error",
                text: error[Object.keys(error)[0]] ?? "Something wrong happens."
            }, 4000) // 4s
        }
    })
}

initSelectedCompilation();
</script>

<template>
    <div class="flex border-b border-gray-200 pb-8 items-center">
        <div class="items-center flex flex-1">
            <template v-if="!complications.length || addingPreset">
                <template v-if="canManageCompilations">
                    <label class="mr-2 font-medium">{{$t('Name:')}}</label>
                    <TextInput v-model="form.name" type="text" class="w-60"/>
                    <PrimaryButton @click="savePreset('create')" class="ml-2 gap-x-1.5">
                        {{$t('Save')}}
                        <ArrowDownTrayIcon class="-mr-0.5 w-4" aria-hidden="true" />
                    </PrimaryButton>
                    <SecondaryButton v-if="complications.length" class="ml-2 gap-x-1.5" @click="cancelPreset">
                        {{$t('Cancel')}}
                        <XCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                    </SecondaryButton>
                </template>
            </template>

            <template v-else>
                <label class="mr-2 font-medium dark:text-white">{{$t('Compilation')}}:</label>
                <SelectMenu @update:modelValue="changePreset" class="w-60" v-model="selectedPreset" :options="presetOptions()" :placeholder="$t('Select')" />
                <template v-if="canManageCompilations">
                    <PrimaryButton @click="addPreset" class="ml-2 gap-x-1.5">
                        {{$t('Add')}}
                        <PlusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                    </PrimaryButton>
                    <RenamePreset v-if="selectedPreset" :name="form.name" @rename="renamePreset">
                        <PrimaryButton class="ml-2 gap-x-1.5">
                            {{$t('Rename')}}
                            <PencilSquareIcon  class="-mr-0.5 w-4" aria-hidden="true" />
                        </PrimaryButton>
                    </RenamePreset>
                    <DeletePreset @delete="deletePreset" v-if="selectedPreset" :name="form.name">
                        <DangerButton class="ml-2 gap-x-1.5">
                            {{$t('Delete')}}
                            <MinusCircleIcon class="-mr-0.5 w-4" aria-hidden="true" />
                        </DangerButton>
                    </DeletePreset>
                </template>
            </template>
        </div>
    </div>
</template>

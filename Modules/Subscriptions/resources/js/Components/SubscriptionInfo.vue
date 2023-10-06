<script setup>
import {CheckIcon} from "@heroicons/vue/20/solid";
import PrimaryBadge from "Jetstream/Components/PrimaryBadge.vue";
import InputLabel from "Jetstream/Components/InputLabel.vue";
import DangerBadge from "Jetstream/Components/DangerBadge.vue";
import { toLocaleDate } from "Modules/Subscriptions/resources/js/subscriptions";
import {usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    planSubscription: Object,
    collectionCount: Number,
});

const page = usePage();
const locale = computed(() => page.props.localeAll[page.props.locale].regional.replace('_', '-'));

const getUsage = (feature) => {
    return props.planSubscription.usage.find((el) => el.feature_id === feature.id);
}

const getFeatureUsage = (feature) => {
    const usage = getUsage(feature);

    return usage ? usage.used : 0;
}
</script>

<template>
    <div>
        <InputLabel>{{$t('Status')}}</InputLabel>
        <PrimaryBadge v-if="planSubscription.is_active" class="mt-1">{{$t("Active")}}</PrimaryBadge>
        <DangerBadge v-else>{{$t('Expired')}}</DangerBadge>

        <InputLabel class="mt-5">{{$t('Plan Type')}}</InputLabel>
        <p class="flex-1 mt-1 text-base uppercase font-semibold">
            <span v-if="planSubscription.on_trial" class="text-gray-400 capitalize">({{$t('Trial')}})</span>
            {{ ' ' }}
            <span class="text-tf-blue-800">{{ planSubscription.plan.name }}</span>
        </p>

        <InputLabel class="mt-5">{{$t('Plan Features')}}</InputLabel>
        <ul role="list" class="mt-1 space-y-3 text-sm leading-6 text-gray-600">
            <template v-for="(feature, key) in planSubscription.plan.features">
                <li v-if="feature.slug !== 'openai-requests' && feature.slug !== 'deepl-requests'" class="flex gap-x-3" :key="feature.id">
                    <CheckIcon class="h-6 w-5 flex-none text-tf-blue-600" aria-hidden="true" />
                    <span>{{ $t(feature.description) }}</span>
                    {{ ' ' }}
                    <template v-if="feature.slug !== 'collection-items-limit' && feature.slug !== 'openai-params'" >
                        <span v-if="feature.slug === 'collections-limit'" class="font-semibold text-gray-500">
                            {{ `(${$t('used')}: ${props.collectionCount})` }}
                        </span>
                        <span v-else class="font-semibold text-gray-500">
                            {{ `(${$t('used')}: ${getFeatureUsage(feature)})` }}
                        </span>
                    </template>
                </li>
            </template>
        </ul>

        <InputLabel class="mt-5">{{ planSubscription.is_active ? $t('Ends at') : $t('Ended at') }}</InputLabel>
        <time class="flex mt-q mt-1 text-gray-800 text-sm" :datetime="planSubscription.ends_at">
            {{ toLocaleDate(planSubscription.ends_at, 'de-DE', {year: 'numeric', month: '2-digit', day: '2-digit'}) }}
        </time>
    </div>
</template>

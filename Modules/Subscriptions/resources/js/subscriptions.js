import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

export const subscriptionContactLink = 'https://www.texthub.io/kontakt';

export function daysLeft(date) {
    const total = Date.parse(date) - Date.parse(new Date().toISOString());

    return  Math.floor(total/(1000*60*60*24));
}

export function toLocaleDate(date, locale, options) {
    return new Date(date).toLocaleDateString(locale, options);
}

export function hasPlanLimit(planSubscription) {
    const currentPlanFeatures = computed(() => {
        return {
            openai: planSubscription.plan.features.find(feature => feature.slug === 'openai-requests'),
            deepl: planSubscription.plan.features.find(feature => feature.slug === 'deepl-requests'),
            api: planSubscription.plan.features.find(feature => feature.slug === 'api-requests'),
        };
    });

    const currentPlanUsage =  computed(() => {
        return {
            openai: planSubscription.usage.find(usage => usage.feature_id === currentPlanFeatures.value.openai.id),
            deepl: planSubscription.usage.find(usage => usage.feature_id === currentPlanFeatures.value.deepl.id),
            api: planSubscription.usage.find(usage => usage.feature_id === currentPlanFeatures.value.api.id),
        };
    });

    const hasPlanLimit =  computed(() => {
        return {
            openai: parseInt(currentPlanFeatures.value.openai.value) > parseInt(currentPlanUsage.value.openai?.used || 0),
            deepl: parseInt(currentPlanFeatures.value.deepl.value) > parseInt(currentPlanUsage.value.deepl?.used || 0),
            api: parseInt(currentPlanFeatures.value.api.value) > parseInt(currentPlanUsage.value.api?.used || 0),
        };
    });

    return hasPlanLimit.value;
}

<template>
    <Head title="Dashboard"/>

    <BreezeAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                    My Active Submissions
                </h2>
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <div class="sm:col-span-2">
                            <div class="mt-1 text-sm text-gray-900">
                                <ul v-if="activeSubmissions" role="list"
                                    class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                    <li v-for="submission in activeSubmissions"
                                        class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                        <div class="w-0 flex-1 flex items-center">
                                            <DocumentTextIcon
                                                class="flex-shrink-0 h-5 w-5 text-gray-400"
                                                aria-hidden="true"
                                            />
                                            <span class="ml-2 flex-1 w-0 truncate"> {{ submission.submission }} </span>
                                        </div>
                                        <div class="ml-4 flex-shrink-0">
                                            <Link
                                                :href="route('bounty.show', submission.bounty.id)"
                                                class="font-medium text-indigo-600 hover:text-indigo-500"
                                            >#{{ submission.bounty.id }}</Link>
                                        </div>
                                    </li>
                                </ul>
                                <span v-else>No Submissions Yet</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="wonBounties" class="pt-5">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight mb-2">
                        Won Bounties
                    </h2>
                    <BountyGrid
                        :bounties="wonBounties"
                    ></BountyGrid>
                </div>
            </div>
        </div>

    </BreezeAuthenticatedLayout>
</template>

<script>
import BreezeAuthenticatedLayout from '@/Layouts/Authenticated.vue';
import {Head, Link} from '@inertiajs/inertia-vue3';
import Button from '@/Components/Button';
import { DocumentTextIcon } from '@heroicons/vue/solid'
import BountyGrid from "@/Components/BountyGrid";

export default {
    name: 'Dashboard',
    props: {
        activeSubmissions: { type: Object, default: () => {}},
        wonBounties: { type: Object, default: () => {}}
    },
    components: {
        BountyGrid,
        BreezeAuthenticatedLayout,
        Head,
        Button,
        Link,
        DocumentTextIcon
    }
}
</script>
